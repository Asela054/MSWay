<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Functionalparameter extends Model
{
    protected $primarykey = 'id';

    protected $fillable =[

        'type_id','kpi_id','parameter','status','created_by', 'updated_by','created_at','updated_at'
    ];
}
