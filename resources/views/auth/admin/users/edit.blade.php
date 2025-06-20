
@extends('layouts.app')

@section('styles')
    <style>
        .admin-content-wrapper {
            margin-left: 200px;
            font-family: 'Montserrat Medium', sans-serif;
            width: calc(100% - 200px);
        }
        .admin-content-wrapper h2 {
            font-family: 'Montserrat Bold', sans-serif;
            font-size: 32px;
            color: #333333;
            margin-top: 25px;
            margin-bottom: 15px;
            text-align: center;
        }
        .course-form {
            background: #ffffff;
            padding: 30px;
            border-radius: 7px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 40px auto;
        }
        .form-group {
            position: relative;
            margin-bottom: 16px;
            padding-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 4px;
            font-family: 'Montserrat SemiBold', sans-serif;
            color: #333333;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            font-size: 14px;
            font-family: 'Montserrat Medium', sans-serif;
            border: 1px solid #cccccc;
            border-radius: 5px;
            transition: border-color 0.2s;
            background-color: #ffffff;
        }
        .form-group select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12'><path fill='%23666' d='M2 4l4 4 4-4z'/></svg>");
            background-repeat: no-repeat;
            background-position: right 10px center;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #615f5f;
        }
        .input-error {
            border-color: #ff4c4c !important;
        }
        .error {
            position: absolute;
            bottom: 0;
            left: 0;
            font-size: 13px;
            color: #ff4c4c;
            font-family: 'Montserrat Medium', sans-serif;
        }
        .buttons {
            margin-top: 12px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }
        .btn-submit {
            background-color: #beffe6;
            color: #333333;
            padding: 10px 20px;
            font-size: 16px;
            font-family: 'Montserrat Medium', sans-serif;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
        }
        .btn-submit:hover {
            background-color: #93edca;
        }
        .btn-cancel {
            background-color: #f0f0f0;
            color: #333333;
            padding: 10px 20px;
            font-size: 16px;
            font-family: 'Montserrat Medium', sans-serif;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-cancel:hover {
            background-color: #d9d9d9;
        }
        .alert-success {
            background-color: #e6f7e6;
            color: #2e7d32;
            padding: 12px 20px;
            margin-bottom: 30px;
            border: 1px solid #c8e6c9;
            border-radius: 7px;
            font-family: 'Montserrat Medium', sans-serif;
            font-size: 16px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .required::after { content: " *"; color: #ff4c4c; }
        .optional::after { content: " (необязательно)"; color: #999; font-weight: normal; font-size: 0.9em; }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_admin')
    <div class="admin-content-wrapper">
        <h2>Редактировать пользователя</h2>

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="course-form" novalidate>
            @csrf
            @method('PUT')

            {{-- Имя --}}
            <div class="form-group">
                <label for="first_name" class="required">Имя</label>
                <input id="first_name" name="first_name" type="text"
                       value="{{ old('first_name', $user->first_name) }}" required
                       class="{{ $errors->has('first_name') ? 'input-error' : '' }}">
                @if($errors->has('first_name'))
                    <div class="error">Введите имя</div>
                @endif
            </div>

            {{-- Фамилия --}}
            <div class="form-group">
                <label for="last_name" class="optional">Фамилия</label>
                <input id="last_name" name="last_name" type="text"
                       value="{{ old('last_name', $user->last_name) }}"
                       class="{{ $errors->has('last_name') ? 'input-error' : '' }}">
                @if($errors->has('last_name'))
                    <div class="error">Введите фамилию</div>
                @endif
            </div>

            {{-- Email --}}
            <div class="form-group">
                <label for="email" class="required">Email</label>
                <input id="email" name="email" type="email"
                       value="{{ old('email', $user->email) }}" required
                       class="{{ $errors->has('email') ? 'input-error' : '' }}">
                @if($errors->has('email'))
                    <div class="error">Введите email</div>
                @endif
            </div>

            {{-- Телефон --}}
            <div class="form-group">
                <label for="phone" class="required">Телефон</label>
                <input id="phone" name="phone" type="text"
                       value="{{ old('phone', $user->phone) }}" required
                       class="{{ $errors->has('phone') ? 'input-error' : '' }}">
                @if($errors->has('phone'))
                    <div class="error">Введите номер телефона</div>
                @endif
            </div>

            {{-- Дата рождения --}}
            <div class="form-group">
                <label for="date_birthday" class="optional">Дата рождения</label>
                <input id="date_birthday" name="date_birthday" type="date"
                       value="{{ old('date_birthday', $user->date_birthday) }}"
                       class="{{ $errors->has('date_birthday') ? 'input-error' : '' }}">
                @if($errors->has('date_birthday'))
                    <div class="error">{{ $errors->first('date_birthday') }}</div>
                @endif
            </div>

            {{-- Уровень --}}
            <div class="form-group">
                <label for="level" class="optional">Уровень</label>
                <select id="level" name="level"
                        class="{{ $errors->has('level') ? 'input-error' : '' }}">
                    <option value="" disabled {{ old('level', $user->level) ? '' : 'selected' }}>Выберите уровень</option>
                    @foreach(['beginner','A1','A2','B1','B2','C1','C2'] as $lvl)
                        <option value="{{ $lvl }}" {{ old('level', $user->level)==$lvl ? 'selected' : '' }}>
                            {{ $lvl == 'beginner' ? 'начинающий' : $lvl }}
                        </option>
                    @endforeach
                </select>
                @if($errors->has('level'))
                    <div class="error">{{ $errors->first('level') }}</div>
                @endif
            </div>

            {{-- Описание --}}
            <div class="form-group">
                <label for="description" class="optional">Описание</label>
                <textarea id="description" name="description" rows="4"
                          class="{{ $errors->has('description') ? 'input-error' : '' }}">{{ old('description', $user->description) }}</textarea>
                @if($errors->has('description'))
                    <div class="error">{{ $errors->first('description') }}</div>
                @endif
            </div>

            {{-- Роль --}}
            <div class="form-group">
                <label for="role" class="required">Роль</label>
                <select id="role" name="role" required
                        class="{{ $errors->has('role') ? 'input-error' : '' }}">
                    <option value="" disabled {{ old('role', $user->role) ? '' : 'selected' }}>Выберите роль</option>
                    <option value="student" {{ old('role', $user->role)=='student' ? 'selected' : '' }}>Студент</option>
                    <option value="teacher" {{ old('role', $user->role)=='teacher' ? 'selected' : '' }}>Учитель</option>
                    <option value="admin" {{ old('role', $user->role)=='admin' ? 'selected' : '' }}>Администратор</option>
                </select>
                @if($errors->has('role'))
                    <div class="error">{{ $errors->first('role') }}</div>
                @endif
            </div>

            <div class="buttons">
                <button type="submit" class="btn-submit">Сохранить</button>
                <a href="{{ route('admin.users.index') }}" class="btn-cancel">Отмена</a>
            </div>
        </form>
    </div>
@endsection
