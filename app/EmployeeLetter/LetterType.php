<?php

namespace App\EmployeeLetter;

use Illuminate\Database\Eloquent\Model;

class LetterType extends Model
{
    protected $table = 'letter_types';

    protected $fillable = ['letter_type', 'remarks'];

    public function letterTemplates()
    {
        return $this->hasMany(LetterTemplate::class, 'letter_type_id');
    }
}
