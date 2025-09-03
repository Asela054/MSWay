<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name', 'code', 'address', 'mobile','ref_no','vat_reg_no','svat_no','bank_account_name','bank_account_number',
        'bank_account_branch_code','employer_number','zone_code'
    ];

    // Append the custom 'location' attribute
    protected $appends = ['location'];

    // Accessor for 'location', maps to 'name'
    public function getLocationAttribute() {
                return $this->attributes['name'];
    }
        
    // Mutator for 'location', sets the 'name' column
    public function setLocationAttribute($value) {
        $this->attributes['name'] = $value;
    }

}
