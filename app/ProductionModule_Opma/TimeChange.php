<?php

namespace App\ProductionModule_Opma;

use Illuminate\Database\Eloquent\Model;

class TimeChange extends Model
{
    protected $table = 'opma_machine_downtime';

    protected $primaryKey = 'id';

    protected $fillable = [
        'date','type_id','machine_id', 'fromtime', 'totime','status','created_by', 'updated_by'
    ];
}
