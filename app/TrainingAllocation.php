<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrainingAllocation extends Model
{
    protected $table = 'training_allocations';
    
    protected $fillable = [
        'type_id',
        'venue',
        'start_time',
        'end_time',
        'status',
        'created_by',
        'updated_by'
    ];
}
