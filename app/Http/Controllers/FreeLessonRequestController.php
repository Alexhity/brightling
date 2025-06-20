<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FreeLessonRequest; // ваша модель для заявки
use App\Models\User;
use App\Models\Language;           // модель для языка
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountCreated;

class FreeLessonRequestController extends Controller
{
    //Работа с формой заявки на бесплатный урок
    public function store(Request $request)
    {
        // Валидация входящих данных
        $validatedData = $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'required|string|max:50',
            'email'    => 'required|email|max:255|unique:free_lesson_requests,email',
            'language' => 'required|exists:languages,id', // Проверка, что выбранный язык существует
        ]);

        // Сохранение заявки в базу
        // Если в миграции для поля status задано значение по умолчанию ('new'),
        FreeLessonRequest::create([
            'name'         => $validatedData['name'],
            'phone'        => $validatedData['phone'],
            'email'        => $validatedData['email'],
            'language_id'  => $validatedData['language'],
            'status'       => 'new',
        ]);

        // Здесь можно добавить уведомление для администратора (например, через Notification или Mail)

        // Перенаправляем обратно с сообщением об успешной отправке
        return redirect()->back()->with('success', 'Ваша заявка успешно отправлена!');
    }


    // Изменение роли в админ-панели
    public function updateRole(Request $request, $id)
    {
        // Валидация входящих данных, чтобы роль была одной из разрешённых
        $validated = $request->validate([
            'requested_role' => 'required|in:student,teacher,admin',
        ]);

        // Поиск заявки по идентификатору
        $application = FreeLessonRequest::findOrFail($id);

        // Обновление роли
        $application->requested_role = $validated['requested_role'];
        $application->save();

        // Можно добавить уведомление для администратора об успешном обновлении
        return redirect()->back()->with('success', 'Роль успешно обновлена!');
    }


    // Создание профиля
    public function createProfile(Request $request, $id)
    {
//        set_time_limit(0);
        // Получаем заявку по ID
        $application = FreeLessonRequest::findOrFail($id);

        // Генерация случайного пароля
        $randomPassword = Str::random(5);

        // Создаем пользователя, копируя необходимые данные из заявки.
        // Внимательно подберите поля: убедитесь, что в таблице users есть нужные столбцы.
        $user = User::create([
            'first_name' => $application->name,
            'email'      => $application->email,
            'phone'      => $application->phone,
            'role'       => $application->requested_role, // используем выбранную или измененную пользователем роль
            'password'   => Hash::make($randomPassword),
        ]);

        // Обновляем статус заявки, чтобы отметить, что она обработана
        $application->status = 'approved';  // статус можно подобрать по вашей бизнес-логике
        $application->save();

        // Отправляем уведомление на email с данными для входа.
        // Здесь предполагается, что у вас есть Mailable класс AccountCreated.
        Mail::to($application->email)->send(new AccountCreated($user, $randomPassword));

        // Возвращаем обратный отклик (например, перенаправление с сообщением)
        return redirect()->back()->with('success', 'Личный кабинет успешно создан! Проверьте свою почту.');
    }

    public function createProfilesAll(Request $request)
    {
        set_time_limit(0);
        // Выбираем только заявки с неутверждённым статусом, например, не 'approved'
        $pendingRequests = FreeLessonRequest::where('status', '<>', 'approved')->get();

        foreach ($pendingRequests as $application) {
            // Генерация случайного пароля
            $randomPassword = Str::random(5);

            // Создаём пользователя, копируя данные из заявки.
            // Обратите внимание, что в таблице users должны быть соответствующие столбцы.
            $user = User::create([
                'first_name' => $application->name,
                'email'      => $application->email,
                'phone'      => $application->phone,
                'role'       => $application->requested_role,
                'password'   => Hash::make($randomPassword),
                // можно добавить и другие поля, если требуется
            ]);

            // Обновляем статус заявки на "approved" или другой статус обработки
            $application->status = 'approved';
            $application->save();

            // Отправляем письмо с информацией для входа
            Mail::to($application->email)->send(new AccountCreated($user, $randomPassword));
        }

        return redirect()->back()->with('success', 'Все заявки успешно обработаны. Личные кабинеты созданы.');
    }

}
