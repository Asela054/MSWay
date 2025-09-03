<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DSDivision extends Model
{
    protected $table = 'employee_ds_division';
    protected $primaryKey = 'id';

    protected $fillable =[
        'ds_division','status'
    ];
}
