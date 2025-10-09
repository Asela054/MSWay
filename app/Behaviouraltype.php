<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Behaviouraltype extends Model
{
    protected $primarykey = 'id';

    protected $fillable =[

        'type','description','status','created_by', 'updated_by','created_at','updated_at'
    ];
}
