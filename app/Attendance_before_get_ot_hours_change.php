<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Attendance extends Model
{
    protected $guarded = [];
    protected $fillable = ['emp_id', 'uid', 'state', 'timestamp', 'date',
        'approved', 'type', 'devicesno', 'location', 'created_at', 'updated_at'];

    use softDeletes;

    public function get_work_days($emp_id, $month)
    {
        $query = "SELECT Max(at1.timestamp) as lasttimestamp,
        Min(at1.timestamp) as firsttimestamp
        FROM attendances as at1
        WHERE at1.emp_id = $emp_id
        AND at1.date LIKE '$month%'
        AND at1.deleted_at IS NULL
        group by at1.uid, at1.date
        ";
        $attendance = \DB::select($query);

        $work_days = 0;
        foreach ($attendance as $att) {
            $first_time = $att->firsttimestamp;
            $last_time = $att->lasttimestamp;

            //get difference in hours
            $diff = round((strtotime($last_time) - strtotime($first_time)) / 3600, 1);

            //if diff is greater than 8 hours then it is a work day
            //if diff is greater than 4 hours then it is a half day
            //if diff is greater than 2 hours then it is a half day
            if ($diff >= 8) {
                $work_days++;
            } elseif ($diff >= 4) {
                $work_days += 0.5;
            } elseif ($diff >= 2){
                $work_days += 0.25;
            }
        }
        return $work_days;
    }

    public function get_leave_days($emp_id, $month)
    {
        $query = DB::table('leaves')
            ->select(DB::raw('SUM(no_of_days) as total'))
            ->where('emp_id', $emp_id )
            ->where('leave_from', 'like',  $month . '%');
        $leave_days_data = $query->get();
        $leave_days = (!empty($leave_days_data[0]->total)) ? $leave_days_data[0]->total : 0;

        return $leave_days;
    }

    public function get_no_pay_days($emp_id, $month){
        $query = DB::table('leaves')
            ->select(DB::raw('SUM(no_of_days) as total'))
            ->where('emp_id', $emp_id )
            ->where('leave_from', 'like',  $month . '%')
            ->where('leave_type', '=', '3');

        $no_pay_days_data = $query->get();
        $no_pay_days = (!empty($no_pay_days_data[0]->total)) ? $no_pay_days_data[0]->total : 0;

        return $no_pay_days;
    }

    public function get_ot_hours($emp_id, $month){

        $month = $month . '%';

        $normal_rate_otwork_hrs = 0;
        $double_rate_otwork_hrs = 0;

        $att_query = 'SELECT at1.*, 
                Max(at1.timestamp) as lasttimestamp,
                Min(at1.timestamp) as firsttimestamp,
                employees.emp_shift,  
                employees.id as emp_auto_id,
                employees.emp_name_with_initial,
                employees.emp_department,
                shift_types.onduty_time, 
                shift_types.offduty_time
                FROM `attendances`  as `at1`
                join `employees` on `employees`.`emp_id` = `at1`.`uid` 
                left join shift_types ON employees.emp_shift = shift_types.id 
                WHERE at1.emp_id = '.$emp_id.' AND date LIKE  "'.$month.'"
                AND at1.deleted_at IS NULL
                 group by at1.uid, at1.date
                ';
        $att_records = DB::select($att_query);

        foreach ($att_records as $att_record) {

            $off_time = $att_record->lasttimestamp;
            $on_time = $att_record->firsttimestamp;
            $record_date = $att_record->date;

            $on_duty_time = $att_record->onduty_time;
            $off_duty_time =  $att_record->offduty_time;

            $ot_hours = (new \App\Attendance)->get_ot_hours_by_date($att_record->uid, $off_time, $on_time, $record_date, $on_duty_time, $off_duty_time, $att_record->emp_department);

            $normal_rate_otwork_hrs += $ot_hours['normal_rate_otwork_hrs'];
            $double_rate_otwork_hrs += $ot_hours['double_rate_otwork_hrs'];
        }

        $data = array(
            'normal_rate_otwork_hrs' => $normal_rate_otwork_hrs,
            'double_rate_otwork_hrs' => $double_rate_otwork_hrs,
        );

        return $data;

    }

    public function get_ot_hours_by_date($emp_id, $off_time, $on_time, $record_date, $on_duty_time, $off_duty_time, $emp_department ){

        if($on_duty_time == ''){
            $data = array(
                'normal_rate_otwork_hrs' => 0,
                'double_rate_otwork_hrs' => 0,
            );

            return $data;
        }

        $off_time = Carbon::parse($off_time);
        $on_time = Carbon::parse($on_time);
        $record_date = Carbon::parse($record_date);

        $shift_start = $on_duty_time;
        $shift_end =  $off_duty_time;

        $date_period = $off_time->diffInDays($on_time);

        $total_ot_hours = 0;
        $total_ot_hours_double = 0;
        $ot_breakdown = array();

        $department = Department::where('id', $emp_department)->first();

        if(empty($department)){
            $data = array(
                'normal_rate_otwork_hrs' => 0,
                'double_rate_otwork_hrs' => 0,
                'ot_breakdown' => $ot_breakdown,
                'info' => 'Department not found',
            );

            return $data;
        }

        if($department->name != 'WORKSHOP' && $department->name != 'DRIVERS'){

            $data = array(
                'normal_rate_otwork_hrs' => 0,
                'double_rate_otwork_hrs' => 0,
                'ot_breakdown' => $ot_breakdown,
                'info' => 'Department is not workshop or driver',
            );

            return $data;
        }

        $emp = DB::table('employees')
            ->select('emp_shift', 'emp_etfno', 'emp_name_with_initial')

            ->where('emp_id', $emp_id)
            ->first();

        $shift = DB::table('shift_types')
            ->where('id', $emp->emp_shift)
            ->first();


        for($i = 0; $i <= $date_period; $i++){

            $date = $record_date->copy()->addDays($i);
            $day = $date->dayOfWeek;

            if($day == 6){

                $saturday_on_duty_time = $shift->saturday_onduty_time;
                $saturday_off_duty_time = $shift->saturday_offduty_time;

                $shift_start = $saturday_on_duty_time;
                $shift_end = $saturday_off_duty_time;

            }

            $ot_ends_morning =  Carbon::parse($date->year.'-'.$date->month.'-'.$date->day.' '.$shift_start);
            $ot_starts_evening = Carbon::parse($date->year.'-'.$date->month.'-'.$date->day.' '.$shift_end);

            //date format is YYYY-MM-DD
            $s_date = $date->format('Y-m-d');

            if($i == 0){
                //1st day
                if($on_time < $ot_ends_morning){

                    //dd('1st day ' .$on_time);

                    $ot_in_minutes_morning = $on_time->diffInMinutes($ot_ends_morning);
                    //get difference in hours and minutes
                    //dd($ot_in_minutes_morning/60);

                    if($ot_in_minutes_morning >= 60){
                        $holiday_check = Holiday::where('date', $s_date)
                            ->where('work_level', '=', '2')
                            ->first();

                        $double_ot_hours_morning = 0;
                        $ot_hours_morning = 0;
                        $is_holiday = false;

                        if($holiday_check){

                            $double_ot_hours_morning = round($ot_in_minutes_morning / 60, 2);
                            $total_ot_hours_double += $double_ot_hours_morning;
                            $is_holiday = true;
  
                        }else{
                            $ot_hours_morning = round($ot_in_minutes_morning / 60, 2);
                            $total_ot_hours += $ot_hours_morning;
                        }

                        $ob = array(
                            'etf_no' => $emp->emp_etfno,
                            'name' => $emp->emp_name_with_initial,
                            'date' => $s_date,
                            'day_name' => $date->format('l'),
                            'from' => $on_time->format('Y-m-d h:i A'),
                            'from_24' => $on_time->format('Y-m-d H:i'),
                            'from_rfc' => $on_time->format('Y-m-d\TH:i:s'),
                            'to' => $ot_ends_morning->format('Y-m-d h:i A'),
                            'to_24' => $ot_ends_morning->format('Y-m-d H:i'),
                            'to_rfc' => $ot_ends_morning->format('Y-m-d\TH:i:s'),
                            'hours' => $ot_hours_morning,
                            'double_hours' => $double_ot_hours_morning,
                            'is_holiday' => $is_holiday,
                        );

                        array_push($ot_breakdown, $ob);

                    }

                }

                //evening
                if($date_period == 0){
                    if($off_time > $ot_starts_evening  ) {
                        $ot_in_minutes_evening = $off_time->diffInMinutes($ot_starts_evening);

                        if($ot_in_minutes_evening >= 60){
                            $holiday_check = Holiday::where('date', $date->year.'-'.$date->month.'-'.$date->day )
                                ->where('work_level', '=', '2')
                                ->first();

                            $double_ot_hours_evening = 0;
                            $is_holiday = false;
                            $ot_hours_evening = 0;

                            if($holiday_check){
                                $double_ot_hours_evening = round($ot_in_minutes_evening / 60, 2);
                                $total_ot_hours_double += $double_ot_hours_evening;
                                $is_holiday = true;
                            }else{
                                $ot_hours_evening = round($ot_in_minutes_evening / 60, 2);
                                $total_ot_hours += $ot_hours_evening;
                            }

                            //var_dump( '1st day evening '.$off_time. ' to '. $ot_starts_evening .': '.$ot_hours_evening.'<br>');

                            $ob = array(
                                'etf_no' => $emp->emp_etfno,
                                'name' => $emp->emp_name_with_initial,
                                'date' => $s_date,
                                'day_name' => $date->format('l'),
                                'from' => $ot_starts_evening->format('Y-m-d h:i A'),
                                'from_24' => $ot_starts_evening->format('Y-m-d H:i'),
                                'from_rfc' => $ot_starts_evening->format('Y-m-d\TH:i:s'),
                                'to' => $off_time->format('Y-m-d h:i A'),
                                'to_24' => $off_time->format('Y-m-d H:i'),
                                'to_rfc' => $off_time->format('Y-m-d\TH:i:s'),
                                'hours' => $ot_hours_evening,
                                'double_hours' => $double_ot_hours_evening,
                                'is_holiday' => $is_holiday,
                            );

                            array_push($ot_breakdown, $ob);
                            //var_dump($ob);
                            //die();

                        }

                    }
                }else{

                    if ($off_time > $ot_ends_morning) {
                        //next day ot ends morning
                        $next_date = $date->copy()->addDays(1);
                        $next_date = $next_date->format('Y-m-d');
                        $next_date_morning = Carbon::parse($next_date.' '.$shift_start);

                        $ot_in_minutes_evening = $ot_starts_evening->diffInMinutes($next_date_morning);

                        if($ot_in_minutes_evening >= 60){
                            $holiday_check = Holiday::where('date', $date->year.'-'.$date->month.'-'.$date->day )
                                ->where('work_level', '=', '2')
                                ->first();

                            $double_ot_hours_evening = 0;
                            $is_holiday = false;
                            $ot_hours_evening = 0;

                            if($holiday_check){
                                $double_ot_hours_evening = round($ot_in_minutes_evening / 60, 2);
                                $total_ot_hours_double += $double_ot_hours_evening;
                                $is_holiday = true;
                            }else{
                                $ot_hours_evening = round($ot_in_minutes_evening / 60, 2);
                                $total_ot_hours += $ot_hours_evening;
                            }

                            //var_dump( '1st day evening '.$ot_starts_evening. ' to '. $next_date_morning .': '.$ot_hours_evening.'<br>');

                            $ob = array(
                                'etf_no' => $emp->emp_etfno,
                                'name' => $emp->emp_name_with_initial,
                                'date' => $s_date,
                                'day_name' => $date->format('l'),
                                'from' => $ot_starts_evening->format('Y-m-d h:i A'),
                                'from_24' => $ot_starts_evening->format('Y-m-d H:i'),
                                'from_rfc' => $ot_starts_evening->format('Y-m-d\TH:i:s'),
                                'to' => $next_date_morning->format('Y-m-d h:i A'),
                                'to_24' => $next_date_morning->format('Y-m-d H:i'),
                                'to_rfc' => $next_date_morning->format('Y-m-d\TH:i:s'),
                                'hours' => $ot_hours_evening,
                                'double_hours' => $double_ot_hours_evening,
                                'is_holiday' => $is_holiday,
                            );

                            array_push($ot_breakdown, $ob);

                        }

                    }

                }


            }else{

                if ($off_time > $ot_ends_morning) {
                    //next day ot ends morning
                    $next_date = $date->copy()->addDays(1);
                    $next_date = $next_date->format('Y-m-d');
                    $next_date_morning = Carbon::parse($next_date.' '.$shift_start);

                    if($off_time > $next_date_morning){

                        $ot_in_minutes_evening = $ot_starts_evening->diffInMinutes($next_date_morning);

                        if($ot_in_minutes_evening >= 60){
                            $holiday_check = Holiday::where('date', $date->year.'-'.$date->month.'-'.$date->day )
                                ->where('work_level', '=', '2')
                                ->first();

                            $double_ot_hours_evening = 0;
                            $is_holiday = false;
                            $ot_hours_evening = 0;

                            if($holiday_check){
                                $double_ot_hours_evening = round($ot_in_minutes_evening / 60, 2);
                                $total_ot_hours_double += $double_ot_hours_evening;
                                $is_holiday = true;
                            } else {
                                $ot_hours_evening = round($ot_in_minutes_evening / 60, 2);
                                $total_ot_hours += $ot_hours_evening;
                            }

                            //var_dump( '2nd day evening '.$ot_starts_evening. ' to '. $next_date_morning .': '.$ot_hours_evening.'<br>');

                            $ob = array(
                                'etf_no' => $emp->emp_etfno,
                                'name' => $emp->emp_name_with_initial,
                                'date' => $s_date,
                                'day_name' => $date->format('l'),
                                'from' => $ot_starts_evening->format('Y-m-d h:i A'),
                                'from_24' => $ot_starts_evening->format('Y-m-d H:i'),
                                'from_rfc' => $ot_starts_evening->format('Y-m-d\TH:i:s'),
                                'to' => $next_date_morning->format('Y-m-d h:i A'),
                                'to_24' => $next_date_morning->format('Y-m-d H:i'),
                                'to_rfc' => $next_date_morning->format('Y-m-d\TH:i:s'),
                                'hours' => $ot_hours_evening,
                                'double_hours' => $double_ot_hours_evening,
                                'is_holiday' => $is_holiday,
                            );

                            array_push($ot_breakdown, $ob);
                        }

                    }
                }

                if($i == $date_period){
                    //second day evening
                    if($off_time > $ot_starts_evening) {
                        $ot_in_minutes_evening = $off_time->diffInMinutes($ot_starts_evening);

                        if($ot_in_minutes_evening >= 60){
                            $holiday_check = Holiday::where('date', $date->year.'-'.$date->month.'-'.$date->day )
                                ->where('work_level', '=', '2')
                                ->first();

                            $double_ot_hours_evening = 0;
                            $is_holiday = false;
                            $ot_hours_evening = 0;

                            if($holiday_check){
                                $double_ot_hours_evening = round($ot_in_minutes_evening / 60, 2);
                                $total_ot_hours_double += $double_ot_hours_evening;
                                $is_holiday = true;
                            } else {
                                $ot_hours_evening = round($ot_in_minutes_evening / 60, 2);
                                $total_ot_hours += $ot_hours_evening;
                            }

                            //var_dump( '2nd day evening '.$ot_starts_evening. ' to '. $off_time .': '.$ot_hours_evening.'<br>');

                            $ob = array(
                                'etf_no' => $emp->emp_etfno,
                                'name' => $emp->emp_name_with_initial,
                                'date' => $s_date,
                                'day_name' => $date->format('l'),
                                'from' => $ot_starts_evening->format('Y-m-d h:i A'),
                                'from_24' => $ot_starts_evening->format('Y-m-d H:i'),
                                'from_rfc' => $ot_starts_evening->format('Y-m-d\TH:i:s'),
                                'to' => $off_time->format('Y-m-d h:i A'),
                                'to_24' => $off_time->format('Y-m-d H:i'),
                                'to_rfc' => $off_time->format('Y-m-d\TH:i:s'),
                                'hours' => $ot_hours_evening,
                                'double_hours' => $double_ot_hours_evening,
                                'is_holiday' => $is_holiday,
                            );

                            array_push($ot_breakdown, $ob);
                        }

                    }
                }

            }

        }

        $data = array(
            'normal_rate_otwork_hrs' => $total_ot_hours,
            'double_rate_otwork_hrs' => $total_ot_hours_double,
            'ot_breakdown' => $ot_breakdown,
        );

        return $data;

    }

    public function get_ot_hours_by_date1($emp_id, $off_time, $on_time, $record_date){

        $emp = DB::table('employees')
            ->leftJoin('shift_types', 'employees.emp_shift', '=', 'shift_types.id')
            ->leftJoin('departments', 'employees.emp_department', '=', 'departments.id')
            ->where('emp_id', $emp_id)
            ->select('emp_shift',
                'emp_etfno',
                'emp_name_with_initial',
                'emp_department',
                'departments.name as department_name',
                'shift_types.onduty_time as shift_start_time',
                'shift_types.offduty_time as shift_end_time',
                'shift_types.saturday_onduty_time as saturday_shift_start_time',
                'shift_types.saturday_offduty_time as saturday_shift_end_time',
                'shift_types.shift_name'
            )
            ->first();

        $off_time = Carbon::parse($off_time);
        $on_time = Carbon::parse($on_time);
        $record_date = Carbon::parse($record_date);


        $shift_name = $emp->shift_name;
        $department_name = $emp->department_name;
        $shift_start_time = Carbon::parse($emp->shift_start_time);
        $shift_end_time = Carbon::parse($emp->shift_end_time);
        $saturday_shift_start_time = Carbon::parse($emp->saturday_shift_start_time);
        $saturday_shift_end_time = Carbon::parse($emp->saturday_shift_end_time);

        $total_ot_hours = 0;
        $total_ot_hours_double = 0;
        $ot_breakdown = array();
        $is_saturday = false;
        $is_holiday = false;

        if($department_name != 'WORKSHOP' && $department_name != 'DRIVERS'){
            $data = array(
                'normal_rate_otwork_hrs' => 0,
                'double_rate_otwork_hrs' => 0,
                'ot_breakdown' => $ot_breakdown,
                'message' => 'Department is not workshop or driver',
            );
            return $data;
        }

        $date_period = $off_time->diffInDays($on_time);

        for($i = 0; $i <= $date_period; $i++) {

            $date = $record_date->copy()->addDays($i);

            $day_name = $date->format('D');
            if($day_name == 'Sat') {
                $is_saturday = true;
                $shift_start_time = $saturday_shift_start_time;
                $shift_end_time = $saturday_shift_end_time;
            }

            $date = $date->format('Y-m-d');

            $ot_minutes = $on_time->diffInMinutes($off_time);

            //on_time, shift_start time
            if($on_time < $shift_start_time){
                $ot_in_minutes = $on_time->diffInMinutes($shift_start_time);
            } else {
                $ot_in_minutes = $on_time->diffInMinutes($off_time);
            }

            if($off_time > $shift_end_time){

            }





            $holiday_check = Holiday::where('date', $date)
                ->where('work_level', '=', '2')
                ->first();

            if($holiday_check){

                $is_holiday = true;
                $half_short = $holiday_check->half_short;

                if($half_short == 1.00 ){

                    $ot_hours = round($ot_minutes / 60, 2);
                    $total_ot_hours_double += $ot_hours;

                }elseif($half_short == 0.50) {



                }elseif($half_short == 0.25){

                }

            }else{
                $ot_hours = round($ot_minutes / 60, 2);
                $total_ot_hours += $ot_hours;
            }

        }

        $data = array(
            'normal_rate_otwork_hrs' => $total_ot_hours,
            'double_rate_otwork_hrs' => $total_ot_hours_double,
            'ot_breakdown' => $ot_breakdown,
        );

        var_dump($data);
        die();

        return $data;

    }

    public function get_attendance_details($emp_id, $month)
    {
        $month = $month.'%';

        $att_query = 'SELECT at1.*, 
                Max(at1.timestamp) as lasttimestamp,
                Min(at1.timestamp) as firsttimestamp,
                employees.emp_shift,  
                employees.id as emp_auto_id,
                employees.emp_name_with_initial,
                employees.emp_etfno,
                shift_types.onduty_time, 
                shift_types.offduty_time
                FROM `attendances`  as `at1`
                join `employees` on `employees`.`emp_id` = `at1`.`uid` 
                left join shift_types ON employees.emp_shift = shift_types.id 
                WHERE at1.emp_id = '.$emp_id.' AND date LIKE  "'.$month.'"
                AND at1.deleted_at IS NULL
                 group by at1.uid, at1.date
                 order by at1.date desc';

        $att_records = DB::select($att_query);

        $emp_data = array();

        foreach ($att_records as $att_record) {
             $att_date = Carbon::parse($att_record->date);
             $date = $att_date->year.'-'.$att_date->month.'-'.$att_date->day;

             $day = Carbon::parse($date)->dayOfWeek;
             $day_name = Carbon::parse($date)->format('l');

             //ot_calculation
            $normal_rate_otwork_hrs = 0;
            $double_rate_otwork_hrs = 0;

            $double_ot_hours = 0;

            $off_time = Carbon::parse($att_record->lasttimestamp);
            $on_time = Carbon::parse($att_record->firsttimestamp);
            $record_date = Carbon::parse($att_record->date);

            //difference between on and off time in minutes
            $work_minutes = $off_time->diffInMinutes($on_time);
            $work_hours = $work_minutes/60;

            $work_hours = round($work_hours, 2);

            $shift_start = $att_record->onduty_time;
            $shift_end =  $att_record->offduty_time;

            $ot_ends_morning =  Carbon::parse($record_date->year.'-'.$record_date->month.'-'.$record_date->day.' '.$shift_start);
            $ot_starts_evening = Carbon::parse($record_date->year.'-'.$record_date->month.'-'.$record_date->day.' '.$shift_end);

            $ot_in_minutes_morning = $on_time->diffInMinutes($ot_ends_morning);
            $ot_hours_morning = 0;
            if($on_time < $ot_ends_morning){
                $ot_hours_morning = $ot_in_minutes_morning / 60;
            }

            $ot_in_minutes_evening = $off_time->diffInMinutes($ot_starts_evening);
            $ot_hours_evening = 0;
            if($off_time > $ot_starts_evening) {
                $ot_hours_evening = $ot_in_minutes_evening / 60;
            }

            $ot_hours = $ot_hours_morning + $ot_hours_evening;

            $holiday_check = Holiday::where('date', $record_date->year.'-'.$record_date->month.'-'.$record_date->day )
                ->first();

            if(!empty($holiday_check)){
                if($holiday_check->work_type == 2 ){
                    $double_ot_hours = $ot_hours;
                    $ot_hours = 0;
                }
            }

            $emp_department = Employee::where('emp_id', $emp_id)->first();
            $department = Department::where('id', $emp_department->emp_department)->first();
            if($department->name == 'WORKSHOP' || $department->name == 'DRIVERS'){

                //actual ot start time = shift_end + 1 hour
                $actual_ot_start_time = Carbon::parse($record_date->year.'-'.$record_date->month.'-'.$record_date->day.' '.$shift_end)->addHour();
                if($actual_ot_start_time <= $off_time){
                    $normal_rate_otwork_hrs += $ot_hours;
                    $double_rate_otwork_hrs += $double_ot_hours;
                }
            }

            if($att_record->firsttimestamp == $att_record->lasttimestamp){
                $last_timestamp = '';
            }else{
                $last_timestamp = $att_record->lasttimestamp;
            }

            //get date from date
            $date_from_date = Carbon::parse($att_record->date)->format('Y-m-d');

            $data = array(
                'emp_id' => $att_record->emp_id,
                'emp_name' => $att_record->emp_name_with_initial,
                'etf_no' => $att_record->emp_etfno,
                'date' => $date_from_date,
                'day' => $day,
                'day_name' => $day_name,
                'first_timestamp' => $att_record->firsttimestamp,
                'last_timestamp' => $last_timestamp,
                'normal_rate_otwork_hrs' => $normal_rate_otwork_hrs,
                'double_rate_otwork_hrs' => $double_rate_otwork_hrs,
                'work_hours' => $work_hours,
            );

            $emp_data[] = $data;

        }

        return $emp_data;

    }

}
