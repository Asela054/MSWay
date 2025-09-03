<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobCategory extends Model
{
    protected $table = 'job_categories';
    //protected $fillable = ['id', 'category', 'short_leave_enabled' , 'annual_leaves', 'casual_leaves', 'medical_leaves', 'normal_ot_rate', 'double_ot_rate', 'no_pay_rate_per_hour', 'no_pay_rate_per_day'];
}

