<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeAbsent extends Model
{
    use softDeletes;
    protected $table = 'employee_absents';

    protected $fillable = [
        'emp_id',
        'from_date',
        'to_date',
    ];

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'emp_id', 'emp_id');
    }

}
