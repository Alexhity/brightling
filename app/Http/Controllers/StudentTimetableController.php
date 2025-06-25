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

        // Курсы студента (pluck с указанием таблицы)
        $courseIds = $student->courses()
            ->select('courses.id')
            ->pluck('courses.id')
            ->all();

        // 1) Берём уроки за эту неделю по этим курсам
        $lessons = Lesson::with(['course','teacher'])
            ->whereBetween('date', [
                $startOfWeek->toDateString(),
                $endOfWeek->toDateString(),
            ])
            ->whereIn('course_id', $courseIds)
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        // 2) Группируем по дате
        $lessonsByDate = $lessons->groupBy(function($lesson) {
            return Carbon::parse($lesson->date)->toDateString();
        });

        // 3) Дни для шапки
        $days = collect(range(0,6))
            ->map(fn($i) => $startOfWeek->copy()->addDays($i)->toDateString());

        return view('auth.student.timetables.index', compact(
            'startOfWeek','endOfWeek','days','lessonsByDate'
        ));
    }
}
