<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShiftChangeLog extends Model
{
     protected $table = 'roster_shift_log';
     protected $fillable = [
        'emp_id',
        'work_date',
        'old_shift_id',
        'new_shift_id',
        'changed_by',
    ];
}
