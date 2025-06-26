<?php

namespace App\Http\Controllers;

use App\Models\Timetable;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\User;
use App\Models\Language;
use App\Models\Price;

class AdminCoursesController extends Controller
{
    public function index(Request $request)
    {
        $search   = $request->input('search');
        $status   = $request->input('status');
        $language = $request->input('language');
        $level    = $request->input('level');
        $format   = $request->input('format');
        $sort     = $request->input('sort', 'title');
        $dir      = $request->input('dir', 'asc');

        $languages = Language::orderBy('name')->get();
        $levels    = ['beginner'=>'Начинающий','A1'=>'A1','A2'=>'A2','B1'=>'B1','B2'=>'B2','C1'=>'C1','C2'=>'C2'];
        $formats   = ['individual'=>'Индивидуальный','group'=>'Групповой'];

        // Базовый запрос
        $query = Course::with([
            'language',
            'price',
            'users',
            // Подгружаем только активные слоты
            'timetables' => function($q) {
                $q->where('active', true)
                    ->orderByRaw("COALESCE(date, '0001-01-01') asc")
                    ->orderBy('weekday')
                    ->orderBy('start_time')
                    ->whereNull('date') ;
            }
        ]);

        if ($search)   $query->where('title', 'like', "%".trim($search)."%");
        if ($status)   $query->where('status', $status);
        if ($language) $query->where('language_id', $language);
        if ($level)    $query->where('level', $level);
        if ($format)   $query->where('format', $format);

        // Сортировка
        if ($sort === 'price_total') {
            $query->selectRaw('courses.*, (prices.unit_price * courses.lessons_count) as price_total')
                ->join('prices', 'courses.price_id', '=', 'prices.id')
                ->orderBy('price_total', $dir);
        } elseif (in_array($sort, ['title','duration','lessons_count'])) {
            $query->orderBy($sort, $dir);
        } else {
            $query->orderBy('title', 'asc');
        }

        $courses = $query->paginate(15)->appends($request->all());

        return view('auth.admin.courses.index', compact(
            'courses','search','status','language','languages',
            'level','levels','format','formats','sort','dir'
        ));
    }

    public function create()
    {
        $languages = Language::orderBy('name')->get();
        $prices    = Price::orderBy('unit_price')->get();
        $levels    = ['beginner'=>'Начинающий','A1'=>'A1','A2'=>'A2','B1'=>'B1','B2'=>'B2','C1'=>'C1','C2'=>'C2'];
        $formats   = ['individual'=>'Индивидуальный','group'=>'Групповой'];
        $teachers  = User::where('role','teacher')->get();
        $weekdays  = ['понедельник','вторник','среда','четверг','пятница','суббота','воскресенье'];
        $types     = ['group'=>'Групповой','individual'=>'Индивидуальный'];

        return view('auth.admin.courses.create', compact(
            'languages','prices','levels','formats',
            'teachers','weekdays','types'
        ));
    }

    public function store(Request $request)
    {


        $data = $request->validate([

            'title'         => 'required|string|max:255',
        'description'   => 'nullable|string',
        'language_id'   => 'required|exists:languages,id',
        'price_id'      => 'required|exists:prices,id',
        'level'         => 'required|in:beginner,A1,A2,B1,B2,C1,C2',
        'format'        => 'required|in:individual,group,online,offline,hybrid',
        'age_group'     => 'nullable|string|max:50',
        'lessons_count' => 'required|integer|min:1',
        'duration'      => 'nullable|date|after_or_equal:today',
        'status'        => 'required|in:recruiting,not_recruiting,completed',
            'timetables'             => 'sometimes|array',
            'timetables.*.weekday'   => 'required_without:timetables.*.date|in:понедельник,вторник,среда,четверг,пятница,суббота,воскресенье',
            'timetables.*.date'      => 'required_without:timetables.*.weekday|nullable|date',
            'timetables.*.start_time'=> 'required|date_format:H:i',
            'timetables.*.duration'  => 'required|integer|min:1',
            'timetables.*.type' => 'required|in:group,individual',
            'timetables.*.user_id'   => 'required|exists:users,id',
            'timetables.*.active'    => 'sometimes|boolean',
        ]);

        $course = Course::create($data);

        if (!empty($data['timetables'])) {
            foreach ($data['timetables'] as $slot) {
                $slot['active'] = !empty($slot['active']);
                $slot['ends_at'] = $course->duration;
                $course->timetables()->create($slot);
            }
        }


        return redirect()->route('admin.courses.index')
            ->with('success', "Курс «{$course->title}» успешно создан.");
    }

    public function edit(Course $course)
    {
        $languages = Language::orderBy('name')->get();
        $prices    = Price::orderBy('unit_price')->get();
        $levels    = ['beginner'=>'Начинающий','A1'=>'A1','A2'=>'A2','B1'=>'B1','B2'=>'B2','C1'=>'C1','C2'=>'C2'];
        $formats   = ['individual'=>'Индивидуальный','group'=>'Групповой','online'=>'Онлайн','offline'=>'Офлайн','hybrid'=>'Смешанный'];
        $teachers  = User::where('role','teacher')->get();
        $weekdays  = ['понедельник','вторник','среда','четверг','пятница','суббота','воскресенье'];
        $types     = ['group'=>'Групповой','individual'=>'Индивидуальный','free'=>'Бесплатный'];
        $slots     = $course->timetables->whereNull('date');  // <— теперь правильная связь

        return view('auth.admin.courses.edit', compact(
            'course','languages','prices','levels','formats',
            'teachers','weekdays','types','slots'
        ));
    }

