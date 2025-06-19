<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Models\Language;
use App\Models\Price;
use App\Models\Timetable;


class Course extends Model
{
    protected $fillable = [
        'title',
        'description',
        'format',
        'timetable',
        'age_group',
        'lessons_count',
        'level',
        'duration',
        'status',
        'language_id',
        'price_id',
    ];


    protected $casts = [
        'timetable' => 'array',
        'duration'   => 'date',     // приводим к Carbon
        'created_at' => 'datetime',
        'updated_at' => 'datetime',

    ];

    public function language()
    {
        return $this->belongsTo(Language::class, 'language_id');
    }

    public function price()
    {
        return $this->belongsTo(Price::class, 'price_id');
    }

    /**
     * Все пользователи на курсе (и преподаватели, и студенты).
     */
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role');  // чтобы pivot->role был доступен
    }

    /**
     * Только преподаватели.
     */
    public function teachers()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->wherePivot('role', 'teacher');
    }

    /**
     * Только студенты.
     */
    public function students()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->wherePivot('role', 'student');
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'course_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'course_id');
    }

    public function timetables()
    {
        return $this->hasMany(Timetable::class);
    }





}
