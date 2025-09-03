<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeBank extends Model
{
    protected $table = 'employee_banks';
    protected $fillable = ['emp_id',
        'bank_code',
        'branch_code',
        'bank_ac_no'
    ];
}
