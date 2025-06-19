<?php

namespace App\Observers;

use App\Models\Timetable;
use App\Services\LessonGenerator;

class TimetableObserver
{
    /**
     * Handle the Timetable "created" event.
     */
    public function created(Timetable $timetable): void
    {
        // Генерируем уроки на 12 недель вперед
        LessonGenerator::generateForSlot($timetable, 12);
    }

    /**
     * Handle the Timetable "updated" event.
     */
    public function updated(Timetable $timetable): void
    {
        // Удаляем все будущие уроки этого слота
        $timetable->lessons()
            ->where('date', '>=', now()->toDateString())
            ->delete();

        // Генерируем заново
        LessonGenerator::generateForSlot($timetable, 12);
    }

    /**
     * Handle the Timetable "deleted" event.
     */
    public function deleted(Timetable $timetable): void
    {
        $timetable->lessons()->delete();
    }

    public function deleting(Timetable $timetable): void
    {
        // Удаляем все уроки, связанные с этим слотом
        $timetable->lessons()->delete();
    }

    /**
     * Handle the Timetable "restored" event.
     */
    public function restored(Timetable $timetable): void
    {
        //
    }

    /**
     * Handle the Timetable "force deleted" event.
     */
    public function forceDeleted(Timetable $timetable): void
    {
        //
    }
}
