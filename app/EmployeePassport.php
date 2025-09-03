<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeePassport extends Model
{
    protected $table = 'employee_passports';
    protected $primaryKey = 'emp_pass_id';
}
