<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Timetable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\FreeLessonRequest; // ваша модель для заявки
use App\Models\User;
use App\Models\Language;           // модель для языка
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountCreated;


class FreeLessonRequestController extends Controller

{

    public function index(Request $request)
    {
        $query = FreeLessonRequest::with(['language', 'lesson']);

        // При необходимости фильтры, например по роли:
        if ($request->filled('role')) {
            $query->where('requested_role', $request->role);
        }

        // пагинируем результат
        $requests = $query->orderBy('created_at', 'desc');

        return view('auth.admin.requests.index', compact('requests'));
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'      => 'required|string|max:255',
            'phone'     => 'required|string|max:50',
            'email'     => 'required|email|max:255|unique:free_lesson_requests,email',
            'language'  => 'required|exists:languages,id',
            'lesson_id' => 'required|exists:lessons,id',  // <-- здесь!
            'agreement' => 'required|accepted',
        ], [
            'name.required'       => 'Введите своё имя.',
            'phone.required'      => 'Введите номер телефона.',
            'email.required'      => 'Введите email.',
            'email.email'         => 'Введите корректный email.',
            'email.unique'        => 'Пользователь с таким email уже подал заявку.',
            'language.required'   => 'Выберите язык.',
            'language.exists'     => 'Выбран неверный язык.',
            'lesson_id.required'  => 'Выберите время урока.',      // кастомное сообщение
            'lesson_id.exists'    => 'Выбранное время урока недоступно.',
            'agreement.required'  => 'Нужно принять пользовательское соглашение.',
            'agreement.accepted'  => 'Нужно принять пользовательское соглашение.',
        ]);

        // Создаем заявку
        $freeRequest = FreeLessonRequest::create([
            'name'         => $validatedData['name'],
            'phone'        => $validatedData['phone'],
            'email'        => $validatedData['email'],
            'language_id'  => $validatedData['language'],
            'lesson_id'    => $validatedData['lesson_id'],  // сохраняем связь на урок
            'status'       => 'new',
        ]);

        // Удаляем request_id у слота, если вы раньше связывали через timetable:
        // теперь не нужно

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

    public function create()
    {
        // Готовим данные
        $languages = Language::all();

        // Пример получения уроков
        $testLessons = Lesson::with('timetable')
            ->where('type','test')
            ->where('date','>=', now()->addDay()->toDateString())
            ->where('status','scheduled')
            ->get();

        $availableDates = [];
        $timeSlots      = [];

        foreach($testLessons as $lesson) {
            $dateKey   = $lesson->date->format('Y-m-d');
            $dateLabel = $lesson->date->translatedFormat('d M Y (D)');

            $availableDates[$dateKey] = $dateLabel;

            $start = \Carbon\Carbon::parse($lesson->time);
            $end   = $start->copy()->addMinutes($lesson->timetable->duration);

            $timeSlots[$dateKey][] = [
                'id'         => $lesson->id,
                'start_time' => $start->format('H:i'),
                'end_time'   => $end->format('H:i'),
            ];
        }

        // **Вот ключевой момент** — именно эти три массива нужно прокинуть в шаблон:
        return view('main', [
            'languages'      => $languages,
            'availableDates' => $availableDates,
            'timeSlots'      => $timeSlots,
        ]);
    }

    public function createProfile(Request $request, $id)
    {
        $application = FreeLessonRequest::findOrFail($id);
        $randomPassword = Str::random(5);

        $user = User::create([
            'first_name' => $application->name,
            'email' => $application->email,
            'phone' => $application->phone,
            'role' => $application->requested_role,
            'password' => Hash::make($randomPassword),
        ]);

        // Если есть привязанный слот - создаем связь
        if ($timetable = Timetable::where('request_id', $application->id)->first()) {
            DB::table('timetable_user')->insert([
                'user_id' => $user->id,
                'timetable_id' => $timetable->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $application->update(['status' => 'approved']);
        Mail::to($application->email)->send(new AccountCreated($user, $randomPassword));

        return redirect()->back()->with('success', 'Личный кабинет успешно создан!');
    }

    public function destroy($id)
    {
        $application = FreeLessonRequest::findOrFail($id);

        // Освобождаем слот расписания
        if ($application->timetable) {
            DB::table('timetables')
                ->where('id', $application->timetable->id)
                ->update(['request_id' => null]);
        }

        $application->delete();

        return redirect()->back()->with('success', 'Заявка успешно удалена!');
    }








    // Создание профиля
//    public function createProfile(Request $request, $id)
//    {
////        set_time_limit(0);
//        // Получаем заявку по ID
//        $application = FreeLessonRequest::findOrFail($id);
//
//        // Генерация случайного пароля
//        $randomPassword = Str::random(5);
//
//        // Создаем пользователя, копируя необходимые данные из заявки.
//        // Внимательно подберите поля: убедитесь, что в таблице users есть нужные столбцы.
//        $user = User::create([
//            'first_name' => $application->name,
//            'email'      => $application->email,
//            'phone'      => $application->phone,
//            'role'       => $application->requested_role, // используем выбранную или измененную пользователем роль
//            'password'   => Hash::make($randomPassword),
//        ]);
//
//        // Обновляем статус заявки, чтобы отметить, что она обработана
//        $application->status = 'approved';  // статус можно подобрать по вашей бизнес-логике
//        $application->save();
//
//        // Отправляем уведомление на email с данными для входа.
//        // Здесь предполагается, что у вас есть Mailable класс AccountCreated.
//        Mail::to($application->email)->send(new AccountCreated($user, $randomPassword));
//
//        // Возвращаем обратный отклик (например, перенаправление с сообщением)
//        return redirect()->back()->with('success', 'Личный кабинет успешно создан! Проверьте свою почту.');
//    }

    public function createProfilesAll(Request $request)
    {
        set_time_limit(0);

        $pendingRequests = FreeLessonRequest::where('status', '<>', 'approved')->get();

        foreach ($pendingRequests as $application) {
            // 1) создаём пользователя
            $randomPassword = Str::random(8);
            $user = User::create([
                'first_name' => $application->name,
                'email'      => $application->email,
                'phone'      => $application->phone,
                'role'       => $application->requested_role,
                'password'   => Hash::make($randomPassword),
            ]);

            // 2) привязываем студента к уроку, если он выбран
            if ($application->lesson_id) {
                $lesson = Lesson::find($application->lesson_id);
                if ($lesson) {
                    // attach, если ещё нет
                    if (!$lesson->students()->where('user_id', $user->id)->exists()) {
                        $lesson->students()->attach($user->id);
                    }
                    // обновляем статус урока
                    $lesson->status = 'scheduled';
                    $lesson->save();
                }
            }

            // 3) отмечаем заявку как обработанную
            $application->status = 'approved';
            $application->save();

            // 4) отправляем письмо с данными
            Mail::to($application->email)
                ->send(new AccountCreated($user, $randomPassword));
        }

        return redirect()
            ->back()
            ->with('success', 'Все заявки успешно обработаны, личные кабинеты созданы и уроки привязаны.');
    }

}
