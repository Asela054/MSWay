<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmpTaskAllocationDetail extends Model
{
    protected $table = 'emp_task_allocation_details';

    protected $primaryKey = 'id';

    protected $fillable = [
        'allocation_id','emp_id','date', 'status', 'created_by', 'updated_by'
    ];
}
