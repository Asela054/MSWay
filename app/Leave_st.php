<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Leave_st extends Model
{
         public function get_leave_days($emp_id, $fromdate, $todate)
    {
        $query = DB::table('leaves')
            ->select(DB::raw('SUM(no_of_days) as total'))
            ->where('emp_id', $emp_id )
            ->where('status', 'Approved' )
            ->where('leave_from', '>=',  $fromdate . '%')
            ->where('leave_from', '<=', $todate)
            ->whereNotIn('leave_type', [7, 3]);
        $leave_days_data = $query->get();
        $leave_days = (!empty($leave_days_data[0]->total)) ? $leave_days_data[0]->total : 0;

        return $leave_days;
    }

    public function get_no_pay_days($emp_id, $fromdate, $todate){

        $query = DB::table('leaves')
            ->select(DB::raw('SUM(no_of_days) as total'))
            ->where('emp_id', '=' , $emp_id)
             ->where('leave_from', '>=',  $fromdate . '%')
            ->where('leave_from', '<=', $todate)
            ->where('leave_type', '=', '3')
            ->where('status', '=', 'Approved');

        $no_pay_days_data = $query->get();
        $no_pay_days = (!empty($no_pay_days_data[0]->total)) ? $no_pay_days_data[0]->total : 0;

        return $no_pay_days;
    }

    public function get_duty_leaves($emp_id, $fromdate, $todate){

        $query = DB::table('leaves')
            ->select(DB::raw('SUM(no_of_days) as total'))
            ->where('emp_id', '=' , $emp_id)
            ->where('leave_from', '>=',  $fromdate . '%')
            ->where('leave_from', '<=', $todate)
            ->where('leave_type', '=', '6')
            ->where('status', '=', 'Approved');

        $dutyleaves = $query->get();
        $total_dutyleaves = (!empty($dutyleaves[0]->total)) ? $dutyleaves[0]->total : 0;

        return $total_dutyleaves;
}

public function get_dayoff_leaves($emp_id,  $fromdate, $todate)
{
    $scheduledDates = DB::table('employee_roster_details')
        ->where('emp_id', $emp_id)
        ->where('work_date', '>=',  $fromdate . '%')
        ->where('work_date', '<=', $todate)
        ->pluck('work_date')
        ->map(function ($d) {
            return Carbon::parse($d)->format('Y-m-d');
        })
        ->toArray();

    $leaveDates = DB::table('leaves')
        ->where('emp_id', $emp_id)
        ->where('leave_from', '>=',  $fromdate . '%')
        ->where('leave_from', '<=', $todate)
        ->where('status', 'Approved')
        ->pluck('leave_from')
        ->map(function ($d) {
            return Carbon::parse($d)->format('Y-m-d');
        })
        ->toArray();

    $start = Carbon::parse($fromdate);
    $end   = Carbon::parse($todate);

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
