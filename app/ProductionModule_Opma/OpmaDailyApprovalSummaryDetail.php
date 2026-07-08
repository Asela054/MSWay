<?php

namespace App\ProductionModule_Opma;

use Illuminate\Database\Eloquent\Model;

class OpmaDailyApprovalSummaryDetail extends Model
{
    protected $table = 'opma_daily_approval_summary_detail';

    protected $fillable = [
        'summary_id',
        'machine_id',
        'style_id',
        'target',
        'produced',
        'average',
        'status',
    ];

    public function summary()
    {
        return $this->belongsTo(OpmaDailyApprovalSummary::class, 'summary_id');
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class, 'machine_id');
    }

    public function style()
    {
        return $this->belongsTo(Style::class, 'style_id');
    }
}