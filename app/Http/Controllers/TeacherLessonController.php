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
            // исключаем отменённые уроки
            ->where('status', '!=', 'cancelled')
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

    public function edit(Lesson $lesson)
    {
        // Сразу подгружаем существующих пользователей и курс с языком
        $lesson->load(['users', 'course.language']);

        // Собираем id студентов курса, если курс есть, иначе — пустой массив
        $studentIds = $lesson->course
            ? $lesson->course->users()->pluck('users.id')->all()
            : [];

        // Синхронизируем их с этим уроком (не удаляем уже существующих)
        $lesson->users()->syncWithoutDetaching($studentIds);


        // Перезагружаем список пользователей, чтобы отобразить обновлённый pivot
        $lesson->load('users', 'course.language');

        return view('auth.teacher.lessons.edit', compact('lesson'));
    }



    public function update(Request $request, Lesson $lesson)
    {
        $data = $request->validate([
            'zoom_link'       => ['nullable','url'],
            'material_path'   => ['nullable','file','max:10240'],
            'remove_material' => ['nullable','in:1'],
            'statuses'        => ['array'],
            'statuses.*'      => ['nullable','in:present,absent'],
            'languages'       => ['array'],
            'languages.*'     => ['nullable','exists:languages,id'],
            'levels'          => ['array'],
            'levels.*'        => ['nullable','in:beginner,A1,A2,B1,B2,C1,C2'],
        ]);

        // 1) Ссылка
        $lesson->zoom_link = $data['zoom_link'] ?? null;

        // 2) Удаление материала
        if (!empty($data['remove_material']) && $lesson->material_path) {
            @unlink(public_path($lesson->material_path));
            $lesson->material_path = null;
        }

        // 3) Загрузка нового материала
        if ($request->hasFile('material_path')) {
            if ($lesson->material_path) {
                @unlink(public_path($lesson->material_path));
            }
            $file     = $request->file('material_path');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('materials'), $filename);
            $lesson->material_path = 'materials/'.$filename;
        }

        $lesson->save();

        // 4) Обновляем участников
        foreach ($lesson->users as $user) {
            // статус
            $rawStatus = $data['statuses'][$user->id] ?? null;
            $status    = $rawStatus === '' ? null : $rawStatus;

            // Обновляем pivot lesson_user
            $lesson->users()->updateExistingPivot($user->id, [
                'status' => $status,
                // убрали mark
            ]);

            // Обновляем pivot language_user
            $langId = $data['languages'][$user->id] ?? null;
            $lvl    = $data['levels'][$user->id]    ?? null;

            if ($langId) {
                // attach or update level
                $user->languages()->syncWithoutDetaching([
                    $langId => ['level' => $lvl]
                ]);
            }
        }

        return redirect()
            ->route('teacher.lessons.index')
            ->with('success', 'Урок успешно обновлён');
    }
}
