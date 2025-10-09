<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Functionaldepartment_detail extends Model
{
    protected $primarykey = 'id';

    protected $fillable =[

        'type_id','kpi_id','parameter_id','measurement_id','department_id','departmentweightage','status','created_by', 'updated_by','created_at','updated_at'
    ];
}
