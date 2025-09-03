<?php

namespace App;

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
        'created_at',
        'created_by'];

    public function get_ot_hours_monthly($emp_id, $month )
    {
        $ot_hours = OtApproved::where('emp_id', $emp_id)
                            ->where('date', 'like', $month.'%')
                            ->sum('hours');
        return $ot_hours;
    }

    public function get_double_ot_hours_monthly($emp_id, $month )
    {
        $double_ot_hours = OtApproved::where('emp_id', $emp_id)
                            ->where('date', 'like', $month.'%')
                            ->sum('double_hours');
        return $double_ot_hours;
    }

}
