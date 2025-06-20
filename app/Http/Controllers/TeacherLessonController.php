<?php

//namespace App\Http\Controllers;
//
//use App\Models\Lesson;
//use Illuminate\Http\Request;
//
//class TeacherLessonController extends Controller
//{
//    // Список уроков
//    public function index()
//    {
//        $lessons = Lesson::where('teacher_id', auth()->id())
//            ->orderBy('date', 'desc')
//            ->orderBy('start_time', 'desc')
//            ->paginate(10);
//
//        return view('auth.teacher.lessons.index', compact('lessons'));
//    }
//
//    // Форма редактирования
//    public function edit(Lesson $lesson)
//    {
//
//        return view('auth.teacher.lessons.edit', compact('lesson'));
//    }
//
//    // Обновление урока
//    public function update(Request $request, Lesson $lesson)
//    {
//
//        $validated = $request->validate([
//            'topic' => 'nullable|string|max:255',
//            'zoom_link' => 'nullable|url',
//            'attendance' => 'required|array',
//            'attendance.*' => 'in:present,absent' // Только 2 статуса
//        ]);
//
//        $lesson->update([
//            'topic' => $request->topic,
//            'zoom_link' => $request->zoom_link,
//            'attendance' => $request->attendance
//        ]);
//
//        return redirect()->route('teacher.lessons.index')
//            ->with('success', 'Урок обновлен');
//    }
//}
