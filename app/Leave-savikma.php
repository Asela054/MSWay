<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Leave extends Model
{
    protected $table = 'leaves';

    
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id', 'emp_id');
    }

    public function covering_employee()
    {
        return $this->belongsTo(Employee::class, 'emp_covering', 'emp_id');
    }

    public function approve_by()
    {
        return $this->belongsTo(Employee::class, 'leave_approv_person', 'emp_id');
    }

     public function get_leave_days($emp_id, $month ,$closedate)
    {
        
        if ($month === '2026-05') {
            $startDate = '2026-05-27';
            $query = DB::table('leaves')
                ->select(DB::raw('SUM(no_of_days) as total'))
                ->where('emp_id', $emp_id)
                ->where('status', 'Approved')
                ->where('leave_from', '>=', $startDate)
                ->where('leave_from', '<=', $closedate)
                ->whereNotIn('leave_type', [7, 3]);
        } else {
            $query = DB::table('leaves')
                ->select(DB::raw('SUM(no_of_days) as total'))
                ->where('emp_id', $emp_id )
                ->where('status', 'Approved' )
                ->where('leave_from', 'like',  $month . '%')
                ->where('leave_from', '<=', $closedate)
                ->whereNotIn('leave_type', [7, 3]);
        }
       
        $leave_days_data = $query->get();
        $leave_days = (!empty($leave_days_data[0]->total)) ? $leave_days_data[0]->total : 0;
        return $leave_days;

        
    }

    public function get_no_pay_days($emp_id, $month ,$closedate){

     if ($month === '2026-05') {
            $startDate = '2026-05-27';

             $query = DB::table('leaves')
            ->select(DB::raw('SUM(no_of_days) as total'))
            ->where('emp_id', '=' , $emp_id)
            ->where('leave_from', '>=', $startDate)
            ->where('leave_from', '<=', $closedate)
            ->where('leave_type', '=', '3')
            ->where('status', '=', 'Approved');
     } else {
        $query = DB::table('leaves')
            ->select(DB::raw('SUM(no_of_days) as total'))
            ->where('emp_id', '=' , $emp_id)
            ->where('leave_from', 'like',  $month . '%')
            ->where('leave_from', '<=', $closedate)
            ->where('leave_type', '=', '3')
            ->where('status', '=', 'Approved');
     }
        $no_pay_days_data = $query->get();
        $no_pay_days = (!empty($no_pay_days_data[0]->total)) ? $no_pay_days_data[0]->total : 0;

        return $no_pay_days;
    }

    // Calculate taken annual leaves count
    public function taken_annual_leaves($emp_id,$from_date,$to_date)
    {

          $total_taken_annual_leaves = DB::table('leaves')
                ->where('leaves.emp_id', '=', $emp_id)
                ->whereBetween('leaves.leave_from', [$from_date, $to_date])
                ->where('leaves.leave_type', '=', '1')
                ->get()->toArray();

            $current_year_taken_a_l = 0;

            foreach ($total_taken_annual_leaves as $tta){
                $leave_from = $tta->leave_from;
                $leave_to = $tta->leave_to;

                $leave_from_year = Carbon::parse($leave_from)->year;
                $leave_to_year = Carbon::parse($leave_to)->year;

                if($leave_from_year != $leave_to_year){
                    //get current year leaves for that record
                    $lastDayOfMonth = Carbon::parse($leave_from)->endOfMonth()->toDateString();

                    $to = \Carbon\Carbon::createFromFormat('Y-m-d', $lastDayOfMonth);
                    $from = \Carbon\Carbon::createFromFormat('Y-m-d', $leave_from);

                    $diff_in_days = $to->diffInDays($from);
                    $current_year_taken_a_l += $diff_in_days;

                    $jan_data = DB::table('leaves')
                        ->where('leaves.id', '=', $tta->id)
                        ->first();

                    $firstDayOfMonth = Carbon::parse($jan_data->leave_to)->startOfMonth()->toDateString();
                    $to_t = \Carbon\Carbon::createFromFormat('Y-m-d', $jan_data->leave_to);
                    $from_t = \Carbon\Carbon::createFromFormat('Y-m-d', $firstDayOfMonth);

                    $diff_in_days_f = $to_t->diffInDays($from_t);
                    $current_year_taken_a_l += $diff_in_days_f;

                }else{
                    $current_year_taken_a_l += $tta->no_of_days;
                }
            }

             return $current_year_taken_a_l;

    }

     // Calculate taken casual leaves count
    public function taken_casual_leaves($emp_id,$from_date,$to_date)
    {
         $total_taken_casual_leaves = DB::table('leaves')
                ->where('leaves.emp_id', '=', $emp_id)
                ->whereBetween('leaves.leave_from', [$from_date, $to_date])
                ->where('leaves.leave_type', '=', '2')
                ->get()->toArray();

            $current_year_taken_c_l = 0;
            
            foreach ($total_taken_casual_leaves as $tta){
                $leave_from = $tta->leave_from;
                $leave_to = $tta->leave_to;

                $leave_from_year = Carbon::parse($leave_from)->year;
                $leave_to_year = Carbon::parse($leave_to)->year;

                if($leave_from_year != $leave_to_year){
                    //get current year leaves for that record
                    $lastDayOfMonth = Carbon::parse($leave_from)->endOfMonth()->toDateString();

                    $to = \Carbon\Carbon::createFromFormat('Y-m-d', $lastDayOfMonth);
                    $from = \Carbon\Carbon::createFromFormat('Y-m-d', $leave_from);

                    $diff_in_days = $to->diffInDays($from);
                    $current_year_taken_c_l += $diff_in_days;
                }else{
                    $current_year_taken_c_l += $tta->no_of_days;
                }
            }

             return $current_year_taken_c_l;

    }

     // Calculate taken medical leaves count
    public function taken_medical_leaves($emp_id,$from_date,$to_date)
    {
        $total_taken_med_leaves = DB::table('leaves')
                ->where('leaves.emp_id', '=', $emp_id)
                ->whereBetween('leaves.leave_from', [$from_date, $to_date])
                ->where('leaves.leave_type', '=', '4')
                ->get()->toArray();

            $current_year_taken_med = 0;
            foreach ($total_taken_med_leaves as $tta){
                $leave_from = $tta->leave_from;
                $leave_to = $tta->leave_to;

                $leave_from_year = Carbon::parse($leave_from)->year;
                $leave_to_year = Carbon::parse($leave_to)->year;

                if($leave_from_year != $leave_to_year){
                    //get current year leaves for that record
                    $lastDayOfMonth = Carbon::parse($leave_from)->endOfMonth()->toDateString();

                    $to = \Carbon\Carbon::createFromFormat('Y-m-d', $lastDayOfMonth);
                    $from = \Carbon\Carbon::createFromFormat('Y-m-d', $leave_from);

                    $diff_in_days = $to->diffInDays($from);
                    $current_year_taken_med += $diff_in_days;
                }else{
                    $current_year_taken_med += $tta->no_of_days;
                }
            }

            return $current_year_taken_med;
    }

    public function taken_weekly_leaves($emp_id,$from_date,$to_date)
    {
         $weekly_leaves = DB::table('leaves')
                        ->where('emp_id', $emp_id)
                        ->whereBetween('leave_from', [$from_date, $to_date])
                        ->where('leave_type', '8')
                        ->get();

            $total_days = 0;

          foreach ($weekly_leaves as $leave) {
                $leave_from = Carbon::parse($leave->leave_from);
                $leave_to = Carbon::parse($leave->leave_to);
                
                // For monthly reset, we need to handle leaves that might span across months
                if ($leave_from->month != $leave_to->month || $leave_from->year != $leave_to->year) {
                    // If leave spans across months, only count days in the current month
                    $end_of_month = Carbon::parse($from_date)->endOfMonth();
                    $total_days += $leave_from->diffInDays($end_of_month) + 1;
                } else {
                    $total_days += $leave->no_of_days;
                }
            }
            
         return $total_days;

    }



