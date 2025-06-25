<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FreeLessonRequest extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'status',
        'requested_role',
        'language_id',
        'lesson_id',
    ];


    //    Отношения

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function timetable()
    {
        return $this->belongsTo(Timetable::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

}
