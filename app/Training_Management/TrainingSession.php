<?php
namespace App\Training_Management;
use Illuminate\Database\Eloquent\Model;

class TrainingSession extends Model
{
    protected $table = 'training_sessions';
    protected $fillable = ['allocation_id', 'session_name', 'start_time', 'end_time', 'trainer_id', 'created_by', 'updated_by'];
}