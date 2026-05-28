<?php

namespace App\EmployeeLetter;

use Illuminate\Database\Eloquent\Model;

class LetterTemplate extends Model
{
    protected $table    = 'letter_templates';
    protected $fillable = ['letter_type_id', 'name', 'content', 'is_active', 'created_by'];

    public function letterType()
    {
        return $this->belongsTo('App\EmployeeLetter\LetterType', 'letter_type_id');
    }
}