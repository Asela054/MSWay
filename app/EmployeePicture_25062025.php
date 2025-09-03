<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeePicture extends Model
{
    protected  $primaryKey = 'emp_pic_id';
    protected $table = 'employee_pictures';

    protected $fillable = [
        'emp_id', 'emp_pic_filename'
    ];

}
