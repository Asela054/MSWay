<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeePayslip extends Model
{
    protected $table = 'employee_payslips';
    protected $fillable = [
        'payroll_profile_id',
        'emp_payslip_no',
        'payment_period_id',
        'payment_period_fr',
        'payment_period_to',
        'payslip_cancel',
        'payslip_held',
        'payslip_held_cnt',
        'payslip_approved',
        'payroll_process_type_id',
        'basic_salary',
        'day_salary'
    ];
}
