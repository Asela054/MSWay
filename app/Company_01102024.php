<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name', 'code', 'address', 'mobile','ref_no','vat_reg_no','svat_no'
    ];

}
