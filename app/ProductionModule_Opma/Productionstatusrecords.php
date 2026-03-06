<?php

namespace App\ProductionModule_Opma;

use Illuminate\Database\Eloquent\Model;

class Productionstatusrecords extends Model
{
    protected $table = 'opma_production_status_records';

    protected $primaryKey = 'id';

    protected $fillable = [
        'production_id','date','employee_count', 'timestamp', 'produced_quntity','production_status','created_by'
    ];
}
