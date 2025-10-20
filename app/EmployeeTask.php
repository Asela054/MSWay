<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeTask extends Model
{
    protected $table = 'employee_task';

    protected $primarykey = 'id';

    protected $fillable =[
        'task_allocation_id','emp_id','date','task_id','amount','description','status','created_by', 'updated_by'
    ];
}
