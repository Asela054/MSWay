<?php

namespace App\ProductionModule_Opma;

use Illuminate\Database\Eloquent\Model;

class StylePart extends Model
{
    protected $table = 'opma_style_parts';

    protected $fillable = [
        'style_id',
        'style_details',
        'part_name',
        'scales',
        'currency_type',
        'unit_price',
        'status',
        'create_by',
        'update_by',
    ];
}