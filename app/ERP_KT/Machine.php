<?php

namespace App\ERP_KT;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    protected $table = 'kt_machines';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'machine_name',
        'machine_type',
        'operator_id',
        'helper_rate',
        'operator_rate',
        'status',
        'date',
        'remarks',
    ];
}