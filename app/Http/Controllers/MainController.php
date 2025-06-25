<?php

namespace App\Http\Controllers;

use App\Mail\FeedbackMail;
use App\Models\FreeLessonRequest;
use App\Models\Lesson;
use App\Models\Timetable;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Language;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class MainController extends Controller
{
    public function newAdmin()
    {
        User::create([
           'email' => 'admin@admin.com',
           'password' => Hash::make('123'),
           'role' => 'admin',
           'first_name' => 'Admin',
            'phone' => '+375333837946',
        ]);
    }

    public function index()
    {
        // 1) Все языки
        $languages = Language::all();

        // 2) Собираем список уже занятых уроков
        $usedLessonIds = FreeLessonRequest::query()
            ->whereNotNull('lesson_id')
            ->pluck('lesson_id')
            ->toArray();

        // 3) Определяем границы: от завтра до +2 недель
        $start = Carbon::tomorrow()->toDateString();
        $end   = Carbon::today()->addWeeks(2)->toDateString();

        // 4) Берём тестовые уроки в этом диапазоне, исключая занятые
        $testLessons = Lesson::with('timetable')
            ->where('type', 'test')
            ->where('status', 'scheduled')
            ->whereBetween('date', [$start, $end])
            ->when(count($usedLessonIds), fn($q) =>
            $q->whereNotIn('lessons.id', $usedLessonIds)
            )
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        // 5) Формируем availableDates и timeSlots
        $availableDates = [];
        $timeSlots      = [];

        foreach ($testLessons as $lesson) {
            $dateKey = $lesson->date->format('Y-m-d');
            $availableDates[$dateKey] = $lesson->date->translatedFormat('d M Y (D)');

            $startTime = Carbon::parse($lesson->time);
            $endTime   = $startTime->copy()->addMinutes($lesson->timetable->duration);

            $timeSlots[$dateKey][] = [
                'id'         => $lesson->id,
                'start_time' => $startTime->format('H:i'),
                'end_time'   => $endTime->format('H:i'),
            ];
        }

        // 6) Отдаём view
        return view('main', [
            'languages'      => $languages,
            'availableDates' => $availableDates,
            'timeSlots'      => $timeSlots,
        ]);
    }




    public function sendContact(Request $request)
    {
        // Валидация полей
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'message' => 'required|string|max:2000',
        ]);

        // Собираем текст письма
        $body = "Новое сообщение из формы «Связаться с нами»: \n\n"
            . "Имя: " . $validated['name'] . "\n"
            . "Email: " . $validated['email'] . "\n\n"
            . "Сообщение: \n" . $validated['message'];

        // Отправляем письмо на адрес школы
        Mail::raw($body, function ($message) use ($validated) {
            $message->to('borodichalexandra@gmail.com')
                ->subject('Новое сообщение с сайта BrightLing');
            // Можно дополнительно указать from:
            // ->from($validated['email'], $validated['name']);
        });

        // Возвращаемся назад с флеш-сообщением
        return redirect()
            ->back()
            ->with('success', 'Спасибо! Ваше сообщение отправлено.')
            ->withFragment('contact-anchor');
    }
}
