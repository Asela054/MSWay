<?php

namespace App\ProductionModule_Opma;

use App\Holiday;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use DateTime;
use DateInterval;

class OpmaAttendance extends Model
{
      public function get_work_days($emp_id, $month,$closedate)
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
        AND at1.date LIKE '$month%'
        AND at1.date <= '$closedate'
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

            // ---- NEW: daily_average check from opma_daily_approval_summary ----
            $dailyApproval = DB::table('opma_daily_approval_summary')
                ->where('emp_id', $emp_id)
                ->where('date', $s_date)
                ->first();

            if (!$dailyApproval || $dailyApproval->daily_average <= 50) {
                continue;
            }
            // ---------------------------------------------------------------

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

      public function get_work_days_for_transport($emp_id, $month,$closedate)
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
        AND at1.date LIKE '$month%'
        AND at1.date <= '$closedate'
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

            // ---- NEW: daily_average check from opma_daily_approval_summary ----
            $dailyApproval = DB::table('opma_daily_approval_summary')
                ->where('emp_id', $emp_id)
                ->where('date', $s_date)
                ->first();

            if (!$dailyApproval || $dailyApproval->daily_average <= 70) {
                continue;
            }
            // ---------------------------------------------------------------

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
}
