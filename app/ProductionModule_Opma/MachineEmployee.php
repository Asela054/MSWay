<?php

namespace App\ProductionModule_Opma;

use App\Employee;
use Illuminate\Database\Eloquent\Model;

class MachineEmployee extends Model
{
    protected $table = 'opma_machine_employees';
    protected $fillable = ['opma_machine_id', 'emp_id'];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id', 'emp_id');
    }
}