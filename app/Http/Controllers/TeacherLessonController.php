<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TeacherLessonController extends Controller
{
    public function index(Request $request)
    {
        $currentWeekStart = Carbon::now()->startOfWeek();
        $windowStart = $currentWeekStart->copy()->subWeeks(2)->startOfWeek();
        $windowEnd   = $currentWeekStart->copy()->addWeeks(2)->endOfWeek();

        $lessons = Lesson::with('course')
            ->where('teacher_id', Auth::id())
            ->whereBetween('date', [
                $windowStart->toDateString(),
                $windowEnd->toDateString(),
            ])
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        return view('auth.teacher.lessons.index', compact(
            'windowStart', 'windowEnd', 'lessons'
        ));
    }

    /**
     * Форма редактирования урока
     */
    public function edit(Lesson $lesson)
    {
        // relation users подтянет pivot-поля из withPivot в модели
        $lesson->load(['users', 'course.language']);

        // 2) Собираем всех студентов, записанных на курс урока
        $studentIds = $lesson->course->users()->pluck('users.id')->all();

        // 3) Синхронизируем их с этим уроком,
        //    не удаляя уже существующих (syncWithoutDetaching)
        $lesson->users()->syncWithoutDetaching($studentIds);

        // 4) Теперь грузим relation users() — в нём уже будут все студенты
        $lesson->load('users', 'course.language');

        return view('auth.teacher.lessons.edit', compact('lesson'));
    }

    /**
     * Сохранение изменений урока
     */
    public function update(Request $request, Lesson $lesson)
    {
        $data = $request->validate([
            'zoom_link'     => ['nullable', 'url'],
            'material_path' => ['nullable', 'file', 'max:10240'], // до 10 МБ
            'remove_material' => ['nullable','in:1'],
            'statuses'      => ['array'],
            'statuses.*'    => ['in:present,absent'],
            'marks'         => ['array'],
            'marks.*'       => ['nullable','integer','min:0','max:100'],
        ]);

        // 1) Ссылка
        $lesson->zoom_link = $data['zoom_link'] ?? null;

        // 1) Обработка удаления старого файла
        if (!empty($data['remove_material']) && $lesson->material_path) {
            $old = public_path($lesson->material_path);
            if (file_exists($old)) {
                @unlink($old);
            }
            $lesson->material_path = null;
        }

        // 2) Обработка загрузки нового файла
        if ($request->hasFile('material_path')) {
            // Если одновременно был старый файл и нет remove_material,
            // можно тоже удалить старый файл, чтобы не захламлять
            if ($lesson->material_path) {
                $old = public_path($lesson->material_path);
                if (file_exists($old)) {
                    @unlink($old);
                }
            }

            $file     = $request->file('material_path');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('materials'), $filename);
            $lesson->material_path = 'materials/' . $filename;
        }

        $lesson->save();

        // Обновляем участников
        foreach ($lesson->users as $user) {
            $pivotData = [
                'status' => $data['statuses'][$user->id] ?? $user->pivot->status,
                'mark'   => $data['marks'][$user->id] ?? $user->pivot->mark,
            ];
            $lesson->users()->updateExistingPivot($user->id, $pivotData);
        }

        return redirect()
            ->route('teacher.lessons.index')
            ->with('success', 'Урок успешно обновлён');
    }
}
