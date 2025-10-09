<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Functionalkpi extends Model
{
    protected $primarykey = 'id';

    protected $fillable =[

        'type_id','kpi','status','created_by', 'updated_by','created_at','updated_at'
    ];
}
