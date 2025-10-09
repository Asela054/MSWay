<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kpidepartment_detail extends Model
{
    protected $primarykey = 'id';

    protected $fillable =[

        'measurement_id','department_id','departmentfigure','status','created_by', 'updated_by','created_at','updated_at'
    ];
}
