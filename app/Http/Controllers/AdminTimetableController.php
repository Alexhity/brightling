<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FreeLessonRequest;
use App\Models\Price;
use App\Models\Timetable;
use App\Models\Course;
use App\Models\User;
use App\Services\LessonGenerator;
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

        // ДОБАВЛЯЕМ: Определение слотов с исключениями
        $slotsWithExceptions = Timetable::whereNotNull('parent_id')
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->pluck('parent_id');

        // Модифицируем запрос - УБИРАЕМ ПРИВЯЗКУ К КУРСАМ
        $slots = Timetable::with(['course', 'teacher', 'overrideTeacher'])
            ->where(function($q) use($startOfWeek, $endOfWeek, $slotsWithExceptions) {
                // Разовые слоты в текущей неделе
                $q->whereNotNull('date')
                    ->whereBetween('date', [$startOfWeek, $endOfWeek]);

                // Регулярные слоты без исключений
                $q->orWhere(function($q2) use($startOfWeek, $endOfWeek, $slotsWithExceptions) {
                    $q2->whereNull('date')
                        ->whereNotIn('id', $slotsWithExceptions)
                        // УБИРАЕМ ПРОВЕРКУ НА КУРС
                        ->where('active', true) // Добавляем проверку активности
                        ->where(function($q3) use($startOfWeek) {
                            $q3->whereNull('ends_at') // Если нет даты окончания
                            ->orWhere('ends_at', '>=', $startOfWeek); // Или еще действует
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

    public function editSlot(Timetable $timetable, $date)
    {
        // Проверяем существование override-записи для этой даты
        $overrideSlot = Timetable::where('parent_id', $timetable->id)
            ->where('date', $date)
            ->first();

        $teachers = User::where('role', 'teacher')->get();



        return view('auth.admin.timetables.edit-slot', compact(
            'timetable',
            'date',
            'teachers',
            'overrideSlot'
        ));
    }

    public function updateSlot(Request $request, Timetable $timetable, string $date)
    {
        $request->validate([
            'teacher_id' => 'nullable|exists:users,id',
            'cancelled'  => 'sometimes|boolean',
        ]);

        // Находим override-слот (если есть)
        $overrideSlot = Timetable::where('parent_id', $timetable->id)
            ->where('date', $date)
            ->first();

        // 1) Отмена занятия
        if ($request->has('cancelled') && $request->cancelled) {
            if ($overrideSlot) {
                $overrideSlot->update([
                    'cancelled'        => true,
                    'override_user_id' => null,
                ]);
            } else {
                Timetable::create([
                    'parent_id'   => $timetable->id,
                    'date'        => $date,
                    'cancelled'   => true,
                    'course_id'   => $timetable->course_id,
                    'weekday'     => $timetable->weekday,
                    'start_time'  => $timetable->start_time,
                    'duration'    => $timetable->duration,
                    'type'        => $timetable->type,
                    'active'      => true,
                    'user_id'     => $timetable->user_id,
                    'title'       => $timetable->title,
                ]);
            }

            // <<< Вызов генератора для отмены
            LessonGenerator::makeLessonForDate(
                $timetable,
                Carbon::parse($date)
            );

            return redirect()->route('admin.timetables.index')
                ->with('success', 'Занятие отменено');
        }

        // 2) Восстановление отменённого занятия
        if ($request->has('cancelled') && !$request->cancelled && $overrideSlot && $overrideSlot->cancelled) {
            $overrideSlot->delete();

            // <<< Вызов генератора для восстановления
            LessonGenerator::makeLessonForDate(
                $timetable,
                Carbon::parse($date)
            );

            return redirect()->route('admin.timetables.index')
                ->with('success', 'Отмена занятия отменена');
        }

        // 3) Возврат основного преподавателя
        if ($request->teacher_id == $timetable->user_id) {
            if ($overrideSlot) {
                $overrideSlot->delete();
            }

            // <<< Вызов генератора для возврата учителя
            LessonGenerator::makeLessonForDate(
                $timetable,
                Carbon::parse($date)
            );

            return redirect()->route('admin.timetables.index')
                ->with('success', 'Восстановлен основной преподаватель');
        }

        // 4) Замена преподавателя (override)
        if ($overrideSlot) {
            $overrideSlot->update([
                'override_user_id' => $request->teacher_id,
                'cancelled'        => false,
            ]);
        } else {
            Timetable::create([
                'parent_id'        => $timetable->id,
                'date'             => $date,
                'override_user_id' => $request->teacher_id,
                'course_id'        => $timetable->course_id,
                'weekday'          => $timetable->weekday,
                'start_time'       => $timetable->start_time,
                'duration'         => $timetable->duration,
                'type'             => $timetable->type,
                'active'           => true,
                'user_id'          => $timetable->user_id,
                'title'            => $timetable->title,
                'cancelled'        => false,
            ]);
        }

        // <<< Вызов генератора после смены преподавателя
        LessonGenerator::makeLessonForDate(
            $timetable,
            Carbon::parse($date)
        );

        return redirect()->route('admin.timetables.index')
            ->with('success', 'Изменения сохранены');
    }

    public function createSlot()
    {
        $teachers = User::where('role', 'teacher')->get();
        $weekdays = ['понедельник','вторник','среда','четверг','пятница','суббота','воскресенье'];
        $types = ['group' => 'Групповой', 'individual' => 'Индивидуальный', 'test' => 'Тестовый'];

        return view('auth.admin.timetables.create-slot', compact('teachers', 'weekdays', 'types'));
    }

    public function storeSlot(Request $request)
    {
        $validated = $request->validate([
            'timetables' => 'required|array|min:1',
            'timetables.*.weekday' => 'required|in:понедельник,вторник,среда,четверг,пятница,суббота,воскресенье',
            'timetables.*.start_time' => 'required|date_format:H:i',
            'timetables.*.duration' => 'required|integer|min:30|max:240',
            'timetables.*.type' => 'required|in:group,individual,test',
            'timetables.*.user_id' => 'required|exists:users,id',
            'timetables.*.ends_at' => 'nullable|date|after_or_equal:today', // Добавляем правило
        ]);

        foreach ($validated['timetables'] as $slotData) {
            Timetable::create([
                'weekday' => $slotData['weekday'],
                'start_time' => $slotData['start_time'],
                'duration' => $slotData['duration'],
                'type' => $slotData['type'],
                'user_id' => $slotData['user_id'],
                'ends_at' => $slotData['ends_at'] ?? null, // Сохраняем дату окончания
                'active' => true,
                // Для регулярных слотов без курса
                'course_id' => null,
                'title' => $this->generateSlotTitle($slotData['type'])
            ]);
        }

        return redirect()->route('admin.timetables.index')
            ->with('success', 'Регулярные слоты успешно созданы');
    }

    private function generateSlotTitle($type)
    {
        $titles = [
            'group' => 'Групповое занятие',
            'individual' => 'Индивидуальное занятие',
            'test' => 'Тестовый урок'
        ];

        return $titles[$type] ?? 'Занятие';
    }


    public function destroySlot(Timetable $timetable)
    {
        // Проверяем, что слот регулярный (без курса) и не является исключением
        if ($timetable->course_id || $timetable->parent_id) {
            return redirect()->route('admin.timetables.index')
                ->with('error', 'Можно удалять только регулярные слоты без курса');
        }

        // Удаляем слот
        $timetable->delete();

        return redirect()->route('admin.timetables.index')
            ->with('success', 'Регулярный слот удалён');
    }


}



