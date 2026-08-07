<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class employeeWorkRate extends Model
{
    protected $table = 'employee_work_rates';
    protected $fillable = [
        'emp_id',
        'emp_etfno',
        'work_year',
        'work_month',
        'work_days',
        'working_week_days',
        'work_hours',
        'leave_days',
        'nopay_days',
        'normal_rate_otwork_hrs',
        'double_rate_otwork_hrs',
        'day_off',
        'night_days',
        'additional_OT',
        'sunday_work_days',
        'poya_work_days',
        'poya_nopay_days',
        'mercantile_work_days',
        'mercantile_nopay_days',
        'sunday_double_ot_hrs',
        'poya_extended_normal_ot_hrs',
        'absent_nopay',
        'excess_working_days',
        'poya_day_work_hours',
        'mercantile_work_hours',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'

    ];

}
