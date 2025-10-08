<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Functionalmeasurementweightage extends Model
{
    protected $primarykey = 'id';

    protected $fillable =[

        'type_id','kpi_id','parameter_id','measurement_id','measurement_weightage','status','created_by', 'updated_by','created_at','updated_at'
    ];
}
