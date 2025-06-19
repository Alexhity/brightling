@extends('layouts.app')

@section('title','Новое сообщение')

@section('styles')
    <style>
        .teacher-content-wrapper {
            margin-left:200px; padding:20px;
            font-family:'Montserrat Medium',sans-serif;
        }
        .message-form {
            background:#fff; padding:30px;
            border-radius:7px; box-shadow:0 2px 8px rgba(0,0,0,0.1);
            max-width:600px; margin:0 auto;
        }
        .message-form h2 {
            font-family:'Montserrat Bold',sans-serif;
            font-size:28px; margin-bottom:20px;
            text-align:center;
        }
        .form-group { margin-bottom:20px; }
        .form-group label { display:block; margin-bottom:6px; color:#333; }
        .form-group select,
        .form-group textarea {
            width:100%; padding:8px; font-size:14px;
            border:1px solid #ccc; border-radius:5px;
            transition:border-color .2s;
        }
        .form-group select:focus,
        .form-group textarea:focus { border-color:#615f5f; }
        .input-error { border-color:#ff4c4c!important; }
        .error { color:#ff4c4c; font-size:13px; margin-top:4px; }
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
        <form action="{{ route('teacher.messages.store') }}"
              method="POST" class="message-form" novalidate>
            @csrf

            <h2>Новое сообщение</h2>

            <div class="form-group">
                <label for="recipient_id">Кому</label>
                <select name="recipient_id" id="recipient_id"
                        class="@error('recipient_id') input-error @enderror"
                        required>
                    <option value="">— выбрать —</option>
                    <option value="all_students">Всем студентам</option>
                    <option value="admin">Администратору</option>   {{-- ← новая опция --}}
                    <optgroup label="Конкретный студент">
                        @foreach($students as $s)
                            <option value="{{ $s->id }}"
                                {{ old('recipient_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->first_name }} {{ $s->last_name }}
                            </option>
                        @endforeach
                    </optgroup>
                </select>
                @error('recipient_id')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="question_text">Текст сообщения</label>
                <textarea name="question_text" id="question_text" rows="5"
                          class="@error('question_text') input-error @enderror"
                          required>{{ old('question_text') }}</textarea>
                @error('question_text')<div class="error">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn-submit">Отправить</button>
        </form>
    </div>
@endsection
