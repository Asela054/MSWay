<?php

namespace App\ActivityManagement;

use Illuminate\Database\Eloquent\Model;
use App\Employee;

class VisitPlace extends Model
{
    protected $table = 'visit_places';
    protected $fillable = [
        'emp_id', 'place', 'date', 'time', 'latitude', 'longitude', 'remark', 'status'
    ];

    /**
     * The employee this visit place belongs to.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id', 'emp_id');
    }
}