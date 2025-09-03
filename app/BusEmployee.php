<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusEmployee extends Model
{
    protected $table = 'bus_employees';
    protected $primaryKey = 'id';

    protected $fillable = [
        'emp_id',
        'bus_id', 
    ];

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'emp_id', 'emp_id');
    }
}
