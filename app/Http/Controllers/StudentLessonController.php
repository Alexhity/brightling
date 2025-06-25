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
        $student = Auth::user();

        // расчёт окна ±2 недели
        $currentWeekStart = Carbon::now()->startOfWeek();
        $windowStart      = $currentWeekStart->copy()->subWeeks(2)->startOfWeek();
        $windowEnd        = $currentWeekStart->copy()->addWeeks(2)->endOfWeek();

        $lessons = Lesson::with(['course', 'teacher', 'students'])
            ->whereBetween('date', [
                $windowStart->toDateString(),
                $windowEnd->toDateString(),
            ])
            ->where(function($q) use($student) {
                $q->whereHas('course.users', fn($q1) =>
                $q1->where('user_id', $student->id)
                )
                    ->orWhereHas('students', fn($q2) =>
                    $q2->where('user_id', $student->id)
                    );
            })
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        return view('auth.student.lessons.index', [
            'windowStart' => $windowStart,
            'windowEnd'   => $windowEnd,
            'lessons'     => $lessons,
        ]);
    }
}
