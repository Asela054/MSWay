<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Policestation extends Model
{
    
    protected $table = 'employee_police_station';
    protected $primaryKey = 'id';

    protected $fillable =[
        'police_station','status'
         
    ];
}
