<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Language;
use App\Models\User;
use App\Models\Price;
use App\Models\FreeLessonRequest;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Защищаем все методы с помощью middleware auth
//    public function __construct()
//    {
//        $this->middleware('auth');
//    }

    public function dashboard()
    {

        // Считаем статистику
        $stats = [
            'users'     => User::count(),
            'courses'   => Course::count(),
            'languages' => Language::count(),
        ];

        // Получаем последних 5 пользователей
        $recentUsers = User::orderBy('created_at', 'desc')->take(5)->get();

        // Получаем заявки
        $requests = FreeLessonRequest::where('status', '<>', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();

        // Получаем все языки
        $languages = Language::orderBy('id', 'desc')->get();

        // Получаем всех учителей
        $teachers = User::where('role', 'teacher')
            ->orderBy('first_name')
            ->get();

        // Получаем все тарифы (цены)
        $pricings = Price::orderBy('id')->get();

        // Получаем все курсы
        $courses = Course::orderBy('title')->get();

        // Получаем всех студентов (тех, кого можно добавить в курс)
        $availableStudents = User::where('role', 'student')
            ->orderBy('first_name')
            ->get();

        // Передаем все переменные в представление dashboard
        return view('auth.admin.statistics', compact(
            'stats',
            'recentUsers',
            'requests',
            'languages',
            'teachers',
            'pricings',
            'courses',
            'availableStudents'
        ));
    }


    // Метод для добавления нового языка
    public function storeLanguage(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:languages,name',
        ]);

        Language::create($validated);

        // Перенаправляем на админ-панель, где dashboard() уже соберёт актуальные данные
        return redirect()->route('auth.admin.statistics')->with('success', 'Новый язык успешно добавлен!');
    }

    // Метод для отображения формы создания курса
    public function createCourse()
    {
        // Получаем список преподавателей (предполагается, что роль teacher)
        $teachers = User::where('role', 'teacher')->orderBy('first_name')->get();

        // Получаем список языков
        $languages = Language::orderBy('name')->get();

        // Получаем список тарифов/цен (предполагается, что существует модель Price)
        $pricings = Price::orderBy('id')->get();

        return view('admin.courses.create', compact('teachers', 'languages', 'pricings'));
    }

    // Метод для сохранения нового курса
    public function storeCourse(Request $request)
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'format'        => 'required|string|max:255',
            'age_group'     => 'nullable|string|max:255',
            'lesson_count'  => 'nullable|integer',
            'duration'      => 'required|string|max:255',
            'teacher_id'    => 'required|exists:users,id',
            'language_id'   => 'required|exists:languages,id',
            'pricing_id'    => 'required|exists:prices,id',
        ]);

        Course::create($validated);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Новый курс успешно создан.');
    }

    public function editCourseStudents(Course $course)
    {

    $course->load('students');

    // Получаем всех пользователей с ролью "student"
    $allStudents = User::where('role', 'student')->orderBy('first_name')->get();

    // Исключаем студентов, уже записанных в курс
    $enrolledIds = $course->students->pluck('id')->toArray();
    $availableStudents = $allStudents->reject(function ($student) use ($enrolledIds) {
        return in_array($student->id, $enrolledIds);
    });

    // Передаём переменные в view: переменная $course и $availableStudents
    return view('admin.courses.add_students', compact('course', 'availableStudents'));
    }

    public function updateCourseStudents(Request $request, Course $course)
    {
        $validated = $request->validate([
            'student_ids'   => 'required|array',
            'student_ids.*' => 'exists:users,id',
        ]);

        // Прикрепляем выбранных студентов, не повторяя уже прикрепленные записи
        $course->students()->syncWithoutDetaching($validated['student_ids']);

        return redirect()->route('admin.courses.students.edit', $course->id)
            ->with('success', 'Студенты успешно добавлены в курс.');
    }
}
