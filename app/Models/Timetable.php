<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{


    protected $fillable = [
        'parent_id',
        'course_id',
        'title',
        'user_id',
        'type',
        'weekday',
        'date',
        'active',
        'ends_at',
        'start_time',
        'duration',
        'override_start_time',
        'override_duration',
        'override_user_id',
        'cancelled',
        'request_id',
    ];

    protected $casts = [
        'date'              => 'date',
        'start_time'        => 'datetime:H:i',
        'enrollment_status' => 'datetime',
        'active'            => 'boolean',
    ];

    // Слоты регулярные: date = null
    public function isRegular(): bool
    {
        return is_null($this->date);
    }

    // Возвращает реальную дату слота (для регулярных — ближайшее совпадение после $from)
    public function getNextOccurrence(\Carbon\Carbon $from)
    {
        if ($this->date) {
            return $this->date;
        }
        // регулярный: ищем ближайшую дату с тем же weekday
        $weekdayNames = [
            'понедельник' => 1, 'вторник' => 2, 'среда' => 3,
            'четверг' => 4, 'пятница' => 5, 'суббота' => 6, 'воскресенье' => 7,
        ];
        $target = $weekdayNames[$this->weekday] ?? null;
        if (!$target) return null;
        return $from->copy()->next($target);
    }

    // Связь: этот слот «принадлежит» курсу (или null)
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Преподаватель слота
    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Ученики, записанные на этот слот
    public function students()
    {
        return $this->belongsToMany(
            User::class,
            'timetable_user',
            'timetable_id',
            'user_id'
        )->withTimestamps();
    }

    // Связь: если этот слот был создан из заявки
    public function freeRequest()
    {
        return $this->belongsTo(FreeLessonRequest::class, 'request_id');
    }

    // Связь: конкретные уроки (если вы будете генерировать Lesson из Timetable)
    /**
     * Уроки, сгенерированные по этому слоту
     */
    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'timetable_id');
    }

    // Расписание

    // Базовый слот → его исключения
    public function exceptions()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

// Исключение → родительский слот
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }


// Преподаватель‑override для исключения
    public function overrideTeacher()
    {
        return $this->belongsTo(User::class, 'override_user_id');
    }

    public function actualOn(\Carbon\Carbon $date)
    {
        if ($this->parent_id && $this->date === $date->toDateString()) {
            // это исключение
            return [
                'start_time' => $this->override_start_time ?? $this->start_time,
                'duration'   => $this->override_duration   ?? $this->duration,
                'user_id'    => $this->override_user_id    ?? $this->user_id,
                'cancelled'  => $this->cancelled,
            ];
        }
        // базовый слот
        return [
            'start_time' => $this->start_time,
            'duration'   => $this->duration,
            'user_id'    => $this->user_id,
            'cancelled'  => false,
        ];
    }


}
