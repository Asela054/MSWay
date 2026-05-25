<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignedDevice extends Model
{
    protected $table = 'assigned_devices';

    protected $primaryKey = 'id';
    
    protected $fillable = ['device_name', 'remarks'];
}
