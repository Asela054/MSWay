<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employeeshiftdetail extends Model
{
    protected $primarykey = 'id';

    protected $fillable =[

        'employeeshift_id','shift_id','date_from','until_time','off_next_day','emp_id','employee_name','status','created_by', 'updated_by'
    ];
}
