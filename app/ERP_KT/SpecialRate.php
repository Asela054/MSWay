<?php

namespace App\ERP_KT;

use Illuminate\Database\Eloquent\Model;

class SpecialRate extends Model
{
    protected $table = 'kt_special_rate';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'job-title',
        'employee',
        'rate',
        'remarks'
    ];
}
