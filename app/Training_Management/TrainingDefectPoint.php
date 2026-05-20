<?php
namespace App\Training_Management;
use Illuminate\Database\Eloquent\Model;

class TrainingDefectPoint extends Model
{
    protected $table = 'training_defect_points';
    protected $fillable = ['allocation_id', 'session_id', 'type_id', 'emp_id', 'points', 'created_by', 'updated_by'];
}