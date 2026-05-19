<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employeeshift extends Model
{
    protected $primaryKey = 'id';

    protected $fillable =[

        'shift_id','date_from','date_to','remark','status','approval_status','created_by', 'updated_by'
    ];
}
