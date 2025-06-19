<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        // Получаем текущего пользователя (студента)
        $user = auth()->user();

        // Загружаем курсы, на которые студент записан, вместе с данными (язык, тариф, преподаватель)
        $courses = $user->enrolledCourses()
            ->with(['language', 'pricing', 'teacher'])
            ->orderBy('title')
            ->get();

        return view('auth.student.dashboard', compact('courses'));

//        $student = auth()->user();
//        $courses = $student->courses()->with('teacher')->get();
//      $orders = $student->orders()->latest()->take(5)->get();


    }

}
