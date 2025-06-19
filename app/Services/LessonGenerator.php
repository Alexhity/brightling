<?php

namespace App\Services;

use App\Models\Timetable;
use App\Models\Lesson;
use Carbon\Carbon;

class LessonGenerator
{
    /**
     * Сгенерировать уроки для одного слота на заданное число недель вперед.
     *
     * @param Timetable $slot
     * @param int $weeks
     */
    public static function generateForSlot(Timetable $slot, int $weeks = 12): void
    {
        if (! $slot->active || ! $slot->course_id) {
            return;
        }

        // Находим ближайшую дату этого дня недели
        $map = ['воскресенье'=>0,'понедельник'=>1,'вторник'=>2,'среда'=>3,'четверг'=>4,'пятница'=>5,'суббота'=>6];
        $today = Carbon::today();
        $dow   = $map[$slot->weekday];
        $first = $today->dayOfWeek === $dow
            ? $today
            : $today->copy()->next($dow);

        for ($i = 0; $i < $weeks; $i++) {
            $date = $first->copy()->addWeeks($i)->toDateString();

            Lesson::firstOrCreate(
                [
                    'timetable_id' => $slot->id,
                    'date'         => $date,
                    'time'         => $slot->start_time,
                ],
                [
                    'course_id'  => $slot->course_id,
                    'teacher_id' => $slot->instructor->id ?? null,
                    'status'     => 'scheduled',
                ]
            );
        }
    }
}
