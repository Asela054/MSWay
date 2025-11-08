<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmpProductAllocation extends Model
{
    protected $table = 'emp_product_allocation';

    protected $primarykey = 'id';

    protected $fillable = [
    'date', 'status', 'created_by', 'updated_by',
    'machine_id', 'product_id', 'shift_id', 'product_type',
    'semi_amount', 'full_amount', 'cancel_description',
    'production_status','complete_status'
];
}
