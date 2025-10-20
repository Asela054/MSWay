<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeProduction extends Model
{
     protected $table = 'employee_production';

    protected $primarykey = 'id';

    protected $fillable =[
        'allocation_id','emp_id','date','machine_id','product_id','amount','description','status','created_by', 'updated_by'
    ];
}
