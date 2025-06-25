@extends('layouts.app')

@section('styles')
    <style>
        .admin-content-wrapper {
            margin-left: 200px;
            width: calc(100% - 200px);
            font-family: 'Montserrat Medium', sans-serif;
        }
        .admin-content-wrapper h2 {
            font-family: 'Montserrat Bold', sans-serif;
            font-size: 32px;
            color: #333333;
            margin: 30px 0;
            text-align: center;
        }
        .course-form {
            background: #fff;
            padding: 30px;
            border-radius: 7px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 0 auto 40px;
        }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; margin-bottom: 4px; }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;
            font-family: 'Montserrat Medium', sans-serif; font-size: 14px;
            transition: border-color .2s;
        }
        .form-group input::placeholder,
        .form-group textarea::placeholder {
            font-size: 14px; color: #999; opacity: 1;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus { outline: none; border-color: #615f5f; }
        .buttons { display: flex; justify-content: flex-end; gap: 8px; margin-top: 12px; }
        .btn-submit { background: #beffe6; padding: 10px 20px; border: none; border-radius: 7px; cursor: pointer; }
        .btn-submit:hover { background: #93edca; }
        .btn-cancel { background: #f0f0f0; padding: 10px 20px; border-radius: 7px; text-decoration: none; display: inline-flex; align-items: center; }
        .required::after { content: " *"; color: #ff4c4c; }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_admin')
    <div class="admin-content-wrapper">
        <h2>Создать пользователя</h2>

        {{-- Ошибки валидации (5с) --}}
        @if($errors->any())
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const list = @json($errors->all()).map(e => `<li>${e}</li>`).join('');
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Ошибка валидации',
                        html: `<ul style="text-align:left; margin:0">${list}</ul>`,
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true,
                    });
                });
            </script>
        @endif

        {{-- Успех (3с) --}}
        @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: @json(session('success')),
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                });
            </script>
        @endif

        <form action="{{ route('admin.users.store') }}"
              method="POST"
              class="course-form"
              id="user-create-form"
              novalidate>
            @csrf

            <div class="form-group">
                <label for="first_name" class="required">Имя</label>
                <input id="first_name" name="first_name" type="text"
                       placeholder="Введите имя" value="{{ old('first_name') }}" required>
            </div>

            <div class="form-group">
                <label for="last_name">Фамилия</label>
                <input id="last_name" name="last_name" type="text"
                       placeholder="Введите фамилию" value="{{ old('last_name') }}">
            </div>

            <div class="form-group">
                <label for="email" class="required">Email</label>
                <input id="email" name="email" type="email"
                       placeholder="user@example.com" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label for="phone" class="required">Телефон</label>
                <input id="phone" name="phone" type="tel"
                       placeholder="+375 (__) ___ __ __" value="{{ old('phone') }}" required>
            </div>

            <div class="form-group">
                <label for="date_birthday" class="required">Дата рождения</label>
                <input id="date_birthday" name="date_birthday" type="date"
                       max="{{ now()->subDay()->toDateString() }}"
                       value="{{ old('date_birthday') }}" required>
            </div>

            <div class="form-group">
                <label for="role" class="required">Роль</label>
                <select id="role" name="role" required>
                    <option value="" disabled {{ old('role')?'':'selected' }}>Выберите роль</option>
                    <option value="admin"   {{ old('role')=='admin'   ?'selected':'' }}>Администратор</option>
                    <option value="teacher" {{ old('role')=='teacher' ?'selected':'' }}>Преподаватель</option>
                    <option value="student" {{ old('role')=='student' ?'selected':'' }}>Студент</option>
                </select>
            </div>

            <div class="form-group">
                <label for="description">Описание</label>
                <textarea id="description" name="description" rows="3"
                          placeholder="Необязательно">{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label for="password" class="required">Пароль</label>
                <input id="password" name="password" type="password"
                       placeholder="Минимум 6 символов" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="required">Подтверждение пароля</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required>
            </div>

            <div class="buttons">
                <button type="submit" class="btn-submit">Создать</button>
                <a href="{{ route('admin.users.index') }}" class="btn-cancel">Отмена</a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Маска телефона
            const phone = document.getElementById('phone');
            phone.addEventListener('focus', () => { if(!phone.value.startsWith('+375')) phone.value = '+375'; });
            phone.addEventListener('input', () => {
                let v = phone.value.replace(/[^\d+]/g,'');
                if (!v.startsWith('+375')) v = '+375'+v.replace(/^\+?375/,'');
                phone.value = '+375'+v.slice(4,13);
            });

            // JS-валидация с Toast
            const form = document.getElementById('user-create-form');
            form.addEventListener('submit', e => {
                const errs=[],
                    reEmail=/^[^\s@]+@[^\s@]+\.[^\s@]+$/,
                    rePhone=/^\+375\d{9}$/,
                    fn=form.first_name.value.trim(),
                    em=form.email.value.trim(),
                    ph=form.phone.value.trim(),
                    db=form.date_birthday.value,
                    rl=form.role.value,
                    pwd=form.password.value,
                    pc=form.password_confirmation.value;
                if(!fn) errs.push('Введите имя.');
                if(!reEmail.test(em)) errs.push('Введите корректный Email.');
                if(!rePhone.test(ph)) errs.push('Телефон в формате +375XXXXXXXXX.');
                if(!db) errs.push('Укажите дату рождения.');
                else if(new Date(db)>=new Date((new Date()).setHours(0,0,0,0)))
                    errs.push('Дата рождения не может быть сегодня или в будущем.');
                if(!rl) errs.push('Выберите роль.');
                if(pwd.length<6) errs.push('Пароль должен быть не менее 6 символов.');
                if(pwd!==pc) errs.push('Пароли не совпадают.');
                if(errs.length){
                    e.preventDefault();
                    const list=errs.map(e=>`<li>${e}</li>`).join('');
                    Swal.fire({
                        toast:true,position:'top-end',icon:'error',
                        title:'Ошибка валидации',
                        html:`<ul style="text-align:left">${list}</ul>`,
                        showConfirmButton:false,timer:5000,timerProgressBar:true
                    });
                }
            });
        });
    </script>
@endsection
