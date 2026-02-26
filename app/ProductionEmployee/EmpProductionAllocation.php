<?php

namespace App\ProductionEmployee;

use Illuminate\Database\Eloquent\Model;

class EmpProductionAllocation extends Model
{
    protected $table = 'emp_production_allocation';

    protected $fillable = [
        'date',
        'department_id',
        'emp_id',
        'status',
        'created_by',
        'updated_by',
    ];

    public function employee()
    {
        return $this->belongsTo(\App\Employee::class, 'emp_id', 'emp_id');
    }
}