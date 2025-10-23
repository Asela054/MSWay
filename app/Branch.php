<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $table = 'branches';
    protected $primaryKey = 'id';
   protected $fillable = ['id', 'location', 'code', 'epf', 'etf', 'latitude', 'longitude', 'outside_location'];
}
