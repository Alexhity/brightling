<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        // Инициализация построителя запроса
        $query = Course::with('language', 'price');

        // Фильтрация по поисковому запросу
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Фильтрация по ID языка
        if ($request->filled('language_id')) {
            $query->where('language_id', $request->input('language_id'));
        }

        // Сортировка. По умолчанию сортировка по 'title' по возрастанию
        $allowedSorts = ['title', 'lessons_count', 'duration', 'created_at'];
        $sort_by = $request->input('sort_by', 'title');
        $order   = $request->input('order', 'asc');

        if (!in_array($sort_by, $allowedSorts)) {
            $sort_by = 'title';
        }
        $query->orderBy($sort_by, $order);

        // Пагинация (например, 12 курсов на страницу)
        $courses = $query->paginate(12);

        return view('courses', compact('courses'));
    }

    /**
     * Отображение детальной информации по курсу.
     *
     * Используется Route Model Binding: параметр $course автоматически содержит выбранный объект.
     */
    public function show(Course $course)
    {
        // Если требуется загрузить связанные модели, можно использовать метод load
        $course->load('language', 'price', 'reviews', 'lessons');

        return view('courses.show', compact('course'));
    }
}
