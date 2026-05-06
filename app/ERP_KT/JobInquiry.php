<?php

namespace App\ERP_KT;

use Illuminate\Database\Eloquent\Model;

class JobInquiry extends Model
{
    protected $table = 'kt_job_inquiry';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'customer_id',
        'inquiry_id',
        'start_from',
        'end_at',
        'reading_hours',
        'job_description',
        'remarks'
    ];

     public function customer() {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function inquiry() {
        return $this->belongsTo(InquiryDetail::class, 'inquiry_id');
    }
    public function details()
    {
        return $this->hasMany(JobDetails::class, 'job_id');
    }
}
