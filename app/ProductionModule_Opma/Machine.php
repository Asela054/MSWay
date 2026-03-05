<?php

namespace App\ProductionModule_Opma;

use App\Branch;
use App\Company;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    protected $table = 'machines';

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
