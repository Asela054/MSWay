<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    public function country()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function attachments()
    {
        return $this->hasMany(EmployeeAttachment::class, 'emp_id', 'emp_id');
    }
}
