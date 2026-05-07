<?php
namespace App\ERP_KT;

use App\Employee;
use Illuminate\Database\Eloquent\Model;

class MachineOperator extends Model
{
    protected $table = 'kt_machine_operators';

    protected $fillable = [
        'machine_id', 
        'emp_id',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id', 'emp_id');
    }
}