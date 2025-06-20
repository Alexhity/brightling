<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Language;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        // Справочники
        $languages = Language::orderBy('name')->get();
        // русские метки уровней
        $levels    = [
            'beginner' => 'Начинающий',
            'A1'       => 'A1',
            'A2'       => 'A2',
            'B1'       => 'B1',
            'B2'       => 'B2',
            'C1'       => 'C1',
            'C2'       => 'C2',
        ];
        $formats   = ['individual'=>'Индивидуальный','group'=>'Групповой'];

        // фильтры из запроса
        $search   = $request->input('search');
        $language = $request->input('language');
        $level    = $request->input('level');
        $format   = $request->input('format');
        $sort     = $request->input('sort', 'title');
        $dir      = $request->input('dir', 'asc');

        // базовый запрос — только курсы со статусом recruiting
        $query = Course::with(['language','price','timetables' => function($q) {
            $q->where('active', true)
                ->orderByRaw("COALESCE(date, '0001-01-01') asc")
                ->orderBy('weekday')
                ->orderBy('start_time');
        }])
            ->where('status','recruiting');

        // применяем поиск/фильтры
        if ($search)   $query->where('title','like',"%{$search}%");
        if ($language) $query->where('language_id',$language);
        if ($level)    $query->where('level',$level);
        if ($format)   $query->where('format',$format);

        // сортировка
        if ($sort === 'price_total') {
            $query->selectRaw('courses.*, (prices.unit_price * courses.lessons_count) as price_total')
                ->join('prices','courses.price_id','=','prices.id')
                ->orderBy('price_total',$dir);
        } else {
            $query->orderBy($sort,$dir);
        }

        $courses = $query->paginate(16)->appends($request->all());

        return view('courses', compact(
            'courses','search','language','languages',
            'level','levels','format','formats','sort','dir'
        ));
    }

    public function enroll(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id'
        ]);

        $user = auth()->user();
        $courseId = $request->course_id;

        // Проверка дублирования записи
        if ($user->courses()->where('course_id', $courseId)->exists()) {
            return back()->withErrors('Вы уже записаны на этот курс');
        }

        // Создание записи
        $user->courses()->attach($courseId, [
            'enrolled_at' => now(),
            'status' => 'active'
        ]);

        return back()->with('success', 'Вы успешно записаны на курс!');
    }
}
