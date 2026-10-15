<?php

namespace App\ActivityManagement\IOU;

use Illuminate\Database\Eloquent\Model;

class IOUReason extends Model
{
    protected $table = 'iou_reasons';
    protected $fillable = [
        'reason', 'estimate_amount', 'remark', 'status'
    ];
}