<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kpiallocation extends Model
{
    protected $primarykey = 'id';

    protected $fillable =[

        'year_id','measurement_id','figure','status','approve_status','approve_time','approve_by','created_by', 'updated_by','created_at','updated_at'
    ];
}
