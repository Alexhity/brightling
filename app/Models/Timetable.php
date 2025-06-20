<?php

namespace App\Models;

use Carbon\Carbon;
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
        'issued_at'              => 'date',
        'start_time'        => 'datetime:H:i',
        'enrollment_status' => 'datetime',
        'active'            => 'boolean',
    ];

    const TYPE_GROUP = 'group';
    const TYPE_INDIVIDUAL = 'individual';
    const TYPE_TEST = 'test';


    public static function lessonTypes()
    {
        return [
            self::TYPE_GROUP => 'Групповое',
            self::TYPE_INDIVIDUAL => 'Индивидуальное',
            self::TYPE_TEST => 'Тестовый урок',
        ];
    }

    public function scopeInWeek($q, Carbon $startOfWeek, Carbon $endOfWeek)
    {
        return $q->where(function($q) use($startOfWeek, $endOfWeek) {
            // одноразовые
            $q->whereNotNull('date')
                ->whereBetween('date', [$startOfWeek, $endOfWeek])
                // регулярные
                ->orWhere(function($q2) use($startOfWeek, $endOfWeek) {
                    $q2->whereNull('date')
                        ->whereHas('course', fn($qc) => $qc
                            ->where('created_at','<=',$endOfWeek)
                            ->where(fn($q3) => $q3->whereNull('duration')
                                ->orWhere('duration','>=',$startOfWeek))
                        );
                });
        })
            ->whereDoesntHave('exceptions', fn($q) =>
            $q->whereBetween('date', [$startOfWeek,$endOfWeek])
            )
            ->orWhereHas('parent', fn($q) =>
            $q->whereBetween('date', [$startOfWeek,$endOfWeek])
            );
    }

    public function scopeActive($q)
    {
        return $q->where('active', true)->where('cancelled', false);
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function isTestLesson()
    {
        return $this->type === self::TYPE_TEST;
    }

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

    public function overrideTeacher()
    {
        return $this->belongsTo(User::class, 'override_user_id');
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

    // Аксессоры для удобства
    public function getEffectiveTeacherAttribute()
    {
        return $this->override_user_id ? User::find($this->override_user_id) : $this->teacher;
    }

    public function getIsExceptionAttribute()
    {
        return !is_null($this->parent_id);
    }

    public function getIsCancelledAttribute()
    {
        return $this->cancelled;
    }

    public function getDisplayDateAttribute()
    {
        if ($this->date) {
            return $this->date;
        }

        if ($this->weekday) {
            $weekdayMap = [
                'понедельник' => Carbon::MONDAY,
                'вторник' => Carbon::TUESDAY,
                'среда' => Carbon::WEDNESDAY,
                'четверг' => Carbon::THURSDAY,
                'пятница' => Carbon::FRIDAY,
                'суббота' => Carbon::SATURDAY,
                'воскресенье' => Carbon::SUNDAY,
            ];

            $dayOfWeek = $weekdayMap[$this->weekday] ?? null;

            if ($dayOfWeek) {
                return Carbon::now()->next($dayOfWeek);
            }
        }

        return null;
    }



//// Преподаватель‑override для исключения
//    public function overrideTeacher()
//    {
//        return $this->belongsTo(User::class, 'override_user_id');
//    }
//
//    public function actualOn(\Carbon\Carbon $date)
//    {
//        if ($this->parent_id && $this->date === $date->toDateString()) {
//            // это исключение
//            return [
//                'start_time' => $this->override_start_time ?? $this->start_time,
//                'duration'   => $this->override_duration   ?? $this->duration,
//                'user_id'    => $this->override_user_id    ?? $this->user_id,
//                'cancelled'  => $this->cancelled,
//            ];
//        }
//        // базовый слот
//        return [
//            'start_time' => $this->start_time,
//            'duration'   => $this->duration,
//            'user_id'    => $this->user_id,
//            'cancelled'  => false,
//        ];
//    }


}
