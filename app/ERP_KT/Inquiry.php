<?php

namespace App\ERP_KT;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    protected $table = 'kt_inquiries';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'customer_id',
        'date',
        'remarks'
    ];
}