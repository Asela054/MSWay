<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeePicture extends Model
{
    protected $table = 'employee_pictures';
    protected $primaryKey = 'emp_pic_id'; 
    
    protected $fillable = [
        'emp_id', 
        'emp_pic_picture', 
        'emp_pic_filename', 
        'emp_file_width', 
        'emp_file_height', 
        'insert_user'
    ];
    
    protected $dates = ['created_at', 'updated_at', 'update_date'];
}