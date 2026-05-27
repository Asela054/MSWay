<?php

namespace App\Training_Management;

use Illuminate\Database\Eloquent\Model;

class TrainingType extends Model
{
    protected $table = 'training_types';
    
    protected $fillable = ['name', 'purpose', 'status'];
}
