<?php

namespace App\ProductionModule_Opma;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    protected $table = 'opma_sizes';

    protected $fillable = [
        'size',
        'remark'
    ];
}