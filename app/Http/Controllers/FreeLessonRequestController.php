<?php

namespace App\Http\Controllers;

use App\Models\Timetable;
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
        $requests = FreeLessonRequest::query();

        if ($request->filled('role')) {
            $requests->where('requested_role', $request->role);
        }

        return view('admin.requests.index', [
            'requests' => $requests->paginate(20)
        ]);
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'required|email|max:255|unique:free_lesson_requests,email',
            'language' => 'required|exists:languages,id',
            'timetable_slot_id' => 'required|exists:timetables,id',
            'agreement' => 'required|accepted'
        ]);

        // Создаем заявку
        $freeRequest = FreeLessonRequest::create([
            'name' => $validatedData['name'],
            'phone' => $validatedData['phone'],
            'email' => $validatedData['email'],
            'language_id' => $validatedData['language'],
            'status' => 'new',
        ]);

        // Привязываем слот к заявке через request_id
        Timetable::where('id', $validatedData['timetable_slot_id'])
            ->update(['request_id' => $freeRequest->id]);

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
        $languages = Language::all();

        // Получаем доступные тестовые слоты
        $testSlots = Timetable::where('type', 'test')
            ->where('active', true)
            ->where('cancelled', false)
            ->where('is_public', true)
            ->whereNull('request_id')
            ->where(function($query) {
                $query->where('date', '>=', now()->format('Y-m-d'))
                    ->orWhereNull('date');
            })
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        // Формируем список доступных дат
        $availableDates = [];
        $timeSlots = [];

        foreach ($testSlots as $slot) {
            $dateKey = $slot->date ? $slot->date->format('Y-m-d') : $slot->weekday;
            $dateLabel = $slot->date
                ? $slot->date->translatedFormat('d M Y (D)')
                : ucfirst($slot->weekday) . ' (регулярно)';

            // Добавляем дату, если её еще нет
            if (!isset($availableDates[$dateKey])) {
                $availableDates[$dateKey] = $dateLabel;
            }

            // Формируем данные о времени
            $endTime = \Carbon\Carbon::parse($slot->start_time)
                ->addMinutes($slot->duration)
                ->format('H:i');

            $timeSlots[$dateKey][] = [
                'id' => $slot->id,
                'start_time' => $slot->start_time,
                'end_time' => $endTime
            ];
        }

        return view('free_lesson_form', compact(
            'languages',
            'availableDates',
            'timeSlots'
        ));
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
