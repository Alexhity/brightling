<?php

namespace App\Observers;

use App\Models\Timetable;
use App\Services\LessonGenerator;

class TimetableObserver
{
    public function created(Timetable $timetable): void
    {
        LessonGenerator::generateAll(16);
    }

    public function updated(Timetable $timetable): void
    {
        // Удаляем будущие уроки данного слота
        $timetable->lessons()
            ->where('date', '>=', now()->toDateString())
            ->delete();

        LessonGenerator::generateAll(4);
    }

    public function deleting(Timetable $timetable): void
    {
        // Удаляем связанные уроки
        $timetable->lessons()->delete();
    }
}

