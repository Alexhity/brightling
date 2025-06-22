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
use Illuminate\Validation\Rule;


class AdminTimetableController extends Controller
{
    public function index(Request $request)
    {
        $startOfWeek = $request->query('week_start')
            ? Carbon::parse($request->query('week_start'))->startOfWeek()
            : Carbon::now()->startOfWeek();
        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        // Исправленный запрос для получения слотов
        $slots = Timetable::with(['course', 'teacher', 'overrideTeacher'])
            ->where(function($q) use($startOfWeek, $endOfWeek) {
                // Разовые слоты в текущей неделе
                $q->whereNotNull('date')
                    ->whereBetween('date', [$startOfWeek, $endOfWeek]);

                // Регулярные слоты
                $q->orWhere(function($q2) use($startOfWeek, $endOfWeek) {
                    $q2->whereNull('date')
                        ->whereHas('course', function($qc) use($startOfWeek, $endOfWeek) {
                            // ВАЖНОЕ ИСПРАВЛЕНИЕ ▼▼▼
                            $qc->where('created_at', '<=', $endOfWeek)
                                ->where(function($q3) use($startOfWeek) {
                                    $q3->whereNull('duration')
                                        ->orWhere('duration', '>=', $startOfWeek);
                                })
                                // Добавляем проверку начала курса
                                ->where('created_at', '<=', $endOfWeek); // Эту строку добавляем
                        });
                });
            })
            ->get();

        // Группировка слотов с исправлением вычисления даты
        $groupedSlots = $slots->groupBy(function($slot) use($startOfWeek) {
            if ($slot->date) {
                return $slot->date->toDateString();
            }

            $weekdayMap = [
                'понедельник' => 0,
                'вторник' => 1,
                'среда' => 2,
                'четверг' => 3,
                'пятница' => 4,
                'суббота' => 5,
                'воскресенье' => 6,
            ];

            $weekday = $slot->weekday;
            $dayIndex = $weekdayMap[$weekday] ?? null;

            if ($dayIndex !== null) {
                return $startOfWeek->copy()->addDays($dayIndex)->toDateString();
            }

            return 'unscheduled';
        });

        $days = collect(range(0, 6))
            ->map(fn($i) => $startOfWeek->copy()->addDays($i));

        // Добавляем фильтрацию по периоду курса ▼▼▼
        $filteredGroupedSlots = collect();
        foreach ($groupedSlots as $dateString => $slotsForDay) {
            $filteredSlots = $slotsForDay->filter(function($slot) use ($dateString) {
                // Для регулярных слотов (без даты) используем дату из группировки
                $slotDate = $slot->date ?: Carbon::parse($dateString);

                // Если курс не привязан - оставляем слот
                if (!$slot->course) return true;

                $courseStart = $slot->course->created_at->startOfDay();
                $courseEnd = $slot->course->duration
                    ? $slot->course->duration->endOfDay()
                    : null;

                return $slotDate >= $courseStart &&
                    (!$courseEnd || $slotDate <= $courseEnd);
            });

            $filteredGroupedSlots[$dateString] = $filteredSlots->sortBy('start_time');
        }

        return view('auth.admin.timetables.index', [
            'startOfWeek' => $startOfWeek,
            'endOfWeek' => $endOfWeek,
            'groupedSlots' => $filteredGroupedSlots,
            'days' => $days
        ]);
    }


