<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeAssignBy extends Model
{
    protected $table = 'employee_assign_by';

    protected $fillable = [
        'emp_id',
        'emp_assign_id',
        'updated_by',
    ];
}