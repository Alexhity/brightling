<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMessageController extends Controller
{
    public function index(Request $request)
    {
        // Разрешённые статусы для фильтрации
        $allowedStatuses = ['pending', 'answered'];

        // Получаем статус из GET-параметров, если он валиден
        $statusFilter = $request->query('status');
        if (!in_array($statusFilter, $allowedStatuses)) {
            $statusFilter = null;
        }

        // Базовый запрос с eager‑loading отправителя и получателя
        $query = Message::with([
            'sender:id,first_name,last_name',
            'recipient:id,first_name,last_name'
        ])
            ->orderByDesc('question_sent_at');

        // Применяем фильтр, если задан
        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        // Получаем коллекцию сообщений
        $messages = $query->get();

        // Передаём в шаблон список сообщений и текущий фильтр
        return view('auth.admin.messages.index', [
            'messages'     => $messages,
            'statusFilter' => $statusFilter,
        ]);
    }

    // Форма создания нового сообщения
    public function create()
    {
        $allUsers  = User::select('id','first_name','last_name','role')->get();
        $students  = $allUsers->where('role','student');
        $teachers  = $allUsers->where('role','teacher');

        return view('auth.admin.messages.create', compact('allUsers','students','teachers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'recipient_id'  => 'required',
            'question_text' => 'required|string',
        ]);

        $senderId = Auth::id();
        $text     = $data['question_text'];
        $when     = now();

        // Определяем список получателей
        if ($data['recipient_id'] === 'all_users') {
            $recipients = User::all();
        } elseif ($data['recipient_id'] === 'all_students') {
            $recipients = User::where('role','student')->get();
        } elseif ($data['recipient_id'] === 'all_teachers') {
            $recipients = User::where('role','teacher')->get();
        } else {
            $recipients = User::where('id', $data['recipient_id'])->get();
        }

        // Убираем из списка самого отправителя
        $recipients = $recipients->filter(function($user) use ($senderId) {
            return $user->id !== $senderId;
        });

        // Если после фильтрации никого не осталось — возвращаем ошибку
        if ($recipients->isEmpty()) {
            return back()
                ->withErrors(['recipient_id' => 'Нет доступных получателей.'])
                ->withInput();
        }

        // Создаём по одному сообщению на каждого получателя
        foreach ($recipients as $user) {
            Message::create([
                'sender_id'        => $senderId,
                'recipient_id'     => $user->id,
                'question_text'    => $text,
                'question_sent_at' => $when,
                'status'           => 'pending',
            ]);
        }

        return redirect()
            ->route('admin.messages.index')
            ->with('success','Сообщения успешно отправлены');
    }



    // Просмотр и форма ответа
    public function show(Message $message)
    {
        return view('auth.admin.messages.show', compact('message'));
    }

    // Отправка ответа
    public function reply(Request $request, Message $message)
    {
        $data = $request->validate([
            'answer_text' => 'required|string',
        ]);

        $message->update([
            'answer_text'    => $data['answer_text'],
            'answer_sent_at' => now(),
            'status'         => 'answered',
        ]);

        return redirect()
            ->route('admin.messages.index')
            ->with('success','Ответ отправлен');
    }
}
