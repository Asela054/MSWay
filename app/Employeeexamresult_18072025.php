<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employeeexamresult extends Model
{
    protected $table = 'employee_exam_results';
    protected $primaryKey = 'id';

    protected $fillable =[
        'emp_id','exam_type','subject_id','grade','status'
    ];
}
