<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaveAprLevel extends Model
{
    protected $table    = 'leave_approval_levels';
    protected $fillable = ['level'];
}
