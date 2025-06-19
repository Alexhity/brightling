<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherMessageController extends Controller
{
    // 1) Список сообщений с фильтром по статусу
    public function index(Request $request)
    {
        $allowed = ['pending','answered'];
        $statusFilter = in_array($request->query('status'), $allowed)
            ? $request->query('status')
            : null;

        $query = Message::with(['sender:id,first_name,last_name','recipient:id,first_name,last_name'])
            ->where(function($q){
                // все где я — отправитель или получатель
                $q->where('sender_id',Auth::id())
                    ->orWhere('recipient_id',Auth::id());
            })
            ->orderByDesc('question_sent_at');

        if ($statusFilter) {
            $query->where('status',$statusFilter);
        }

        $messages = $query->get();

        return view('auth.teacher.messages.index', compact('messages','statusFilter'));
    }

    // 2) Форма создания/рассылки
    public function create()
    {
        $me = Auth::user();
        // Список студентов всех моих курсов, уникально
        $studentIds = $me->courses
            ->flatMap->students
            ->pluck('id')
            ->unique();
        $students = User::whereIn('id',$studentIds)->get();

        return view('auth.teacher.messages.create', compact('students'));
    }

    // 3) Сохранение
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
        if ($data['recipient_id'] === 'all_students') {
            $studentIds = Auth::user()->courses
                ->flatMap->students
                ->pluck('id')
                ->unique();
            $recipients = User::whereIn('id', $studentIds)->get();
        }
        elseif ($data['recipient_id'] === 'admin') {
            // всем администраторам (обычно один)
            $recipients = User::where('role', 'admin')->get();
        }
        else {
            $recipients = User::where('id', $data['recipient_id'])->get();
        }

        // Исключаем себя
        $recipients = $recipients->filter(fn($u)=>$u->id!==$senderId);

        if ($recipients->isEmpty()) {
            return back()->withErrors(['recipient_id'=>'Нет доступных студентов'])->withInput();
        }

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
            ->route('teacher.messages.index')
            ->with('success','Сообщения успешно отправлены');
    }

    // 4) Просмотр / ответ
    public function show(Message $message)
    {
        // Разрешаем только свои
        if ($message->sender_id!==Auth::id() && $message->recipient_id!==Auth::id()) {
            abort(403);
        }
        return view('auth.teacher.messages.show', compact('message'));
    }

    // 5) Отправка ответа
    public function reply(Request $request, Message $message)
    {
        // Только если я — получатель
        if ($message->recipient_id !== Auth::id()) {
            abort(403);
        }
        $data = $request->validate([
            'answer_text'=>'required|string',
        ]);
        $message->update([
            'answer_text'    => $data['answer_text'],
            'answer_sent_at' => now(),
            'status'         => 'answered',
        ]);
        return redirect()
            ->route('teacher.messages.index')
            ->with('success','Ответ отправлен');
    }
}
