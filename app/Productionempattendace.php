<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Productionempattendace extends Model
{
    protected $table = 'production_emp_attendance';

    protected $primaryKey = 'id';

    protected $fillable = [
        'emp_id','production_id','date', 'start_timestmp', 'finish_timestamp','status','created_by', 'updated_by'
    ];
}
