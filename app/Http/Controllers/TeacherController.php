<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Course;


class TeacherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
//        $courses = $teacher->taughtCourses()->withCount('students')->get();
//        $reviews = $teacher->reviews()->latest()->take(5)->get();


//        // Дополнительные переменные
//        $totalStudents = $courses->sum('students_count');
//        $activeCoursesCount = $courses->count();


        // Получаем аутентифицированного пользователя (преподавателя)
        $teacher = auth()->user();

        // Отношение coursesTaught вернет все курсы, где teacher_id равен id данного пользователя
        $courses = $teacher->coursesTaught;

        return view('auth.teacher.dashboard', compact('courses'));
    }
}
