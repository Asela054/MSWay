<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserCompany extends Model
{
    protected $table = 'user_has_companies';

    protected $fillable = ['user_id', 'company_id'];
}