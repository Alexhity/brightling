<?php

namespace App\Http\Controllers;

use App\Models\Timetable;
use Illuminate\Http\Request;
use App\Models\Lesson;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class StudentTimetableController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::user();
        $startOfWeek = $request->query('week_start')
            ? Carbon::parse($request->query('week_start'))->startOfWeek()
            : Carbon::now()->startOfWeek();

        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        // Получаем активные курсы студента
        $courses = $student->courses()->get();
        $courseIds = $courses->pluck('id')->toArray();

        // Получаем все слоты для курсов студента
        $slots = Timetable::with(['course', 'teacher', 'overrideTeacher'])
            ->whereIn('course_id', $courseIds)
            ->where('active', true)
            ->where('cancelled', false)
            ->get();

        // Фильтруем слоты по дате курса
        $filteredSlots = $slots->filter(function($slot) use ($courses, $startOfWeek, $endOfWeek) {
            $course = $courses->firstWhere('id', $slot->course_id);

            // Если курс не найден, исключаем слот
            if (!$course) return false;

            $courseStart = $course->created_at->startOfDay();
            $courseEnd = $course->duration ? Carbon::parse($course->duration)->endOfDay() : null;

            // Для разовых занятий
            if ($slot->date) {
                $slotDate = Carbon::parse($slot->date);

                // Проверяем попадает ли в период курса и текущую неделю
                $afterStart = $slotDate->gte($courseStart);
                $beforeEnd = $courseEnd ? $slotDate->lte($courseEnd) : true;
                $inCurrentWeek = $slotDate->between($startOfWeek, $endOfWeek);

                return $afterStart && $beforeEnd && $inCurrentWeek;
            }
            // Для регулярных занятий
            else {
                $weekdayMap = [
                    'понедельник' => 0,
                    'вторник' => 1,
                    'среда' => 2,
                    'четверг' => 3,
                    'пятница' => 4,
                    'суббота' => 5,
                    'воскресенье' => 6,
                ];

                if (!isset($weekdayMap[$slot->weekday])) {
                    return false;
                }

                // Вычисляем дату слота на текущей неделе
                $dayIndex = $weekdayMap[$slot->weekday];
                $slotDate = $startOfWeek->copy()->addDays($dayIndex);

                // Проверяем попадает ли дата в период курса
                $afterStart = $slotDate->gte($courseStart);
                $beforeEnd = $courseEnd ? $slotDate->lte($courseEnd) : true;

                return $afterStart && $beforeEnd;
            }
        });

        // Группируем слоты по дням с исправлением для понедельника
        $groupedSlots = $this->groupSlotsByWeek($filteredSlots, $startOfWeek);

        $days = collect(range(0, 6))
            ->map(fn($i) => $startOfWeek->copy()->addDays($i));

        return view('auth.student.timetables.index', [
            'student' => $student,
            'startOfWeek' => $startOfWeek,
            'endOfWeek' => $endOfWeek,
            'groupedSlots' => $groupedSlots,
            'days' => $days,
            'slots' => $filteredSlots
        ]);
    }

    protected function groupSlotsByWeek($slots, $startOfWeek)
    {
        $grouped = [];

        $weekdayMap = [
            'понедельник' => 0,
            'вторник' => 1,
            'среда' => 2,
            'четверг' => 3,
            'пятница' => 4,
            'суббота' => 5,
            'воскресенье' => 6,
        ];

        foreach ($slots as $slot) {
            $date = null;

            // Для разовых занятий
            if ($slot->date) {
                $date = $slot->date->toDateString();
            }
            // Для регулярных занятий
            elseif ($slot->weekday && isset($weekdayMap[$slot->weekday])) {
                $dayIndex = $weekdayMap[$slot->weekday];
                $date = $startOfWeek->copy()->addDays($dayIndex)->toDateString();
            }

            if ($date) {
                if (!isset($grouped[$date])) {
                    $grouped[$date] = [];
                }
                $grouped[$date][] = $slot;
            }
        }

        return collect($grouped);
    }
}
