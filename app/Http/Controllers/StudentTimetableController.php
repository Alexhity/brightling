<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use Carbon\Carbon;

class StudentTimetableController extends Controller
{
    public function index(Request $request) {
        $userId = auth()->user()->id;
        $start = $request->query('start') ? Carbon::parse($request->query('start')) : Carbon::now()->startOfWeek();

// Получение уроков текущего студента
        $lessons = Lesson::with(['course'])
            ->whereHas('user_lessons', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->whereBetween('date', [$start->toDateString(), $start->copy()->addWeeks(4)->endOfWeek()->toDateString()])
            ->orderBy('date')->orderBy('time')
            ->get();

        return view('auth.student.timetable', compact('lessons', 'start'));
    }
}
