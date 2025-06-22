<?php

namespace App\Http\Controllers;

use App\Models\Timetable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherTimetableController extends Controller
{
    public function index(Request $request)
    {
        $teacher = Auth::user();
        $startOfWeek = $request->query('week_start')
            ? Carbon::parse($request->query('week_start'))->startOfWeek()
            : Carbon::now()->startOfWeek();

        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        // Получаем все слоты где учитель основной или замещающий
        $slots = Timetable::with(['course'])
            ->where(function($query) use ($teacher) {
                $query->where('user_id', $teacher->id)
                    ->orWhere('override_user_id', $teacher->id);
            })
            ->where('active', true)
            ->where('cancelled', false)
            ->get();

        // Фильтруем слоты по дате курса
        $filteredSlots = $slots->filter(function($slot) use ($startOfWeek, $endOfWeek) {
            $course = $slot->course;

            if (!$course) return false;

            $courseStart = $course->created_at->startOfDay();
            $courseEnd = $course->duration ? Carbon::parse($course->duration)->endOfDay() : null;

            // Для разовых занятий
            if ($slot->date) {
                $slotDate = Carbon::parse($slot->date);
                return $slotDate->between($startOfWeek, $endOfWeek);
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

                $dayIndex = $weekdayMap[$slot->weekday];
                $slotDate = $startOfWeek->copy()->addDays($dayIndex);

                $afterStart = $slotDate >= $courseStart;
                $beforeEnd = $courseEnd ? $slotDate <= $courseEnd : true;
                $inCurrentWeek = $slotDate->between($startOfWeek, $endOfWeek);

                return $afterStart && $beforeEnd && $inCurrentWeek;
            }
        });

        // Группируем слоты по дням с фиксом понедельника
        $groupedSlots = $this->groupSlotsByWeek($filteredSlots, $startOfWeek);

        $days = collect(range(0, 6))
            ->map(fn($i) => $startOfWeek->copy()->addDays($i));

        return view('auth.teacher.timetables.index', [
            'teacher' => $teacher,
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

            if ($slot->date) {
                $date = $slot->date->toDateString();
            }
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
