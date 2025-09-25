<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeRosterDetails extends Model
{
     protected $table = 'employee_roster_details';
     protected $fillable = [
        'shift_id',
        'emp_id',
        'work_date',
        'scheduling_status',
        'remark',
    ];
}
