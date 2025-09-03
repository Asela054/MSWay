<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = ['holiday_name', 'holiday_type', 'date', 'work_level', 'half_short', 'start_time', 'end_time'];

}
