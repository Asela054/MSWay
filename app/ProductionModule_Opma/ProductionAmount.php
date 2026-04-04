<?php

namespace App\ProductionModule_Opma;

use Illuminate\Database\Eloquent\Model;

class ProductionAmount extends Model
{
    protected $table = 'opma_production_amount';
    public $timestamps = false;
    protected $fillable = [
        'department_id',
        'jobtitle',
        'end_precentage',
        'amount',
    ];
}
