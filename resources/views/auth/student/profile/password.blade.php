@extends('layouts.app')

@section('styles')
    <style>
        .student-content-wrapper {
            display: flex;
            margin-left: 200px;
            padding: 30px;
            justify-content: center;
            font-family: 'Montserrat Medium', sans-serif;
        }
        .profile-form {
            background: #fff;
            padding: 30px;
            border-radius: 7px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            width: 400px;
        }
        .profile-form h2 {
            font-family: 'Montserrat Bold', sans-serif;
            font-size: 28px;
            margin-bottom: 20px;
            text-align: center;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom:6px; color:#333; }
        .form-group input {
            width:100%; padding:8px; font-size:14px;
            border:1px solid #ccc; border-radius:5px;
            transition:border-color .2s;
        }
        .form-group input:focus { border-color:#615f5f; }
        .input-error { border-color:#ff4c4c!important; }
        .error { color:#ff4c4c; font-size:13px; margin-top:4px; }
        .btn-submit {
            background:#beffe6; color:#333;
            padding:10px 20px; border:none; border-radius:7px;
            width:100%; cursor:pointer; transition:background .3s;
            font-family:'Montserrat Medium',sans-serif;
        }
        .btn-submit:hover { background:#93edca; }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_student')

    <div class="student-content-wrapper">
        <div class="profile-form">
            <h2>Сменить пароль</h2>
            @if(session('success'))
                <script>
                    Swal.fire({
                        toast:true, position:'top-end',
                        icon:'success',
                        title:@json(session('success')),
                        showConfirmButton:false,
                        timer:3000, timerProgressBar:true
                    });
                </script>
            @endif

            <form action="{{ route('student.profile.password.update') }}"
                  method="POST" novalidate>
                @csrf
                @method('PATCH')

                <div class="form-group">
                    <label>Текущий пароль</label>
                    <input type="password" name="current_password"
                           class="@error('current_password') input-error @enderror">
                    @error('current_password')<div class="error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label>Новый пароль</label>
                    <input type="password" name="password" minlength="5"
                           class="@error('password') input-error @enderror">
                    @error('password')<div class="error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label>Подтвердите пароль</label>
                    <input type="password" name="password_confirmation" minlength="5">
                </div>

                <button type="submit" class="btn-submit">
                    Изменить пароль
                </button>
            </form>
        </div>
    </div>
@endsection
