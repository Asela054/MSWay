<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employeeshiftdetail extends Model
{
    protected $primarykey = 'id';

    protected $fillable =[

        'employeeshift_id','shift_id','date_from','date_to','emp_id','employee_name','status','created_by', 'updated_by'
    ];
}
