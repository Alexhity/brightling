<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentLessonController extends Controller
{
    public function index(Request $request)
    {
        // рассчитываем окно ±2 недели
        $currentWeekStart = Carbon::now()->startOfWeek();
        $windowStart = $currentWeekStart->copy()->subWeeks(2)->startOfWeek();
        $windowEnd   = $currentWeekStart->copy()->addWeeks(2)->endOfWeek();

        // выбираем уроки, где студент записан на курс этого урока
        $lessons = Lesson::with(['course', 'teacher'])
            ->whereBetween('date', [
                $windowStart->toDateString(),
                $windowEnd->toDateString(),
            ])
            ->whereHas('course.users', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        return view('auth.student.lessons.index', compact(
            'windowStart', 'windowEnd', 'lessons'
        ));
    }
}
