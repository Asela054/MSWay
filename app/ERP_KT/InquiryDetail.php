<?php

namespace App\ERP_KT;

use Illuminate\Database\Eloquent\Model;

class InquiryDetail extends Model
{
    protected $table = 'kt_inquiry_details';

    protected $fillable = [
        'inquiry_id',
        'inquiry',
        'quotation'
    ];
}