    public function create()
    {
        $courses = Course::orderBy('title')->get();
        $teachers = User::where('role', 'teacher')->get();
        $weekdays = ['понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота', 'воскресенье'];
        $lessonTypes = Timetable::lessonTypes();

        return view('auth.admin.timetables.create', compact(
            'courses', 'teachers', 'weekdays', 'lessonTypes'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'slot_type' => 'required|in:single,recurring',
            'date' => 'required_if:slot_type,single|nullable|date',
            'weekday' => 'required_if:slot_type,recurring|nullable|in:понедельник,вторник,среда,четверг,пятница,суббота,воскресенье',
            'ends_at' => 'nullable|date|after_or_equal:today',
            'lesson_type' => 'required|in:group,individual,test',
            'start_time' => 'required|date_format:H:i',
            'duration' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($request) {
                    // Проверка для тестовых уроков
                    if ($request->input('lesson_type') === 'test' && $value != 15) {
                        $fail('Для тестовых уроков длительность должна быть 15 минут.');
                    }

                    // Проверка для других типов уроков
                    if ($request->input('lesson_type') !== 'test') {
                        if ($value < 15) {
                            $fail('Минимальная длительность занятия - 15 минут.');
                        }
                        if ($value > 240) {
                            $fail('Максимальная длительность занятия - 4 часа (240 минут).');
                        }
                    }
                }
            ],
            'teacher_id' => 'required|exists:users,id',
            'course_id' => 'nullable|exists:courses,id',
            'title' => 'required_without:course_id|string|max:255',
            'active' => 'sometimes|boolean',
            'is_public' => 'sometimes|boolean',
        ]);

        // Автоматическая установка параметров для тестовых уроков
        $data = $validated;
        if ($data['lesson_type'] === 'test') {
            $data['duration'] = 15; // Фиксированная длительность 15 минут
            $data['is_public'] = true; // По умолчанию публичный
        }

        $slotData = [
            'type' => $data['lesson_type'],
            'start_time' => $data['start_time'],
            'duration' => $data['duration'],
            'user_id' => $data['teacher_id'],
            'course_id' => $data['course_id'] ?? null,
            'title' => $data['title'] ?? null,
            'active' => $request->has('active'),
            'is_public' => $request->boolean('is_public') && $data['lesson_type'] === 'test',
        ];

        if ($data['slot_type'] === 'single') {
            $slotData['date'] = $data['date'];
        } else {
            $slotData['weekday'] = $data['weekday'];
            $slotData['ends_at'] = $data['ends_at'] ?? null;
        }

        Timetable::create($slotData);

        return redirect()->route('admin.timetables.index')
            ->with('success', 'Слот успешно создан!');
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
            // Обновляем основной слот
            $timetable->update([
                'active'  => $data['active'],
                'user_id' => $data['user_id'],
            ]);

            // Удаляем все исключения для этого слота
            Timetable::where('parent_id', $timetable->id)->delete();
        } else {
            // Обновляем только для конкретной даты
            $this->updateSingleSlot($timetable, $date, $data);
        }

        return redirect()
            ->route('admin.timetables.index')
            ->with('success', 'Слот успешно обновлён');
    }

    protected function updateSingleSlot(Timetable $parent, string $date, array $data)
    {
        // Удаляем старые исключения для этой даты
        Timetable::where('parent_id', $parent->id)
            ->where('date', $date)
            ->delete();

        // Если слот деактивирован - просто создаем отмену
        if (!$data['active']) {
            return Timetable::create([
                'parent_id' => $parent->id,
                'date' => $date,
                'cancelled' => true,
                'course_id' => $parent->course_id,
                'weekday' => $parent->weekday,
                'start_time' => $parent->start_time,
                'duration' => $parent->duration,
                'type' => $parent->type,
                'active' => false,
                'user_id' => $parent->user_id,
                'title' => $parent->title,
            ]);
        }

        // Если изменен преподаватель - создаем исключение с новым преподавателем
        if ($data['user_id'] != $parent->user_id) {
            return Timetable::create([
                'parent_id' => $parent->id,
                'date' => $date,
                'override_user_id' => $data['user_id'],
                'course_id' => $parent->course_id,
                'weekday' => $parent->weekday,
                'start_time' => $parent->start_time,
                'duration' => $parent->duration,
                'type' => $parent->type,
                'active' => true,
                'user_id' => $parent->user_id,
                'title' => $parent->title,
            ]);
        }

        // Если никаких изменений нет - ничего не делаем
        return null;
    }
}

