<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Routes extends Model
{
    use softDeletes;
    protected $table = 'routes';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name', 'from', 'to', 'vehicle_type', 'seating_capacity'
    ];

    public function emp_route()
    {
        return $this->hasOne('App\EmployeeRoute', 'id', 'route_id');
    }

    public function vehicle_type_rel()
    {
        return $this->hasOne('App\VehicleType', 'id', 'vehicle_type');
    }

}
