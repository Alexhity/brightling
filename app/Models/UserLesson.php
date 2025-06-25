<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLesson extends Model
{
    protected $table = 'lesson_user';

    protected $fillable = [
        'lesson_id', 'user_id', 'status', 'mark'
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
