<?php

namespace App\Services;

use App\Models\Timetable;
use App\Models\Lesson;
use Carbon\Carbon;


class LessonGenerator
{
    /**
     * Генерировать уроки для всех активных базовых слотов.
     *
     * @param int $weeks Количество недель вперёд для регулярных слотов
     */
    public static function generateAll(int $weeks = 16): void
    {
        $today   = Carbon::today();
        $endDate = $today->copy()->addWeeks($weeks);

        // — Берём только основные слоты (у них parent_id = null)
        $slots = Timetable::where('active', true)
            ->whereNull('parent_id')            // <<< добавлено
            ->where(function ($q) use ($today, $endDate) {
                $q->whereNotNull('date')
                    ->whereBetween('date', [$today, $endDate])
                    ->orWhereNull('date');
            })
            ->get();

        foreach ($slots as $slot) {
            static::processSlot($slot, $today, $endDate);
        }
    }

    /**
     * Обработать один слот: собрать даты и завести уроки.
     */
    protected static function processSlot(Timetable $slot, Carbon $start, Carbon $endDate): void
    {
        $slotEnd = $slot->ends_at
            ? Carbon::parse($slot->ends_at)
            : $endDate;

        if ($slotEnd->lt($start)) {
            return;
        }

        $realEnd = $slotEnd->lte($endDate) ? $slotEnd : $endDate;

        $map = [
            'понедельник' => Carbon::MONDAY,
            'вторник'     => Carbon::TUESDAY,
            'среда'       => Carbon::WEDNESDAY,
            'четверг'     => Carbon::THURSDAY,
            'пятница'     => Carbon::FRIDAY,
            'суббота'     => Carbon::SATURDAY,
            'воскресенье' => Carbon::SUNDAY,
        ];

        if ($slot->date) {
            $d     = Carbon::parse($slot->date);
            if ($d->gte($start) && $d->lte($realEnd)) {
                $dates[] = $d;
            }
        } else {
            $dow   = $map[$slot->weekday] ?? $start->dayOfWeek;
            $delta = ($dow - $start->dayOfWeek + 7) % 7;
            $first = $start->copy()->addDays($delta);

            $dates = [];
            for ($d = $first; $d->lte($realEnd); $d->addWeek()) {
                $dates[] = $d->copy();
            }
        }

        foreach ($dates as $date) {
            static::makeLessonForDate($slot, $date);
        }
    }

    /**
     * Создать или обновить урок для слота и даты с учётом отмен и замен.
     */
    public static function makeLessonForDate(Timetable $slot, Carbon $date): void
    {
        $dateStr = $date->toDateString();
        $time    = $slot->start_time;

        // --- 1) Проверяем отмену ---
        $cancelEx = Timetable::where('parent_id', $slot->id)
            ->where('date', $dateStr)
            ->where('cancelled', true)
            ->exists();
        if ($cancelEx) {
            // обновляем/создаём отменённый урок на базовом слоте
            Lesson::updateOrCreate([
                'timetable_id' => $slot->id,
                'date'         => $dateStr,
                'time'         => $time,
            ], [
                'course_id'  => $slot->course_id,
                'teacher_id' => $slot->user_id,
                'status'     => 'cancelled',
                'type'       => $slot->type,
            ]);
            // Удаляем возможный урок на override-слоте
            Timetable::where('parent_id', $slot->id)
                ->where('date', $dateStr)
                ->pluck('id')
                ->each(fn($ovId) => Lesson::where([
                    ['timetable_id', $ovId],
                    ['date', $dateStr],
                    ['time', $time]
                ])->delete());
            return;
        }

        // --- 2) Проверяем override (замену преподавателя) ---
        $override = Timetable::where('parent_id', $slot->id)
            ->where('date', $dateStr)
            ->where('cancelled', false)
            ->first();

        if ($override) {
            $lessonSlotId = $override->id;
            $teacherId    = $override->override_user_id;
        } else {
            $lessonSlotId = $slot->id;
            $teacherId    = $slot->user_id;
        }

        // --- 3) Создаём/обновляем урок для выбранного slot_id ---
        Lesson::updateOrCreate([
            'timetable_id' => $lessonSlotId,
            'date'         => $dateStr,
            'time'         => $time,
        ], [
            'course_id'  => $slot->course_id,
            'teacher_id' => $teacherId,
            'status'     => 'scheduled',
            'type'       => $slot->type,
        ]);

        // --- 4) Удаляем «лишнюю» запись на другом слоте ---
        $otherSlotId = ($lessonSlotId === $slot->id)
            ? optional($override)->id
            : $slot->id;
        if ($otherSlotId) {
            Lesson::where([
                ['timetable_id', $otherSlotId],
                ['date', $dateStr],
                ['time', $time]
            ])->delete();
        }
    }
}
