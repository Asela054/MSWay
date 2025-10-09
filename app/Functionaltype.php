<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Functionaltype extends Model
{
    protected $primarykey = 'id';

    protected $fillable =[

        'type','status','created_by', 'updated_by','created_at','updated_at'
    ];
}
