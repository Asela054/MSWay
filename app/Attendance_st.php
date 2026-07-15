<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use DateTime;
use DateInterval;

class Attendance_st extends Model
{
    public function get_work_days($emp_id, $fromdate, $todate)
    {

         $shiftQuery = "SELECT st.onduty_time, st.offduty_time, st.saturday_onduty_time, st.saturday_offduty_time 
                   FROM employees emp 
                   JOIN shift_types st ON emp.emp_shift = st.id 
                   WHERE emp.emp_id = $emp_id 
                   LIMIT 1";
    
            $shiftInfo = \DB::select($shiftQuery);
            
            if (empty($shiftInfo)) {
                $expectedHours = 8;
                $halfDayHours = 4;
                $saturdayExpectedHours = 8;
                $saturdayHalfDayHours = 4;
            } else {
                $shift = $shiftInfo[0];
                
                   // Parse times using Carbon
                $ondutyTime = Carbon::parse($shift->onduty_time);
                $offdutyTime = Carbon::parse($shift->offduty_time);

                $saturdayOndutyTime = Carbon::parse($shift->saturday_onduty_time);
                $saturdayOffdutyTime = Carbon::parse($shift->saturday_offduty_time);

                 $expectedHours = $ondutyTime->diffInHours($offdutyTime);
                 $halfDayHours = $expectedHours / 2;

                 $saturdayExpectedHours = $saturdayOndutyTime->diffInHours($saturdayOffdutyTime);
                 $saturdayHalfDayHours = $saturdayExpectedHours / 2;
            }

         $empjob_cat = DB::table('employees')
            ->leftJoin('job_categories', 'job_categories.id' , '=', 'employees.job_category_id')
            ->select('job_categories.full_day_work_hours')
            ->where('emp_id', $emp_id)
            ->first();

        $full_day_work_hours = $empjob_cat ? $empjob_cat->full_day_work_hours : 8;

        $query = "SELECT Max(at1.timestamp) as lasttimestamp,
        Min(at1.timestamp) as firsttimestamp
        FROM attendances as at1
        WHERE at1.emp_id = $emp_id
        AND at1.date >= '$fromdate'
        AND at1.date <= '$todate'
        AND at1.deleted_at IS NULL
        group by at1.uid, at1.date
        ";
        $attendance = \DB::select($query);

        $work_days = 0;
        foreach ($attendance as $att) {

            $first_time = $att->firsttimestamp;
            $last_time = $att->lasttimestamp;

            $date = Carbon::parse($first_time);
            $s_date = $date->format('Y-m-d');
            $holiday_check = Holiday::where('date', $s_date)
                ->where('work_level', '=', '2')
                ->first();

            if(!EMPTY($holiday_check)){
                continue;
            }

            // $work_days++;
            $diff = round((strtotime($last_time) - strtotime($first_time)) / 3600, 1);

               if ($date->isSaturday()) {
                    $required_full_hours = $saturdayExpectedHours;
                } else {
                    $required_full_hours = $full_day_work_hours;
                }
                
            if ($diff >= $required_full_hours) {
                $work_days++;
            } else{
                $work_days += 0.5;
            }
        }
        return $work_days;
    }

    public function get_working_week_days_confirmed($emp_id, $fromdate, $todate)
    {
        $data = WorkHour::where('date', '>=', $fromdate)
            ->where('date', '<=', $todate)
            ->where('emp_id', $emp_id)
            ->select(DB::raw('SUM(no_of_days) as total'))
            ->get();

        return array(
            'no_of_days' => $data[0]->total
        );
    }

