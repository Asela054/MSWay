<?php

namespace App\ERP_KT;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'kt_customer';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'contact_number',
        'email',
        'remarks'
    ];
}
