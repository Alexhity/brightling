<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentMessageController extends Controller
{
    public function index(Request $request)
    {
        $allowed = ['pending','answered'];
        $statusFilter = in_array($request->query('status'), $allowed)
            ? $request->query('status')
            : null;

        $query = Message::with(['sender:id,first_name,last_name','recipient:id,first_name,last_name'])
            ->where(function($q){
                $me = Auth::id();
                $q->where('sender_id',$me)
                    ->orWhere('recipient_id',$me);
            })
            ->orderByDesc('question_sent_at');

        if ($statusFilter) {
            $query->where('status',$statusFilter);
        }

        $messages = $query->get();
        return view('auth.student.messages.index', compact('messages','statusFilter'));
    }

    public function create()
    {
        // Получаем всех админов
        $admins = User::where('role','admin')->get();
        // Получаем преподавателей своих курсов
        $teacherIds = Auth::user()->courses
            ->flatMap->teachers
            ->pluck('id')
            ->unique();
        $teachers = User::whereIn('id',$teacherIds)->get();

        return view('auth.student.messages.create', compact('admins','teachers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'recipient_id'  => 'required',
            'question_text' => 'required|string',
        ]);

        $senderId = Auth::id();
        $when     = now();
        $text     = $data['question_text'];

        // Определяем получателей
        if ($data['recipient_id']==='admin') {
            $recipients = User::where('role','admin')->get();
        }
        elseif ($data['recipient_id']==='all_teachers') {
            $teacherIds = Auth::user()->courses
                ->flatMap->teachers
                ->pluck('id')
                ->unique();
            $recipients = User::whereIn('id',$teacherIds)->get();
        } else {
            $recipients = User::where('id',$data['recipient_id'])->get();
        }

        // Исключаем себя
        $recipients = $recipients->filter(fn($u)=>$u->id!==$senderId);
        if ($recipients->isEmpty()) {
            return back()
                ->withErrors(['recipient_id'=>'Нет доступных получателей'])
                ->withInput();
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
            ->route('student.messages.index')
            ->with('success','Сообщения успешно отправлены');
    }

    public function show(Message $message)
    {
        $me = Auth::id();
        if ($message->sender_id!==$me && $message->recipient_id!==$me) {
            abort(403);
        }
        return view('auth.student.messages.show', compact('message'));
    }

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
            ->route('student.messages.index')
            ->with('success','Ответ отправлен');
    }
}
