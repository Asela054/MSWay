<?php

namespace App\ProductionModule_Opma;

use App\Branch;
use App\Company;
use App\ProductionModule_Opma\MachineEmployee;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    protected $table = 'opma_machines';

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function employees()
    {
        return $this->hasMany(MachineEmployee::class, 'opma_machine_id');
    }
}
