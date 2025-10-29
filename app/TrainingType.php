<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrainingType extends Model
{
    protected $table = 'training_types';
    
    protected $fillable = ['name', 'purpose', 'status'];
}
