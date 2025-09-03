<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeTransfer extends Model
{
    use softDeletes;
    protected $table = 'employee_transfers';
    protected $primaryKey = 'id';

    protected $fillable = [
        'emp_id',
        'registered_location_id',
        'transfer_location_id',
        'record_date',
        'in_time',
        'out_time',
        'charge_per_hour',
        'is_approved',
        'approved_at',
        'approved_by',
    ];

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'emp_id', 'emp_id');
    }

    public function registered_location()
    {
        return $this->belongsTo('App\Branch', 'registered_location_id', 'id');
    }

    public function transfer_location()
    {
        return $this->belongsTo('App\Branch', 'transfer_location_id', 'id');
    }

    public function approved_by()
    {
        return $this->belongsTo('App\User', 'approved_by', 'id');
    }
}
