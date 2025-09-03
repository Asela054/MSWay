<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkHour extends Model
{
    protected $table = 'work_hours';
    protected $fillable = [
        'emp_id',
        'date',
        'from',
        'to',
        'working_hours',
        'no_of_days',
        'comment'
    ];

}
