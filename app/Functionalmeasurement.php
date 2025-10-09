<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Functionalmeasurement extends Model
{
    protected $primarykey = 'id';

    protected $fillable =[

        'type_id','kpi_id','parameter_id','measurement','department_id','status','created_by', 'updated_by','created_at','updated_at'
    ];
}
