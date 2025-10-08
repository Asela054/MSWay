<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kpiyear extends Model
{
    protected $primarykey = 'id';

    protected $fillable =[

        'year','description','status','created_by', 'updated_by','created_at','updated_at'
    ];
}
