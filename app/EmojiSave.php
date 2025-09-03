<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmojiSave extends Model
{
    protected $table = 'emoji_save';
    protected $primaryKey = 'id';

    protected $fillable = [
        'emoji_id',
        'date',
    ];
}
