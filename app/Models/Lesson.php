<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
    'zoom_link', 'date', 'start_time', 'topic',
    'course_id', 'teacher_id', 'attendance'
];

    protected $casts = [
        'attendance' => 'array',
        'date' => 'date',
        'start_time' => 'datetime:H:i'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'lesson_user',
            'lesson_id',
            'user_id'
        )->withTimestamps();
    }

    public function students()
    {
        return $this->belongsToMany(
            User::class,
            'user_lessons',
            'lesson_id',
            'user_id'
        )->withTimestamps();
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Получить фактического ведущего: если нет teacher_id, взять преподавателя курса
     */
    public function getInstructorAttribute()
    {
        // Если назначен учитель для конкретного урока — вернуть его, иначе взять первого учителя курса
        return $this->teacher ?: ($this->course->primaryTeacher ?? $this->course->teachers()->first());
    }

    public function homeworks()
    {
        return $this->hasMany(Homework::class, 'lesson_id');
    }

    public function timetable()
    {
        return $this->belongsTo(Timetable::class);
    }

    public function user_lessons() {
        return $this->hasMany(UserLesson::class);
    }

    public function instructor() {
        return $this->belongsTo(User::class, 'instructor_id'); // если есть поле instructor_id
    }

}
