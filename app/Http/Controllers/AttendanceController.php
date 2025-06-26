<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function show(Request $request)
    {
        $student = Auth::user();  // роль student гарантирована middleware

        // Период: месяц из GET-параметра или текущий
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end   = (clone $start)->endOfMonth();

        // Загружаем курсы студента с ценой и уроками по датам
        $courses = $student->courses()
            ->with(['price', 'timetables'])
            ->get()
            ->map(function($course) use ($student, $start, $end) {
                // получаем все уроки за период
                $lessons = $course->lessons()
                    ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
                    ->orderBy('date')
                    ->orderBy('time')
                    ->get()
                    // оставляем только те, где есть запись в pivot
                    ->filter(fn($lesson) => $lesson->users->contains($student));

                // строим строки ведомости
                $lines = $lessons
                    ->map(function($lesson) use ($student, $course) {
                        $dt = Carbon::createFromFormat(
                            'Y-m-d H:i:s',
                            $lesson->date->format('Y-m-d') . ' ' . $lesson->time
                        );
                        $status = $lesson->users
                            ->first(fn($u) => $u->id === $student->id)
                            ->pivot
                            ->status;
                        $price = $status === 'present'
                            ? ($course->price->unit_price ?? 0)
                            : 0;
                        return [
                            'datetime' => $dt,
                            'status'   => $status,
                            'price'    => $price,
                        ];
                    })
                    // **Фильтруем** строки с ненулевым статусом
                    ->filter(fn($item) => ! is_null($item['status']))
                    ->values();  // сбрасываем ключи после фильтрации

                // итоговая сумма по отфильтрованным строкам
                $total = $lines->sum('price');

                return [
                    'course' => $course,
                    'lines'  => $lines,
                    'total'  => $total,
                ];
            });

        return view('auth.student.attendance', compact('student','courses','month'));
    }
}
