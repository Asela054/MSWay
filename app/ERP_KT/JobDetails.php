<?php

namespace App\ERP_KT;

use Illuminate\Database\Eloquent\Model;

class JobDetails extends Model
{
    protected $table = 'kt_job_details';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'job_id',
        'emp_id',
        'emp_title',
        'machine_id'
    ];

    public function jobInquiry()
    {
        return $this->belongsTo(JobInquiry::class, 'job_id');
    }
    public function machine()
    {
        return $this->belongsTo(Machine::class, 'machine_id');
    }
}
