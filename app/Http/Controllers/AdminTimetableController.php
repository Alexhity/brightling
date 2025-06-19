<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FreeLessonRequest;
use App\Models\Price;
use App\Models\Timetable;
use App\Models\Course;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminTimetableController extends Controller
{

    public function index(Request $request)
    {
        $startOfWeek = $request->query('week_start')
            ? Carbon::parse($request->query('week_start'))->startOfWeek()
            : Carbon::now()->startOfWeek();
        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        // 1) Получаем слоты
        $slots = Timetable::with('course')
            ->where(function($q) use($startOfWeek, $endOfWeek) {
                // А) разовые
                $q->whereNotNull('date')
                    ->whereBetween('date', [
                        $startOfWeek->toDateString(),
                        $endOfWeek->toDateString(),
                    ]);
                // B) регулярные (любые, которые вообще лежат в интервале существования курса)
                $q->orWhere(function($q2) use($startOfWeek, $endOfWeek) {
                    $q2->whereNull('date')
                        ->whereHas('course', function($qc) use($startOfWeek, $endOfWeek) {
                            $qc->whereDate('created_at', '<=', $endOfWeek)
                                ->where(function($q3) use($startOfWeek) {
                                    $q3->whereNull('duration')
                                        ->orWhereDate('duration', '>=', $startOfWeek);
                                });
                        });
                });
            })
            ->get()
            ->groupBy(fn(Timetable $t) => $t->date
                ? $t->date
                : mb_strtolower($t->weekday)
            );

        // 2) Дни для шаблона
        $days = collect(range(0,6))
            ->map(fn($i) => $startOfWeek->copy()->addDays($i));

        return view('auth.admin.timetables.index', compact(
            'startOfWeek','endOfWeek','slots','days'
        ));
    }

    public function create()
    {
        $courses  = Course::orderBy('title')->get();
        $teachers = User::where('role','teacher')->get();
        $prices    = Price::orderBy('unit_price')->get();
        $weekdays = ['понедельник','вторник','среда','четверг','пятница','суббота','воскресенье'];
        $types    = ['group'=>'Групповой','individual'=>'Индивидуальный','free'=>'Бесплатный'];

        return view('auth.admin.timetables.create', compact(
            'courses','teachers','prices','weekdays','types'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id'   => 'nullable|exists:courses,id',
            'price_id'    => 'required|exists:prices,id',
            'type'        => 'required|in:group,individual,free',
            'start_time'  => 'required|date_format:H:i',
            'duration'    => 'required|integer|min:1',
            'user_id'     => 'required|exists:users,id',
            'active'      => 'sometimes|boolean',
            'repeat_type' => 'required|in:single,recurring',
            'date'        => 'required_if:repeat_type,single|nullable|date',
            'weekday'     => 'required_if:repeat_type,recurring|nullable|in:понедельник,вторник,среда,четверг,пятница,суббота,воскресенье',
            'ends_at'     => 'nullable|date|after_or_equal:today',
            'title'       => 'required_without:course_id|string|max:255',
        ]);

        // (Ваш код проверки courseStart/courseEnd, если нужен)

        $slotData = [
            'course_id'  => $data['course_id'] ?? null,
            'price_id'   => $data['price_id'],
            'type'       => $data['type'],
            'start_time' => $data['start_time'],
            'duration'   => $data['duration'],
            'user_id'    => $data['user_id'],
            'active'     => !empty($data['active']),
        ];

        if ($data['repeat_type'] === 'single') {
            $slotData['date']    = $data['date'];
            $slotData['weekday'] = null;
            $slotData['ends_at'] = null;
        } else {
            $slotData['date']    = null;
            $slotData['weekday'] = $data['weekday'];
            $slotData['ends_at'] = $data['ends_at'] ?? null;
        }

        Timetable::create($slotData);

        return redirect()->route('admin.timetables.index')
            ->with('success','Слот успешно создан.');
    }

    public function edit(Timetable $timetable)
    {
        $courses  = Course::orderBy('title')->get();
        $weekdays = ['понедельник','вторник','среда','четверг','пятница','суббота','воскресенье'];
        $types    = ['group'=>'Групповой','individual'=>'Индивидуальный','free'=>'Бесплатный'];

        return view('auth.admin.timetables.edit', compact(
            'timetable', 'courses', 'weekdays', 'types'
        ));
    }

    public function update(Request $request, Course $course)
    {
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
            'timetables.*.type'      => 'required|in:group,individual,free',
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
            'status'        => $data['status'],
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
                ]);
            } else {
                $course->timetables()->create([
                    'weekday'    => $slot['weekday'] ?? null,
                    'date'       => $slot['date'] ?? null,
                    'start_time' => $slot['start_time'],
                    'duration'   => $slot['duration'],
                    'type'       => $slot['type'],
                    'user_id'    => $slot['user_id'],
                    'active'     => $slot['active'],
                ]);
            }
        }

        return redirect()->route('admin.courses.index')
            ->with('success', "Курс «{$course->title}» успешно обновлён.");
    }

    public function editSlot(Timetable $timetable, $date)
    {
        $teachers = User::where('role','teacher')->get();
        return view('auth.admin.timetables.edit-slot', compact(
            'timetable','teachers','date'
        ));
    }

    public function updateSlot(Request $request, Timetable $timetable, string $date)
    {
        $data = $request->validate([
            'apply_to' => 'required|in:single,series',
            'active'   => 'required|boolean',
            'user_id'  => 'required|exists:users,id',
        ]);

        if ($data['apply_to'] === 'series') {
            // Применяем к базовому слоту (смена на всю серию)
            $timetable->update([
                'active'  => $data['active'],
                'user_id' => $data['user_id'],
            ]);

        } else {
            // Применяем только к одному занятию → создаём новую запись‑исключение
            $exception = new Timetable();

            // Цепочка родитель → exception
            $exception->parent_id = $timetable->id;
            $exception->date      = $date;

            // Копируем из родительского слота все неизменяемые поля
            $exception->course_id  = $timetable->course_id;
            $exception->weekday    = $timetable->weekday;
            $exception->start_time = $timetable->start_time;
            $exception->duration   = $timetable->duration;
            $exception->type       = $timetable->type;
            $exception->active     = $timetable->active;
            $exception->user_id    = $timetable->user_id;
            if (isset($timetable->title)) {
                $exception->title = $timetable->title;
            }

            // Накладываем переопределения
            $exception->override_start_time = $timetable->start_time;
            $exception->override_duration   = $timetable->duration;
            $exception->override_user_id    = $data['user_id'];

            // Если деактивируем — ставим флаг cancelled
            if (! $data['active']) {
                $exception->cancelled = true;
            }

            $exception->save();
        }

        return redirect()
            ->route('admin.timetables.index')
            ->with('success', 'Слот успешно обновлён');
    }
}


