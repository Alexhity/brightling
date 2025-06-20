<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Homework;
use Illuminate\Http\Request;

class TeacherHomeworkController extends Controller
{
    /**
     * Список домашних заданий преподавателя с фильтром по курсу.
     */
    public function index(Request $request)
    {
        $courseId = $request->input('course');
        // Список курсов для фильтрации
        $courses = Course::where('status','recruiting')->get();

        $query = Homework::with(['lesson.course', 'lesson.users' => function($q) {
            $q->where('role','student');
        }]);

        if ($courseId) {
            $query->whereHas('lesson.course', fn($q) => $q->where('id',$courseId));
        }

        $homeworks = $query->orderBy('deadline','desc')
            ->paginate(15)
            ->appends($request->all());

        return view('auth.teacher.homeworks.index', compact('homeworks','courses','courseId'));
    }

    /**
     * Форма создания нового домашнего задания.
     */
    public function create()
    {
        // Для выбора урока: показываем все курсы + их уроки
        $courses = Course::with('lessons')->get();
        return view('auth.teacher.homeworks.create', compact('courses'));
    }

    /**
     * Сохранение нового задания.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'lesson_id'   => 'required|exists:lessons,id',
            'description' => 'required|string',
            'deadline'    => 'required|date',
            'link'        => 'nullable|url',
        ]);

        Homework::create($data);

        return redirect()
            ->route('teacher.homeworks.index')
            ->with('success','Домашнее задание успешно создано.');
    }

    /**
     * Форма редактирования.
     */
    public function edit(Homework $homework)
    {
        return view('auth.teacher.homeworks.edit', compact('homework'));
    }

    /**
     * Сохраняем изменения.
     */
    public function update(Request $request, Homework $homework)
    {
        $data = $request->validate([
            'description' => 'required|string',
            'deadline'    => 'required|date',
            'link'        => 'nullable|url',
        ]);

        $homework->update($data);

        return redirect()
            ->route('teacher.homeworks.index')
            ->with('success', 'Домашнее задание обновлено.');
    }

    /**
     * Удаление.
     */
    public function destroy(Homework $homework)
    {
        $homework->delete();

        return back()->with('success', 'Задание удалено.');
    }
}
