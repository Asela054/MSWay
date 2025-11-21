<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FinancialCategory extends Model
{
    
    protected $table = 'financial_categories';

    protected $primaryKey = 'id';

    protected $fillable = [
        'category'
    ];
}
