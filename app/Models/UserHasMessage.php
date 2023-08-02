<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserHasMessage extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'user_has_message';

    protected $fillable = [
        'user',
        'message',
        'readed',
        'readed_at'
    ];
}