public function calculateMonthlyLeaveBalance($emp_id, $monthly_allocation, $poya_covering_leave = 0)
{
    $currentYear = Carbon::now()->year;
    $currentMonth = Carbon::now()->month;
    
    $remaining_balance = 0;
    
    // Start from current month and go backwards
    for ($month = $currentMonth; $month >= 1; $month--) {
        // Get month start and end dates
        $monthStart = Carbon::createFromDate($currentYear, $month, 1)->startOfMonth();
        $monthEnd   = Carbon::createFromDate($currentYear, $month, 1)->endOfMonth();
        
        // Get leaves taken in this month
        $leaves_taken_in_month = $this->taken_annual_leaves( $emp_id, $monthStart, $monthEnd);
        
        // --- Poya covering bonus ---
        $poya_bonus = 0;
        if ($poya_covering_leave == 1) {
            $poya_bonus = $this->countPoyaWorkedDays($emp_id, $monthStart, $monthEnd);
        }
        
        // Calculate this month's unused leaves and add to balance
        $remaining_balance += ($monthly_allocation + $poya_bonus - $leaves_taken_in_month);
        
        
        // If employee used the full allocation this month, stop here
        if ($leaves_taken_in_month >= ($monthly_allocation + $poya_bonus)) {
            break;
        }
    }
    
    return $remaining_balance;
}

   private function countPoyaWorkedDays($emp_id, $monthStart, $monthEnd)
{
    $poyaDays = DB::table('holidays')
        ->where('holiday_type', 1)
        ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
        ->pluck('date')
        ->map(function($d) { return Carbon::parse($d)->toDateString(); })
        ->toArray();

    if (empty($poyaDays)) {
        return 0;
    }

    $workedCount = DB::table('attendances')
        ->where('emp_id', $emp_id)
        ->whereNull('deleted_at')
        ->where(function($query) use ($poyaDays) {
            foreach ($poyaDays as $poyaDay) {
                $query->orWhereDate('date', '=', $poyaDay);
            }
        })
        ->distinct(DB::raw('DATE(date)'))
        ->count(DB::raw('DATE(date)'));

    return $workedCount;
}

