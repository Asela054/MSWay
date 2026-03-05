<?php

namespace App\ProductionModule_Opma;

use Illuminate\Database\Eloquent\Model;

class EmpProductAllocationDetail extends Model
{
    protected $table = 'opma_emp_product_allocation_details';

    protected $primaryKey = 'id';

    protected $fillable = [
        'allocation_id','emp_id','date', 'status', 'adding_status','created_by', 'updated_by'
    ];
}