    public function get_working_week_days($emp_id, $fromdate, $todate)
    {

          $shiftQuery = "SELECT st.onduty_time, st.offduty_time 
                   FROM employees emp 
                   JOIN shift_types st ON emp.emp_shift = st.id 
                   WHERE emp.emp_id = $emp_id 
                   LIMIT 1";
    
            $shiftInfo = \DB::select($shiftQuery);
            
            if (empty($shiftInfo)) {
                $expectedHours = 8;
                $halfDayHours = 4;
            } else {
                $shift = $shiftInfo[0];
                
                   // Parse times using Carbon
                $ondutyTime = Carbon::parse($shift->onduty_time);
                $offdutyTime = Carbon::parse($shift->offduty_time);
                
                 $expectedHours = $ondutyTime->diffInHours($offdutyTime);
                 $halfDayHours = $expectedHours / 2;
            }



        $query = "SELECT Max(at1.timestamp) as lasttimestamp,
        Min(at1.timestamp) as firsttimestamp, date
        FROM attendances as at1
        WHERE at1.emp_id = $emp_id
        AND at1.date >= '$fromdate'
        AND at1.date <= '$todate'
        AND at1.deleted_at IS NULL
        group by at1.uid, at1.date";
        
        $attendance = \DB::select($query);

        $no_of_working_workdays = 0;
        $working_work_days_breakdown_arr = array();
        $leave_deductions = array();
        foreach ($attendance as $att) {
            $first_time = $att->firsttimestamp;
            $last_time = $att->lasttimestamp;

            $date = Carbon::parse($att->date);
            $s_date = $date->format('Y-m-d');

            $emp = DB::table('employees')
                ->leftJoin('job_categories', 'job_categories.id' , '=', 'employees.job_category_id')
                ->leftJoin('shift_types', 'shift_types.id' , '=', 'employees.emp_shift')
                ->select('emp_shift', 'emp_etfno', 'emp_name_with_initial', 'job_category_id', 'job_categories.is_sat_ot_type_as_act', 'job_categories.custom_saturday_ot_type', 'shift_types.onduty_time', 'shift_types.offduty_time')
                ->where('emp_id', $emp_id)
                ->first();

            $shift_on = Carbon::parse($date->year.'-'.$date->month.'-'.$date->day.' '.$emp->onduty_time);
            $shift_off = Carbon::parse($date->year.'-'.$date->month.'-'.$date->day.' '.$emp->offduty_time);

            $seven_am = Carbon::parse('07:35');
            $seven_am_time = $seven_am->format('H:i');
            $today_seven = Carbon::parse($date->year.'-'.$date->month.'-'.$date->day.' '.$seven_am_time);

            $eight_am = Carbon::parse('08:35');
            $eight_am_time = $eight_am->format('H:i');
            $today_eight = Carbon::parse($date->year.'-'.$date->month.'-'.$date->day.' '.$eight_am_time);

            $twelve_pm = Carbon::parse('12:00');
            $twelve_pm_time = $twelve_pm->format('H:i');
            $today_twelve = Carbon::parse($date->year.'-'.$date->month.'-'.$date->day.' '.$twelve_pm_time);

            $one_pm = Carbon::parse('13:00');
            $one_pm_time = $one_pm->format('H:i');
            $today_one = Carbon::parse($date->year.'-'.$date->month.'-'.$date->day.' '.$one_pm_time);

            $work_hours_from = null;
            $work_hours_to = null;

            if($first_time <= $shift_on) {
                $work_hours_from = $today_eight;
            }

            if($first_time > $shift_on) {
                $work_hours_from = $first_time;
            }

            if($first_time >=$today_twelve && $first_time < $today_one ){
                $work_hours_from = $today_one;
            }  
            else if($shift_off >=$today_twelve && $shift_off < $today_one){

            }
            else{
                if($work_hours_to !== null) {
                        $work_hours_to->subHours(1);
                    }
            }

            
            if($last_time > $shift_off) {
                $work_hours_to = $shift_off;
            }else{
                $work_hours_to = $last_time;
            }

            $work_hours_to = Carbon::parse($work_hours_to);
            $work_hours_from = Carbon::parse($work_hours_from);

            if($first_time >=$today_twelve && $first_time < $today_one ){
            }else{
                $work_hours_to->subHours(1);
            }

            //get difference in hours
            $diff = round((strtotime($work_hours_to) - strtotime($work_hours_from)) / 3600, 1);

            if($first_time >=$today_twelve && $first_time < $today_one ){
            }else{
                $work_hours_to->addHours(1);
            }

            $day = $date->dayOfWeek;

            $holiday_check = Holiday::where('date', $s_date)
                ->where('work_level', '=', '2')
                ->first();

            $is_holiday = false;
            if(!EMPTY($holiday_check)){
                $is_holiday = true;
            }

            $work_days_arr = [1,2,3,4,5];

            if($emp->is_sat_ot_type_as_act == 2){
                array_push($work_days_arr, 6);
            }

            //if diff is greater than 8 hours then it is a work day
            //if diff is greater than 4 hours then it is a half day
            //if diff is greater than 2 hours then it is a half day
            if ($diff >= $expectedHours) {

                if (in_array($day ,$work_days_arr)){
                    if(!$is_holiday){
                        $no_of_working_workdays++;
                        $arr_work_days_bd = array(
                            'from' =>  $work_hours_from->format('Y-m-d H:i'),
                            'to' => $work_hours_to->format('Y-m-d H:i'),
                            'date' => $s_date,
                            'day_name' => Carbon::parse($date)->format('l'),
                            'hours' => $diff,
                            'work_day' => 1,
                            'is_no_pay_leave' => false
                        );
                        array_push($working_work_days_breakdown_arr, $arr_work_days_bd);
                    }
                }

            } elseif ($diff >=  $halfDayHours) {

                if (in_array($day ,$work_days_arr)){
                    if(!$is_holiday) {
                        //check if no pay half leaves exists for today
                        $leave = DB::table('leaves')
                            ->select('no_of_days', 'leave_from')
                            ->where('emp_id', $emp_id )
                            ->where('leave_type', '3' )
                            ->where('status', 'Approved' )
                            ->where('leave_from', '=',  $s_date )
                            ->first();

                        if (empty($leave) ) {
                            $no_of_working_workdays += 0.5;
                            //var_dump($s_date. ' + 0.5');
                            $arr_work_days_bd = array(
                                'from' => $work_hours_from->format('Y-m-d H:i'),
                                'to' => $work_hours_to->format('Y-m-d H:i'),
                                'date' => $s_date,
                                'day_name' => Carbon::parse($date)->format('l'),
                                'hours' => $diff,
                                'work_day' => 0.5,
                                'is_no_pay_leave' => false
                            );
                            array_push($working_work_days_breakdown_arr, $arr_work_days_bd);

                        }else{
                            $no_of_working_workdays += 0.5;
                            //var_dump($s_date. ' + 0.5');
                            $arr_work_days_bd = array(
                                'from' => $work_hours_from->format('Y-m-d H:i'),
                                'to' => $work_hours_to->format('Y-m-d H:i'),
                                'date' => $s_date,
                                'day_name' => Carbon::parse($date)->format('l'),
                                'hours' => $diff,
                                'work_day' => 0.5,
                                'is_no_pay_leave' => true
                            );
                            array_push($working_work_days_breakdown_arr, $arr_work_days_bd);
                        }

                    }
                }

            }
        }

        return array(
            'no_of_working_workdays' => $no_of_working_workdays,
            'working_work_days_breakdown' => $working_work_days_breakdown_arr,
            'leave_deductions' => $leave_deductions
        );

    }

