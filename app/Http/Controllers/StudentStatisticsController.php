<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;

class StudentStatisticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $student = auth()->user();

        // Количество активных курсов, где студент записан
        $activeCourses = Course::whereHas('students', function($query) use ($student) {
            $query->where('users.id', $student->id);
        })->count();

//        $completedLessons = Lesson::where('status', 'completed')
//            ->whereHas('users', function ($query) use ($student) {
//                $query->where('users.id', $student->id);
//            })->count();


        // Сколько учится — время с момента регистрации студента
        $studyDuration = $student->created_at->diffForHumans(null, true);

        return view('auth.student.statistics', compact('activeCourses',  'studyDuration'));
    }
}
