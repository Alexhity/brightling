<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Timetable;
use App\Models\Lesson;
use Carbon\Carbon;

class GenerateLessons extends Command
{
    protected $signature = 'lessons:generate {weeks=12}';
    protected $description = 'Сгенерировать уроки на заданное количество недель вперёд';

    public function handle()
    {
        $weeks = (int)$this->argument('weeks');
        $slots = Timetable::where('active', true)
            ->whereNotNull('course_id')
            ->get();

        foreach ($slots as $slot) {
            $firstDate = $this->getNextDateForRussianWeekday($slot->weekday);
            for ($i = 0; $i < $weeks; $i++) {
                $date = Carbon::parse($firstDate)->addWeeks($i)->toDateString();
                Lesson::firstOrCreate(
                    ['timetable_id' => $slot->id, 'date' => $date, 'time' => $slot->start_time],
                    [
                        'course_id'  => $slot->course_id,
                        'teacher_id' => $slot->instructor->id ?? null,
                        'status'     => 'scheduled'
                    ]
                );
            }
        }
        $this->info("Уроки на {$weeks} недель вперед сгенерированы.");
    }

    private function getNextDateForRussianWeekday($russianDay)
    {
        $map = ['воскресенье'=>0,'понедельник'=>1,'вторник'=>2,'среда'=>3,'четверг'=>4,'пятница'=>5,'суббота'=>6];
        $today = Carbon::now();
        $dow = $map[$russianDay];
        if ($today->dayOfWeek == $dow) return $today->toDateString();
        return $today->copy()->next($dow)->toDateString();
    }
}
