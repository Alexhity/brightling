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
        $filteredSlots = $slots->filter(function($slot) use ($courses, $startOfWeek) {
            $course = $courses->firstWhere('id', $slot->course_id);

            // Если курс не найден, исключаем слот
            if (!$course) return false;

            $courseStart = $course->created_at->startOfDay();
            $courseEnd = $course->duration ? Carbon::parse($course->duration)->endOfDay() : null;

            // Для разовых занятий
            if ($slot->date) {
                $slotDate = Carbon::parse($slot->date);

                // Проверяем попадает ли дата в период курса
                $afterStart = $slotDate->gte($courseStart);
                $beforeEnd = $courseEnd ? $slotDate->lte($courseEnd) : true;

                return $afterStart && $beforeEnd;
            }
            // Для регулярных занятий
            else {
                $weekdayMap = [
                    'понедельник' => Carbon::MONDAY,
                    'вторник' => Carbon::TUESDAY,
                    'среда' => Carbon::WEDNESDAY,
                    'четверг' => Carbon::THURSDAY,
                    'пятница' => Carbon::FRIDAY,
                    'суббота' => Carbon::SATURDAY,
                    'воскресенье' => Carbon::SUNDAY,
                ];

                if (!isset($weekdayMap[$slot->weekday])) {
                    return false;
                }

                // Вычисляем дату слота на текущей неделе
                $slotDate = $startOfWeek->copy()->next($weekdayMap[$slot->weekday])->startOfDay();

                // Проверяем попадает ли дата в период курса
                $afterStart = $slotDate->gte($courseStart);
                $beforeEnd = $courseEnd ? $slotDate->lte($courseEnd) : true;

                return $afterStart && $beforeEnd;
            }
        });

        // Группируем слоты по дням
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
            'понедельник' => Carbon::MONDAY,
            'вторник' => Carbon::TUESDAY,
            'среда' => Carbon::WEDNESDAY,
            'четверг' => Carbon::THURSDAY,
            'пятница' => Carbon::FRIDAY,
            'суббота' => Carbon::SATURDAY,
            'воскресенье' => Carbon::SUNDAY,
        ];

        foreach ($slots as $slot) {
            $date = null;

            // Для разовых занятий
            if ($slot->date) {
                $date = $slot->date->toDateString();
            }
            // Для регулярных занятий
            elseif ($slot->weekday && isset($weekdayMap[$slot->weekday])) {
                $date = $startOfWeek->copy()->next($weekdayMap[$slot->weekday])->toDateString();
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
