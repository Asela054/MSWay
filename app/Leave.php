<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $table = 'leaves';

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id', 'emp_id');
    }

    public function covering_employee()
    {
        return $this->belongsTo(Employee::class, 'emp_covering', 'emp_id');
    }

    public function approve_by()
    {
        return $this->belongsTo(Employee::class, 'leave_approv_person', 'emp_id');
    }

}
