<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeAvailability extends Model
{
    protected $table = 'employee_availability';

    protected $fillable = [
        'emp_id',
        'date',
        'session',
    ];

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'emp_id', 'emp_id');
    }

}
