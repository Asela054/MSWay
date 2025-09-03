<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OccupationGroup extends Model
{
    protected $table = 'occupation_groups';
    protected $fillable = [
        'name',
        'created_by',
        'updated_by',
    ];
}
