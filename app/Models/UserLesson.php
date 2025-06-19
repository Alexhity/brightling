<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLesson extends Model
{
    protected $table = 'user_lessons';

    protected $fillable = [
        'lesson_id',
        'user_id',
    ];
}
