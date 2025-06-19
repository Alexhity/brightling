{{-- resources/views/auth/teacher/profile/password.blade.php --}}
@extends('layouts.app')

@section('styles')
    <style>
        .teacher-content-wrapper {
            display: flex;
            margin-left: 200px;
            padding: 30px;
            font-family: 'Montserrat Medium', sans-serif;
            justify-content: center;
        }

        .profile-form {
            background: #ffffff;
            padding: 30px;
            border-radius: 7px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            width: 400px;
        }

        .profile-form h2 {
            font-family: 'Montserrat Bold', sans-serif;
            font-size: 28px;
            color: #333333;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #333333;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            font-size: 14px;
            border: 1px solid #cccccc;
            border-radius: 5px;
            transition: border-color 0.2s;
        }
        .form-group input:focus {
            border-color: #615f5f;
        }
        .input-error {
            border-color: #ff4c4c !important;
        }
        .error {
            color: #ff4c4c;
            font-size: 13px;
            margin-top: 4px;
        }
        .btn-submit {
            background-color: #beffe6;
            color: #333333;
            padding: 10px 20px;
            border: none;
            border-radius: 7px;
            width: 100%;
            cursor: pointer;
            transition: background-color 0.3s;
            font-family: 'Montserrat Medium', sans-serif;
        }
        .btn-submit:hover {
            background-color: #93edca;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_teacher')

    <div class="teacher-content-wrapper">
        <div class="profile-form">
            <h2>Сменить пароль</h2>

            @if(session('success'))
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: @json(session('success')),
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                </script>
            @endif

            <form action="{{ route('teacher.profile.password.update') }}" method="POST" novalidate>
                @csrf
                @method('PATCH')

                <div class="form-group">
                    <label for="current_password">Текущий пароль</label>
                    <input id="current_password"
                           type="password"
                           name="current_password"
                           class="@error('current_password') input-error @enderror">
                    @error('current_password')
                    <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Новый пароль</label>
                    <input id="password"
                           type="password"
                           name="password"
                           minlength="5"
                           class="@error('password') input-error @enderror">
                    @error('password')
                    <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Подтвердите пароль</label>
                    <input id="password_confirmation"
                           type="password"
                           name="password_confirmation"
                           minlength="5">
                </div>

                <button type="submit" class="btn-submit">Изменить пароль</button>
            </form>
        </div>
    </div>
@endsection
