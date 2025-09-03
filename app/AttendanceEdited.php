<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AttendanceEdited extends Model
{
    protected $table = 'attendance_edits';

    protected $fillable = [
        'emp_id',
        'date',
        'prev_val',
        'new_val',
        'edited_user_id',
        'attendance_id'
    ];
}
