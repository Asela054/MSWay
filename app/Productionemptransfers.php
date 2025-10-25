<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Productionemptransfers extends Model
{
    protected $table = 'production_emp_tranfer_records';

    protected $primaryKey = 'id';

    protected $fillable = [
        'production_id','allocation_detailed_id','attendance_record_id', 'current_qty', 'status'
    ];
}
