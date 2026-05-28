<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeResignBy extends Model
{
    protected $table = 'employee_resign_by';

    protected $fillable = [
        'emp_id',
        'updated_by',
    ];
}