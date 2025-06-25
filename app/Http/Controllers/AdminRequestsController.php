<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;
use App\Models\FreeLessonRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountCreated;

class AdminRequestsController extends Controller
{
    public function index()
    {

        // Загружаем заявки, которые ещё не обработаны
        $requests = FreeLessonRequest::where('status', '<>', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('auth.admin.requests', compact('requests'));
    }

    public function updateRole(Request $request, $id)
    {
        $validated = $request->validate([
            'requested_role' => 'required|in:student,teacher,admin',
        ]);

        $application = FreeLessonRequest::findOrFail($id);
        $application->requested_role = $validated['requested_role'];
        $application->save();

        return redirect()->back()->with('success', 'Роль успешно обновлена!');
    }

    public function createProfile(Request $request, $id)
    {
        $application = FreeLessonRequest::findOrFail($id);
        $randomPassword = Str::random(8);

        $user = User::create([
            'first_name' => $application->name,
            'email'      => $application->email,
            'phone'      => $application->phone,
            'role'       => $application->requested_role,
            'password'   => Hash::make($randomPassword),
        ]);

        $application->status = 'approved';
        $application->save();

        Mail::to($application->email)->send(new AccountCreated($user, $randomPassword));

        return redirect()->back()->with('success', 'Личный кабинет успешно создан! Проверьте свою почту.');
    }

    public function createProfilesAll(Request $request)
    {
        // Сразу забираем все email в users
        $existingEmails = User::pluck('email')->all();

        $skipped = [];  // сюда будем заносить email, которые уже были

        $pending = FreeLessonRequest::where('status', '<>', 'approved')->get();

        foreach ($pending as $application) {
            // 1) если такой email уже есть — запомним и пропустим
            if (in_array($application->email, $existingEmails, true)) {
                $skipped[] = $application->email;
                // при этом мы можем отметить заявку, но обычно оставляем её на будущее
                continue;
            }

            // 2) создаём пользователя
            $randomPassword = Str::random(8);
            $user = User::create([
                'first_name' => $application->name,
                'email'      => $application->email,
                'phone'      => $application->phone,
                'role'       => $application->requested_role,
                'password'   => Hash::make($randomPassword),
            ]);

            // 3) если студент и есть lesson_id — привязываем его к уроку
            if ($application->requested_role === 'student' && $application->lesson_id) {
                $lesson = Lesson::find($application->lesson_id);
                if ($lesson) {
                    $lesson->students()->attach($user->id);
                    $lesson->status = 'scheduled';
                    $lesson->save();
                }
            }

            // 4) помечаем заявку как обработанную
            $application->status = 'approved';
            $application->save();

            // 5) отправляем письмо
            Mail::to($application->email)
                ->send(new AccountCreated($user, $randomPassword));
        }

        // 6) подготавливаем флеш-сообщения
        $message = 'Все новые заявки обработаны.';
        if (count($skipped)) {
            $message .= ' Пропущены, т.к. уже зарегистрированы: ' . implode(', ', array_unique($skipped)) . '.';
        }

        return redirect()
            ->back()
            ->with('success', $message);
    }

}
