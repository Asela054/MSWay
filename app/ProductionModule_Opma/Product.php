<?php

namespace App\ProductionModule_Opma;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'opma_styles';

    protected $fillable = [
        'title',
        'code',
        'from_date',
        'to_date',
        'request_qty',
        'request_qty_edited',
        'status',
        'over_qty_approve_status',
        'currency_type',
        'unit_price'
    ];

}
