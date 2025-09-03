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
        'leave_days',
        'nopay_days',
        'normal_rate_otwork_hrs',
        'double_rate_otwork_hrs',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'

    ];

}
