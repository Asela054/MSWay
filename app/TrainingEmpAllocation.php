<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrainingEmpAllocation extends Model
{
    protected $table = 'training_emp_allocations';
    
    protected $fillable = [
        'allocation_id',
        'emp_id',
        'marks',
        'remarks',
        'is_attend',
        'status',
        'created_by',
        'updated_by'
    ];
}
