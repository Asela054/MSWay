<?php

namespace App\ProductionModule_Opma;

use Illuminate\Database\Eloquent\Model;

class ProductionemployeeDailyEnding extends Model
{
    protected $table = 'opma_daily_production_summary';

    protected $primarykey = 'id';

    protected $fillable =[
        'emp_id','date','target','produce','difference','bonus','damage','created_by', 'updated_by'
    ];
}
