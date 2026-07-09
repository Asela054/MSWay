<?php

namespace App\ProductionModule_Opma;

use Illuminate\Database\Eloquent\Model;

class OpmaDailyApprovalSummary extends Model
{
    protected $table = 'opma_daily_approval_summary';

    protected $fillable = [
        'emp_id',
        'department_id',
        'date',
        'on_time',
        'off_time',
        'late_minites',
        'normal_ot',
        'daily_target',
        'daily_produce',
        'daily_average',
        'target_bonus',
        'status',
    ];

    public function details()
    {
        return $this->hasMany(OpmaDailyApprovalSummaryDetail::class, 'summary_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}