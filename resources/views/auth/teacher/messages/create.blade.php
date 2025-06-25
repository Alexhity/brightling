{{-- Форма отправки нового сообщения для учителя --}}
@extends('layouts.app')

@section('title','Новое сообщение')

@section('styles')
    <style>
        .teacher-content-wrapper {
            margin-left:200px;
            padding:20px;
            font-family:'Montserrat Medium',sans-serif;
        }
        .teacher-content-wrapper h2 {
            font-family:'Montserrat Bold',sans-serif;
            font-size:28px;
            margin-bottom:20px;
            text-align:center;
            color:#333333;
        }
        .message-form {
            background:#fff;
            padding:30px;
            border-radius:7px;
            box-shadow:0 2px 8px rgba(0,0,0,0.1);
            max-width:600px;
            margin:0 auto;
        }
        .form-group { margin-bottom:20px; }
        .form-group label { display:block; margin-bottom:6px; color:#333; }
        .form-group select,
        .form-group textarea {
            width:100%; padding:8px; font-size:14px;
            border:1px solid #ccc; border-radius:5px;
            transition:border-color .2s;
            font-family:'Montserrat Medium',sans-serif;
        }
        .form-group select:focus,
        .form-group textarea:focus { border-color:#615f5f; }
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
    @include('layouts.left_sidebar_teacher')

    <div class="teacher-content-wrapper">
        <h2>Новое сообщение</h2>

        <form id="teacher-message-form"
              action="{{ route('teacher.messages.store') }}"
              method="POST"
              class="message-form"
              novalidate>
            @csrf

            <div class="form-group">
                <label for="recipient_id">Кому</label>
                <select id="recipient_id" name="recipient_id" required>
                    <option value="" disabled selected>— выбрать —</option>
                    <option value="all_students" {{ old('recipient_id')=='all_students'?'selected':'' }}>Всем студентам</option>
                    <option value="admin" {{ old('recipient_id')=='admin'?'selected':'' }}>Администратору</option>
                    <optgroup label="Конкретный студент">
                        @foreach($students as $s)
                            <option value="{{ $s->id }}" {{ old('recipient_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->first_name }} {{ $s->last_name }}
                            </option>
                        @endforeach
                    </optgroup>
                </select>
            </div>

            <div class="form-group">
                <label for="question_text">Текст сообщения</label>
                <textarea id="question_text"
                          name="question_text"
                          rows="5"
                          placeholder="Напишите текст сообщения"
                          required>{{ old('question_text') }}</textarea>
            </div>

            <div class="form-group" style="text-align:center;">
                <button type="submit" class="btn-submit">Отправить</button>
            </div>
        </form>
    </div>

    {{-- Показ серверных ошибок --}}
    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const errs = @json($errors->all());
                const list = errs.map(m => `<li>${m}</li>`).join('');
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'Ошибка валидации',
                    html: `<ul style="text-align:left; margin:0">${list}</ul>`,
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true,
                    customClass: { popup: 'swal2-toast' }
                });
            });
        </script>
    @endif

    {{-- Клиентская валидация (по желанию) --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('teacher-message-form');
            form.addEventListener('submit', function(e) {
                const errs = [];
                const recipient = form.recipient_id.value;
                const text = form.question_text.value.trim();

                if (!recipient) errs.push('Выберите получателя.');
                if (!text) errs.push('Введите текст сообщения.');
                else if (text.length < 5) errs.push('Текст должен быть не короче 5 символов.');

                if (errs.length) {
                    e.preventDefault();
                    const list = errs.map(msg => `<li>${msg}</li>`).join('');
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Ошибка валидации',
                        html: `<ul style="text-align:left; margin:0">${list}</ul>`,
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true,
                        customClass: { popup: 'swal2-toast' }
                    });
                }
            });
        });
    </script>
@endsection
