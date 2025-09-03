<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AttendanceClear extends Model
{
    protected $table = 'attendance_clear';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'user_id', 'device_id', 'location_id'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function device()
    {
        return $this->belongsTo('App\FingerprintDevice', 'device_id');
    }

    public function branch()
    {
        return $this->belongsTo('App\Branch', 'location_id');
    }
}


