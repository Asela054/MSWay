<?php

namespace App\ProductionModule_Opma;

use Illuminate\Database\Eloquent\Model;

class EmpProductAllocation extends Model
{
    protected $table = 'opma_emp_product_allocation';

    protected $primarykey = 'id';

    protected $fillable = [
    'date', 'status', 'created_by', 'updated_by',
    'machine_id', 'product_id', 'shift_id','target','scale','size','remark', 'product_type',
    'semi_amount', 'full_amount', 'cancel_description',
    'production_status','complete_status'
];
}
