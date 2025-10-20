<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmpTaskAllocation extends Model
{
    protected $table = 'emp_task_allocation';

    protected $primarykey = 'id';

    protected $fillable =[
        'date','task_id','cancel_description','task_status','status','created_by', 'updated_by'
    ];
}
