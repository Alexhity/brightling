<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TeacherCoursesController extends Controller
{


    public function courses()
    {
        $teacherId = Auth::id();

        $courses = Course::withCount('students')
            ->with('language')
            ->whereHas('teachers', fn($q) => $q->where('users.id', $teacherId))
            ->orderByDesc('id')
            ->get();

        return view('auth.teacher.courses.index', compact('courses'));
    }

    public function editCourseLevel(Course $course)
    {

        $levels = ['beginner'=>'Beginner','A1'=>'A1','A2'=>'A2','B1'=>'B1','B2'=>'B2','C1'=>'C1','C2'=>'C2'];
        return view('auth.teacher.courses.edit_level', compact('course','levels'));
    }

    public function updateCourseLevel(Request $request, Course $course)
    {

        $data = $request->validate([
            'level' => ['required', Rule::in(['beginner','A1','A2','B1','B2','C1','C2'])],
        ], [
            'level.required' => 'Выберите уровень курса',
            'level.in'       => 'Некорректный уровень',
        ]);

        $course->update(['level' => $data['level']]);

        return redirect()
            ->route('teacher.courses.index')
            ->with('success', 'Уровень курса обновлён');
    }
}
