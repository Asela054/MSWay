<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmpProductAllocation extends Model
{
    protected $table = 'emp_product_allocation';

    protected $primarykey = 'id';

    protected $fillable =[

        'date','status','created_by', 'updated_by'
    ];
}
