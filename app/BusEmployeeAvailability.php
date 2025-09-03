<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusEmployeeAvailability extends Model
{
    protected $table = 'bus_employees_availability';
    protected $primaryKey = 'id';

    protected $fillable = [
        'emp_id',
        'bus_id', 
        'date', 
        'availability', 
    ];

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'emp_id', 'emp_id');
    }

    public function bus()
    {
        return $this->belongsTo('App\BusLogin', 'bus_id', 'id');
    }
}
