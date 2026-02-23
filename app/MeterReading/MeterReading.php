<?php

namespace App\MeterReading;

use Illuminate\Database\Eloquent\Model;

class MeterReading extends Model
{
    protected $table = 'meter_reading_count';

    protected $fillable = [
        'emp_id',
        'date',
        'count',
        'status',
        'approve_status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'emp_id', 'emp_id');
    }
}
