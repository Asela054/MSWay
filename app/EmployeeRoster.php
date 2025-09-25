<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeRoster extends Model
{
     protected $fillable = [
        'company_id',
        'employee_id',
        'work_date',
        'attendance_options',
        'attendance_option_other_remarks',
        'covering_employee_id_assignment',
        'authorized_shifts_for_company_id',
        'master_shift_id',
        'scheduling_status',
    ];
}
