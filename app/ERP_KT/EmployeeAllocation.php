<?php

namespace App\ERP_KT;

use Illuminate\Database\Eloquent\Model;

class EmployeeAllocation extends Model
{
    protected $table = 'kt_shift_ot';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'shift_id',
        'emp_id',
        'date',
        'in_time',
        'out_time',
        'ot_hours',
        'approve_status',
    ];
}
