<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyHierarchy extends Model
{
    protected $table = 'company_hierarchies';

    protected $primaryKey = 'id';
    
    protected $fillable = ['order_number','position'];
}
