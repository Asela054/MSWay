<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyBankDetail extends Model
{
    protected $table = 'company_bank_details';
    protected $primaryKey = 'id';

    protected $fillable = [
        'company_id', 'bank_code', 'branch_code', 'bank_account_number', 'bank_account_name'
    ];
}
