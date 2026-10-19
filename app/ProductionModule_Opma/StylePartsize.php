<?php

namespace App\ProductionModule_Opma;

use Illuminate\Database\Eloquent\Model;

class StylePartSize extends Model
{
    protected $table = 'opma_style_part_sizes';

    protected $fillable = [
        'opma_style_part_id',
        'opma_size_id',
    ];
}