<?php

namespace App\ActivityManagement\IOU;

use Illuminate\Database\Eloquent\Model;
use App\ActivityManagement\IOU\IOUReason;
use App\Employee;

class IOURequest extends Model
{
    protected $table = 'iou_requests';
    protected $fillable = [
        'reason_id', 'emp_id', 'amount', 'remark', 'status'
    ];

    /**
     * The IOU reason this request belongs to.
     */
    public function reason()
    {
        return $this->belongsTo(IOUReason::class, 'reason_id', 'id');
    }

    /**
     * The employee this request belongs to.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id', 'emp_id');
    }
}