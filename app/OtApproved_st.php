<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class OtApproved_st extends Model
{
     public function get_ot_hours_monthly($emp_id, $fromdate, $todate)
    {
        $ot_hours = OtApproved::where('emp_id', $emp_id)
                            ->where('date', '>=', $fromdate)
                            ->where('date', '<=', $todate)
                            ->where('status', '!=', 3)
                            ->sum('hours');
        return $ot_hours;
    }

    public function get_double_ot_hours_monthly($emp_id, $fromdate, $todate)
    {
        $double_ot_hours = OtApproved::where('emp_id', $emp_id)
                            ->where('date', '>=', $fromdate)
                            ->where('date', '<=', $todate)
                            ->where('status', '!=', 3)
                            ->sum('double_hours');
        return $double_ot_hours;
    }

    public function get_triple_ot_hours_monthly($emp_id, $fromdate, $todate)
    {
        $triple_ot_hours = OtApproved::where('emp_id', $emp_id)
                            ->where('date', '>=', $fromdate)
                            ->where('date', '<=', $todate)
                            ->where('status', '!=', 3)
                            ->sum('triple_hours');
        return $triple_ot_hours;
    }

      public function get_night_work_days($emp_id, $fromdate, $todate)
    {
        $night_days = DB::table('employee_roster_details')
            ->where('employee_roster_details.emp_id', $emp_id)
            ->where('employee_roster_details.work_date', '>=', $fromdate)
            ->where('employee_roster_details.work_date', '<=', $todate)
            ->whereIn('employee_roster_details.shift_id', [11, 12])
            ->join('attendances', function ($join) use ($emp_id) {
                $join->on(DB::raw('DATE(attendances.date)'), '=', 'employee_roster_details.work_date')
                    ->where('attendances.emp_id', '=', $emp_id);
            })
            ->distinct('employee_roster_details.work_date')
            ->count('employee_roster_details.work_date');

        return $night_days;
    }
}
