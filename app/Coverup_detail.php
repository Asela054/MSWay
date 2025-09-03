<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coverup_detail extends Model
{
    protected $table = 'coverup_details';

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id', 'emp_id');
    }

    public function covering_employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id', 'emp_id');
    }

}
