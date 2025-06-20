<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Course;


class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $search   = $request->input('search');
        $language = $request->input('language');
        $level    = $request->input('level');

        $languages = Language::orderBy('name')->get();
        $levels    = [
            'beginner' => 'Начинающий',
            'A1'       => 'A1',
            'A2'       => 'A2',
            'B1'       => 'B1',
            'B2'       => 'B2',
            'C1'       => 'C1',
            'C2'       => 'C2',
        ];

        $query = User::with(['languages' => function($q) {
            $q->withPivot('level');
        }])
            ->where('role', 'teacher');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name',  'like', "%{$search}%");
            });
        }

        if ($language) {
            $query->whereHas('languages', function($q) use ($language) {
                $q->where('language_id', $language);
            });
        }

        if ($level) {
            $query->whereHas('languages', function($q) use ($level) {
                $q->wherePivot('level', $level);
            });
        }

        $teachers = $query->paginate(12)->appends($request->all());

        return view('teachers', compact(
            'teachers',
            'search',
            'language',
            'languages',
            'level',
            'levels'
        ));
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
