<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class OtApproved extends Model
{
    protected $table = 'ot_approved';
    protected $fillable = [
        'emp_id',
        'date',
        'from',
        'to',
        'hours',
        //'one_point_five_hours',
        'double_hours',
        'is_holiday',
        'status',
        'created_at',
        'created_by'];

    public function get_ot_hours_monthly($emp_id, $month ,$closedate)
    {
        $ot_hours = OtApproved::where('emp_id', $emp_id)
                            ->where('date', 'like', $month.'%')
                            ->where('date', '<=', $closedate)
                            ->where('status', '!=', 3)
                            ->sum('hours');
        return $ot_hours;
    }

    public function get_double_ot_hours_monthly($emp_id, $month ,$closedate)
    {
        $double_ot_hours = OtApproved::where('emp_id', $emp_id)
                            ->where('date', 'like', $month.'%')
                            ->where('date', '<=', $closedate)
                            ->where('status', '!=', 3)
                            ->sum('double_hours');
        return $double_ot_hours;
    }

    public function get_triple_ot_hours_monthly($emp_id, $month ,$closedate)
    {
        $triple_ot_hours = OtApproved::where('emp_id', $emp_id)
                            ->where('date', 'like', $month.'%')
                            ->where('date', '<=', $closedate)
                            ->where('status', '!=', 3)
                            ->sum('triple_hours');
        return $triple_ot_hours;
    }

    public function is_exists_in_ot_approved($emp_id, $date, $OTfrom){
        $date = Carbon::parse($date);
        $date = $date->format('Y-m-d');
        $ot = OtApproved::where('emp_id', $emp_id)
            ->where('date', '=', $date)
            ->where('from', '=', $OTfrom)
          ->where(function($query) {
                $query->where('status', '!=', 3)
                    ->orWhereNull('status');
            })
            ->get();

        $status = true;
        if($ot->isEmpty()){
            $status = false;
        }

        return $status;
    }
    
      public function get_ot_hours_monthly_ktClean($emp_id, $month ,$closedate)
    {
        $ot_hours =  DB::table('kt_shift_ot')
                            ->where('emp_id', $emp_id)
                            ->where('date', 'like', $month.'%')
                            ->where('date', '<=', $closedate)
                            ->where('approve_status', '=', 1)
                            ->sum('ot_hours');
        return $ot_hours;
    }
    
   public function get_night_work_days($emp_id, $month, $closedate)
    {
        $night_days = DB::table('employee_roster_details')
            ->where('employee_roster_details.emp_id', $emp_id)
            ->where('employee_roster_details.work_date', 'like', $month.'%')
            ->where('employee_roster_details.work_date', '<=', $closedate)
            ->where('employee_roster_details.shift_id', '=', 4)
            ->join('attendances', function ($join) use ($emp_id) {
                $join->on(DB::raw('DATE(attendances.date)'), '=', 'employee_roster_details.work_date')
                    ->where('attendances.emp_id', '=', $emp_id);
            })
            ->distinct('employee_roster_details.work_date')
            ->count('employee_roster_details.work_date');

        return $night_days;
    }
}