public function get_duty_leaves($emp_id, $month ,$closedate){

    if ($month === '2026-05') {
            $startDate = '2026-05-27';
             $query = DB::table('leaves')
            ->select(DB::raw('SUM(no_of_days) as total'))
            ->where('emp_id', '=' , $emp_id)
            ->where('leave_from', '>=', $startDate)
            ->where('leave_from', '<=', $closedate)
            ->where('leave_type', '=', '6')
            ->where('status', '=', 'Approved');
     } else {

        $query = DB::table('leaves')
            ->select(DB::raw('SUM(no_of_days) as total'))
            ->where('emp_id', '=' , $emp_id)
            ->where('leave_from', 'like',  $month . '%')
            ->where('leave_from', '<=', $closedate)
            ->where('leave_type', '=', '6')
            ->where('status', '=', 'Approved');
     }
        $dutyleaves = $query->get();
        $total_dutyleaves = (!empty($dutyleaves[0]->total)) ? $dutyleaves[0]->total : 0;

        return $total_dutyleaves;
}


public function get_dayoff_leaves($emp_id, $month, $closedate)
{
    if ($month === '2026-05') {
        $startDate = '2026-05-27';

        $scheduledDates = DB::table('employee_roster_details')
            ->where('emp_id', $emp_id)
            ->where('work_date', '>=', $startDate)
            ->where('work_date', '<=', $closedate)
            ->pluck('work_date')
            ->map(function ($d) {
                return Carbon::parse($d)->format('Y-m-d');
            })
            ->toArray();

        $leaveDates = DB::table('leaves')
            ->where('emp_id', $emp_id)
            ->where('leave_from', '>=', $startDate)
            ->where('leave_from', '<=', $closedate)
            ->where('status', 'Approved')
            ->pluck('leave_from')
            ->map(function ($d) {
                return Carbon::parse($d)->format('Y-m-d');
            })
            ->toArray();

        $start = Carbon::parse($startDate);
    } else {
        $scheduledDates = DB::table('employee_roster_details')
            ->where('emp_id', $emp_id)
            ->where('work_date', 'like', $month . '%')
            ->where('work_date', '<=', $closedate)
            ->pluck('work_date')
            ->map(function ($d) {
                return Carbon::parse($d)->format('Y-m-d');
            })
            ->toArray();

        $leaveDates = DB::table('leaves')
            ->where('emp_id', $emp_id)
            ->where('leave_from', 'like', $month . '%')
            ->where('leave_from', '<=', $closedate)
            ->where('status', 'Approved')
            ->pluck('leave_from')
           ->map(function ($d) {
                return Carbon::parse($d)->format('Y-m-d');
            })
            ->toArray();

        $start = Carbon::parse($month . '-01');
    }

    $end = Carbon::parse($closedate);

    $total_dutyleaves = 0;

    for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
        $d = $date->format('Y-m-d');

        $isOffDay = !in_array($d, $scheduledDates);
        $hasLeave = in_array($d, $leaveDates);

        if ($isOffDay && !$hasLeave) {
            $total_dutyleaves++;
        }
    }

    return $total_dutyleaves;
}

}
