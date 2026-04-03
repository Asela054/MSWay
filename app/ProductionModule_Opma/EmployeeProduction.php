<?php

namespace App\ProductionModule_Opma;

use Illuminate\Database\Eloquent\Model;

class EmployeeProduction extends Model
{
     protected $table = 'opma_employee_production';

    protected $primarykey = 'id';

    protected $fillable =[
        'allocation_id','emp_id','date','machine_id','product_id','target','Produce_qty','difference','precentage','amount','description','damage_precentage','damage_qty','perfomance','status','created_by', 'updated_by'
    ];
}
