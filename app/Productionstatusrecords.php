<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Productionstatusrecords extends Model
{
    protected $table = 'production_status_records';

    protected $primaryKey = 'id';

    protected $fillable = [
        'production_id','date','employee_count', 'timestamp', 'produced_quntity','production_status','created_by'
    ];
}
