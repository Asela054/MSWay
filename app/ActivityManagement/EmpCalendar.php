<?php

namespace App\ActivityManagement;

use Illuminate\Database\Eloquent\Model;
use App\Employee;

class EmpCalendar extends Model
{
    protected $table = 'emp_calendar';
    protected $fillable = [
        'emp_id', 'title', 'date', 'time', 'remark', 'status'
    ];

    /**
     * The employee this employee calendar belongs to.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id', 'emp_id');
    }
}