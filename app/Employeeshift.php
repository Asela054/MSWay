<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employeeshift extends Model
{
    protected $primarykey = 'id';

    protected $fillable =[

        'shift_id','date_from','date_to','status','created_by', 'updated_by'
    ];
}
