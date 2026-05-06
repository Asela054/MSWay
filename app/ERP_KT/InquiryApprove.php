<?php

namespace App\ERP_KT;

use Illuminate\Database\Eloquent\Model;

class InquiryApprove extends Model
{
    protected $table = 'kt_inquiry_details';

    protected $fillable = [
        'inquiry_id',
        'inquiry',
        'quotation',
        'approve_status'
    ];
}