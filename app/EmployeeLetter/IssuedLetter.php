<?php

namespace App\EmployeeLetter;

use Illuminate\Database\Eloquent\Model;

class IssuedLetter extends Model
{
    protected $table    = 'issued_letters';
    protected $fillable = [
        'letter_type_id',
        'template_id',
        'employee_id',
        'content',
        'issued_date',
        'issued_by'
    ];
}
