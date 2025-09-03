<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusLogin extends Model
{
    protected $table = 'bus_logins';
    protected $primaryKey = 'id';

    protected $fillable = [
        'bus_no',
        'password', 
    ];
}
