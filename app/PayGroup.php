<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PayGroup extends Model
{
    protected $table = 'user_has_pay_groups';

    protected $primaryKey = 'id';
    
    protected $fillable = ['user_id','group_id'];
    
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}