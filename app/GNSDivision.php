<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GNSDivision extends Model
{
    protected $table = 'employee_gns_division';
    protected $primaryKey = 'id';

    protected $fillable =[
        'gns_division','status'
         
    ];
}
