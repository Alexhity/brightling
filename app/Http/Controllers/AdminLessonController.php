<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;

class AdminLessonController extends Controller
{
//    public function store(Request $request)
//    {
//        $data = $request->validate([
//            'is_once'    => 'required|boolean',
//            'date'       => 'required|date',
//            'time'       => 'required|date_format:H:i',
//            'course_id'  => 'required|exists:courses,id',
//        ]);
//
//        Lesson::create([
//            'timetable_id' => null,
//            'course_id'    => $data['course_id'],
//            'teacher_id'   => null,              // можно потом назначить
//            'date'         => $data['date'],
//            'time'         => $data['time'],
//            'status'       => 'scheduled',
//            'zoom_link'    => null,
//        ]);
//
//        return redirect()
//            ->route('admin.schedule.index', ['start' => $data['date']])
//            ->with('success', 'Однократный урок успешно добавлен.');
//    }
}
