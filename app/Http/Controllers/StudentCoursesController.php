<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;

class StudentCoursesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Отображает страницу "Мои курсы" для студента.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Получаем авторизованного студента
        $student = Auth::user();

        // Предполагаем, что у модели User настроена связь courses()
        // Загружаем курсы с учителем (один) или учителями (если их несколько) и расписанием.
        $courses = $student->courses()->with(['teachers', 'students'])->get();


        return view('auth.student.courses', compact('courses'));
    }
}