    public function get_working_hours($emp_id, $fromdate, $todate)
    {
        $startDate = new DateTime($fromdate);
        $endDate = new DateTime($todate);

        $dateRange = [];
        while ($startDate <= $endDate) {
            $dateRange[] = $startDate->format('Y-m-d');
            $startDate->add(new DateInterval('P1D'));
        }


            $totalworkHours = 0;
            $totalweekworkshours = 0;
            $totalMinutesAll  = 0;

            foreach ($dateRange as $todayDate) {


                $ignoredate = DB::table('ignore_days')
                ->select('ignore_days.*')
                ->whereDate('date', $todayDate)
                ->first();

                if(!$ignoredate){
                    $query = DB::table('attendances as at1')
                            ->select(
                                'at1.id',
                                'at1.emp_id',
                                'at1.timestamp',
                                'at1.date',
                                'shift_types.onduty_time',
                                'shift_types.offduty_time'
                            )
                            ->leftJoin('employees', 'at1.emp_id', '=', 'employees.emp_id')
                            ->leftJoin('shift_types', 'employees.emp_shift', '=', 'shift_types.id')
                            ->whereNull('at1.deleted_at')
                            ->where('at1.emp_id', $emp_id)
                            ->where('at1.date', 'LIKE', $todayDate . '%')
                            ->orderBy('at1.timestamp', 'asc')
                            ->get();

                      if ($query->isNotEmpty()) {
                            $timestamps = $query->pluck('timestamp')->toArray();
                            $count = count($timestamps);

                            if ($count % 2 === 0) {
                                $totalMinutes = 0;

                                for ($i = 0; $i < $count; $i += 2) {
                                   $in  = Carbon::parse($timestamps[$i])->second(0);
                                   $out = Carbon::parse($timestamps[$i + 1])->second(0);

                                    if ($in && $out && $in != $out) {
                                        $totalMinutesAll  += $in->diffInMinutes($out);
                                    }
                                }
                            }
                        }

                }
            }
            
            $hours   = intdiv($totalMinutesAll, 60);
            $minutes = $totalMinutesAll % 60;
            $totalWorkHoursFormatted = sprintf('%d:%02d', $hours, $minutes);

            return $totalWorkHoursFormatted;
    }
}
