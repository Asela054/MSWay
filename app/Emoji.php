<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Emoji extends Model
{
    protected $table = 'emojies';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'emoji', 
    ];
}
