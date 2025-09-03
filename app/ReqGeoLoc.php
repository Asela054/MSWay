<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReqGeoLoc extends Model
{
    use softDeletes;
    protected $table = 'request_geo_location';
    protected $primaryKey = 'id';
    protected $fillable = [
        'emp_id', 'latitude', 'longitude'
    ];

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'emp_id', 'emp_id');
    }

}
