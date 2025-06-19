{{-- Просмотр и ответ на сообщение --}}
@extends('layouts.app')

@section('title','Просмотр сообщения')

@section('styles')
    <style>
        .admin-content-wrapper {
            margin-left: 200px;
            display: flex;               /* новый стиль */
            justify-content: center;     /* центрируем по горизонтали */
            padding: 30px;
            font-family: 'Montserrat Medium', sans-serif;

        }
        .message-card {
            background: #fff;
            padding: 30px;
            border-radius: 7px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            width: 700px;                /* фиксированная ширина */
            max-width: 100%;             /* чтобы не вылазило на мобильных */
        }
        .message-card h2 {
            font-family:'Montserrat Bold',sans-serif;
            font-size:28px; margin-bottom:20px;
            text-align:center;
        }
        .field {
            margin-bottom:20px;
        }
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
    @include('layouts.left_sidebar_admin')

    <div class="admin-content-wrapper">
        <div class="message-card">
            <h2>Сообщение от {{ $message->sender->first_name }} {{ $message->sender->last_name }}</h2>

            <div class="field">
                <label>Текст вопроса</label>
                <div class="value">{{ $message->question_text }}</div>
            </div>

            <div class="field">
                <label>Дата</label>
                <div class="value">{{ $message->question_sent_at->format('d.m.Y H:i') }}</div>
            </div>

            {{-- Если уже отвечено, показываем ответ всем --}}
            @if($message->status === 'answered')
                <div class="field">
                    <label>Ответ</label>
                    <div class="value">{{ $message->answer_text }}</div>
                </div>
                <div class="field">
                    <label>Дата ответа</label>
                    <div class="value">{{ $message->answer_sent_at->format('d.m.Y H:i') }}</div>
                </div>

                {{-- Иначе, если я — получатель, показываем форму ответа --}}
            @elseif($message->recipient_id === auth()->id())
                <form action="{{ route('admin.messages.reply', $message) }}"
                      method="POST"
                      class="reply-form"
                      novalidate>
                    @csrf
                    @method('PATCH')

                    <div class="form-group">
                        <label for="answer_text">Ваш ответ</label>
                        <textarea name="answer_text"
                                  id="answer_text"
                                  rows="4"
                                  required
                                  class="@error('answer_text') input-error @enderror">{{ old('answer_text') }}</textarea>
                        @error('answer_text')
                        <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn-submit">Отправить ответ</button>
                </form>

                {{-- Иначе — это сообщение, которое админ сам отправил --}}
            @else
                <div class="field">
                    <label>Статус</label>
                    <div class="value">
                        {{-- Pending или отвечено? --}}
                        @if($message->status === 'pending')
                            <span class="badge-pending">Ожидает</span>
                        @else
                            <span class="badge-answered">Отвечено</span>
                        @endif
                    </div>
                </div>
                <div class="alert">
                    Вы не можете отвечать на собственные исходящие сообщения.
                </div>
            @endif

        </div>
    </div>
@endsection