    public function update(Request $request, Course $course)
    {
        logger()->info('slot ');
        // Сначала формируем правила валидации
        $rules = [
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'language_id'   => 'required|exists:languages,id',
            'price_id'      => 'required|exists:prices,id',
            'level'         => 'required|in:beginner,A1,A2,B1,B2,C1,C2',
            'format'        => 'required|in:individual,group,online,offline,hybrid',
            'age_group'     => 'nullable|string|max:50',
            'lessons_count' => 'required|integer|min:1',
            // Правило для duration с учётом $course
            'duration'      => 'nullable|date|after_or_equal:' . $course->created_at->toDateString(),
            'status'        => 'required|in:recruiting,not_recruiting,completed',
            // Слоты
            'timetables'             => 'sometimes|array',
            'timetables.*.id'        => 'nullable|exists:timetables,id',
            'timetables.*.weekday'   => 'required_without:timetables.*.date|in:понедельник,вторник,среда,четверг,пятница,суббота,воскресенье',
            'timetables.*.date'      => 'required_without:timetables.*.weekday|nullable|date',
            'timetables.*.start_time'=> 'required|date_format:H:i',
            'timetables.*.duration'  => 'required|integer|min:1',
            'timetables.*.type' => 'required|in:group,individual',
            'timetables.*.user_id'   => 'required|exists:users,id',
            'timetables.*.active'    => 'sometimes|boolean',
        ];
        // Теперь вызываем валидацию
        $data = $request->validate($rules);

        // Обновляем курс
        $course->update([
            'title'         => $data['title'],
            'description'   => $data['description'] ?? null,
            'language_id'   => $data['language_id'],
            'price_id'      => $data['price_id'],
            'level'         => $data['level'],
            'format'        => $data['format'],
            'age_group'     => $data['age_group'] ?? null,
            'lessons_count' => $data['lessons_count'],
            'duration'      => $data['duration'] ?? null,
            'status'        => $data['status']
        ]);

        // Синхронизация слотов
        $incoming = collect($data['timetables'] ?? []);
        $ids      = $incoming->pluck('id')->filter()->all();

        // Удаляем те, что были удалены в форме
        $course->timetables()->whereNotIn('id', $ids)->delete();

        // Обновляем и создаём слоты
        foreach ($incoming as $slot) {
            $slot['active'] = !empty($slot['active']);
            if (!empty($slot['id'])) {
                \App\Models\Timetable::find($slot['id'])->update([
                    'weekday'    => $slot['weekday'] ?? null,
                    'date'       => $slot['date'] ?? null,
                    'start_time' => $slot['start_time'],
                    'duration'   => $slot['duration'],
                    'type'       => $slot['type'],
                    'user_id'    => $slot['user_id'],
                    'active'     => $slot['active'],
                    'ends_at'    => $course->duration,
                ]);
            } else {
                Timetable::create([
                    'weekday'    => $slot['weekday'] ?? null,
                    'date'       => $slot['date'] ?? null,
                    'start_time' => $slot['start_time'],
                    'duration'   => $slot['duration'],
                    'type'       => $slot['type'],
                    'user_id'    => $slot['user_id'],
                    'active'     => $slot['active'],
                    'course_id' => $course['id'],
                    'ends_at'    => $course->duration
                ]);
//                $course->timetables()->create([
//                    'weekday'    => $slot['weekday'] ?? null,
//                    'date'       => $slot['date'] ?? null,
//                    'start_time' => $slot['start_time'],
//                    'duration'   => $slot['duration'],
//                    'type'       => $slot['type'],
//                    'user_id'    => $slot['user_id'],
//                    'active'     => $slot['active'],
//                ]);
            }
        }


        return redirect()->route('admin.courses.index')
            ->with('success', "Курс «{$course->title}» успешно обновлён.");
    }



    public function destroy(Course $course)
    {
        // Удаляем все слоты, привязанные к этому курсу
        $course->timetables()->delete();

        // Теперь можно удалить сам курс
        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', "Курс «{$course->title}» удалён.");
    }

    /**
     * Показывает страницу управления участниками.
     */
    public function participants(Course $course)
    {
        // Текущие преподаватели и студенты
        $teachers = $course->users()->wherePivot('role', 'teacher')->get();
        $students = $course->users()->wherePivot('role', 'student')->get();

        // Все пользователи с ролью teacher/student для добавления
        $allTeachers = User::where('role', 'teacher')->get();
        $allStudents = User::where('role', 'student')->get();

        return view('auth.admin.courses.participants', compact(
            'course',
            'teachers',
            'students',
            'allTeachers',
            'allStudents'
        ));
    }

    /**
     * Добавляет преподавателя к курсу.
     */
    public function addTeacher(Request $request, Course $course)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // attach с ролью
        $course->users()->attach($data['user_id'], ['role' => 'teacher']);

        return back()->with('success', 'Преподаватель добавлен.');
    }

    /**
     * Удаляет преподавателя из курса.
     */
    public function removeTeacher(Course $course, User $user)
    {
        $course->users()->detach($user->id);

        return back()->with('success', 'Преподаватель удалён.');
    }

    /**
     * Добавляет студента к курсу.
     */
    public function addStudent(Request $request, Course $course)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $course->users()->attach($data['user_id'], ['role' => 'student']);

        return back()->with('success', 'Студент добавлен.');
    }

    /**
     * Удаляет студента из курса.
     */
    public function removeStudent(Course $course, User $user)
    {
        $course->users()->detach($user->id);

        return back()->with('success', 'Студент удалён.');
    }

}
