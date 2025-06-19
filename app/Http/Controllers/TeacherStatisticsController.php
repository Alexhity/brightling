<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Language;
use App\Models\Lesson;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TeacherStatisticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
//        $teacher = auth()->user();
//
//        // Количество курсов, которые ведёт учитель
//        $teacherCourses = $teacher->coursesAsTeacher()->count();
//        $teacherCourses = Course::where('user_id', $teacher->id)->count();

//        // Получаем уникальных студентов по всем курсам, где учитель ведёт занятия
//        $totalStudents = Course::where('user_id', $teacher->id)
//            ->with('students')
//            ->get()
//            ->pluck('students')
//            ->flatten()
//            ->unique('id')
//            ->count();
//
//        // Сколько работает: если created_at заполнено, показываем период, иначе 'Нет данных'
//        $workDuration = $teacher->created_at
//            ? $teacher->created_at->diffForHumans(null, true)
//            : 'Нет данных';



//        return view('auth.teacher.statistics', compact('teacherCourses', 'totalStudents', 'workDuration'));

        {
            $teacher = auth()->user();

            // Получаем количество курсов, где через pivot прикреплён преподаватель (фильтруем по teachers)
            $teacherCourses = Course::whereHas('teachers', function ($query) use ($teacher) {
                $query->where('users.id', $teacher->id);
            })->count();

            // Получаем курсы, где преподаватель ведёт занятия, и подгружаем список студентов (через отношение students)
            $courses = Course::whereHas('teachers', function ($query) use ($teacher) {
                $query->where('users.id', $teacher->id);
            })->with('students')->get();

            $totalStudents = $courses->pluck('students')
                ->flatten()
                ->unique('id')
                ->count();

            $workDuration = $teacher->created_at
                ? $teacher->created_at->diffForHumans(null, true)
                : 'Нет данных';

            // В контроллере преподавателя/студента
            $start = Carbon::now()->startOfWeek();
            $end   = $start->copy()->addMonth();
            $lessons = Lesson::whereBetween('date', [$start, $end])
                ->orderBy('date', 'asc')->orderBy('time', 'asc');

            return view('auth.teacher.statistics', compact('teacherCourses', 'totalStudents', 'workDuration', 'lessons'));
        }
    }
}
