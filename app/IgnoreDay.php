<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IgnoreDay extends Model
{
    protected $table = 'ignore_days';
    protected $primaryKey = 'id';
    protected $fillable = ['month','date'];
}
