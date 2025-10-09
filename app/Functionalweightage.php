<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Functionalweightage extends Model
{
    protected $primarykey = 'id';

    protected $fillable =[

        'type_id','kpi_id','parameter_id','weightage','status','created_by', 'updated_by','created_at','updated_at'
    ];
}
