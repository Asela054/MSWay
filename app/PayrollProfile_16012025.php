<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PayrollProfile extends Model
{
     protected $table = 'payroll_profiles';
     protected $fillable = ['emp_id',
         'emp_etfno',
         'payroll_process_type_id',
         'payroll_act_id',
         'employee_bank_id',
         'employee_executive_level',
         'basic_salary',
         'day_salary'
     ];
}
