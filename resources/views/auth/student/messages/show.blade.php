@extends('layouts.app')

@section('title','Просмотр сообщения')

@section('styles')
    <style>
        .student-content-wrapper {
            display:flex; justify-content:center;
            margin-left:0; padding:20px;
            font-family:'Montserrat Medium',sans-serif;
        }
        .message-card {
            background:#fff; padding:30px;
            border-radius:7px; box-shadow:0 2px 8px rgba(0,0,0,0.1);
            width:700px; max-width:100%;
        }
        .message-card h2 {
            font-family:'Montserrat Bold',sans-serif;
            font-size:28px; margin-bottom:20px; text-align:center;
        }
        .field { margin-bottom:20px; }
        .field label { font-weight:600; display:block; margin-bottom:6px; }
        .field .value {
            background:#f9f9f9; padding:12px;
            border-radius:5px; font-size:14px;
        }
        .reply-form .form-group { margin-bottom:20px; }
        .reply-form textarea {
            width:100%; padding:8px; font-size:14px;
            border:1px solid #ccc; border-radius:5px;
        }
        .btn-submit {
            background:#beffe6; color:#333;
            padding:10px 20px; border:none; border-radius:7px;
            cursor:pointer; transition:background .3s;
            font-family:'Montserrat Medium',sans-serif;
        }
        .btn-submit:hover { background:#93edca; }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_student')

    <div class="student-content-wrapper">
        <div class="message-card">
            <h2>Сообщение {{ $message->sender_id===Auth::id()?'к': 'от' }}
                {{ $message->sender->first_name }} {{ $message->sender->last_name }}</h2>

            <div class="field">
                <label>Текст вопроса</label>
                <div class="value">{{ $message->question_text }}</div>
            </div>
            <div class="field">
                <label>Дата</label>
                <div class="value">{{ $message->question_sent_at->format('d.m.Y H:i') }}</div>
            </div>

            @if($message->status==='answered')
                <div class="field">
                    <label>Ответ</label>
                    <div class="value">{{ $message->answer_text }}</div>
                </div>
                <div class="field">
                    <label>Дата ответа</label>
                    <div class="value">{{ $message->answer_sent_at->format('d.m.Y H:i') }}</div>
                </div>
            @elseif($message->recipient_id===Auth::id())
                <form action="{{ route('student.messages.reply',$message) }}"
                      method="POST" class="reply-form" novalidate>
                    @csrf @method('PATCH')
                    <div class="form-group">
                        <label for="answer_text">Ваш ответ</label>
                        <textarea name="answer_text" id="answer_text" rows="4" required
                                  class="@error('answer_text') input-error @enderror">{{ old('answer_text') }}</textarea>
                        @error('answer_text')<div class="error">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn-submit">Отправить ответ</button>
                </form>
            @else
                <div class="field">
                    <div class="value">Вы можете только читать это сообщение.</div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                @if(session('success'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: @json(session('success')),
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true,
                });
                @endif
            });
        </script>
    @endpush
@endsection
