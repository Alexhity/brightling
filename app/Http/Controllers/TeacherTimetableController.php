<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Timetable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TeacherTimetableController extends Controller
{
    public function index(Request $request)
    {
        $teacher     = Auth::user();
        $startOfWeek = $request->query('week_start')
            ? Carbon::parse($request->query('week_start'))->startOfWeek()
            : Carbon::now()->startOfWeek();
        $endOfWeek   = $startOfWeek->copy()->endOfWeek();

        // 1) Подтягиваем уроки за неделю
        $lessons = Lesson::with('course')
            ->whereBetween('date', [
                $startOfWeek->toDateString(),
                $endOfWeek->toDateString(),
            ])
            ->where('teacher_id', $teacher->id)  // только ваши уроки
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        // 2) Группируем их по дате
        $lessonsByDate = $lessons->groupBy(function($lesson) {
            return \Carbon\Carbon::parse($lesson->date)->toDateString(); // только дата
        });


        // 3) Передаём в blade
        return view('auth.teacher.timetables.index', compact(
            'teacher',
            'startOfWeek',
            'endOfWeek',
            'lessonsByDate'
        ));
    }



}
