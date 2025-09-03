<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{

    protected $table = 'employees';
    protected $fillable = [
        'emp_id',
        'emp_etfno',
        'emp_first_name',
        'emp_med_name',
        'emp_last_name',
        'emp_fullname',
        'emp_name_with_initial',
        'calling_name',
        'emp_national_id',
        'emp_mobile',
        'emp_birthday',
        'emp_address',
        'emp_join_date',
        'emp_department',
        'emp_status',
    ];

    public function country()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function attachments()
    {
        return $this->hasMany(EmployeeAttachment::class, 'emp_id', 'emp_id');
    }
}
