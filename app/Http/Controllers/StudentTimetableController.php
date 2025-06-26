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
        $student     = Auth::user();
        $startOfWeek = $request->query('week_start')
            ? Carbon::parse($request->query('week_start'))->startOfWeek()
            : Carbon::now()->startOfWeek();
        $endOfWeek   = $startOfWeek->copy()->endOfWeek();

        // 1) Уроки по курсам студента
        $courseIds = $student->courses()->pluck('courses.id')->all();
        $byCourse  = Lesson::with(['course','teacher','students'])
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->whereIn('course_id', $courseIds);

        // 2) Тестовые уроки через pivot
        $byPivot = Lesson::with(['course','teacher','students'])
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->where('type', 'test') // Добавляем фильтр по типу
            ->whereHas('students', fn($q) => $q->where('user_id', $student->id));

        // 3) Объединяем и сортируем
        $lessons = $byCourse
            ->union($byPivot->toBase())
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        // 4) Группируем по дате
        $lessonsByDate = $lessons->groupBy(fn($lesson) => $lesson->date->toDateString());

        // Даты недели
        $days = collect(range(0,6))
            ->map(fn($i) => $startOfWeek->copy()->addDays($i)->toDateString());

        return view('auth.student.timetables.index', compact(
            'startOfWeek','endOfWeek','days','lessonsByDate'
        ));
    }
}
