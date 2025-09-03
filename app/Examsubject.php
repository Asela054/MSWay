<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Examsubject extends Model
{
    protected $table = 'exam_subjects';
    protected $primaryKey = 'id';

    protected $fillable =[
        'exam_type','subject','status',
         'created_by', 'updated_by'
    ];
}
