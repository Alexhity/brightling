@extends('layouts.app')

@section('styles')
    <style>
        .student-content-wrapper {
            display: flex;
            margin-left: 200px;
            padding: 30px;
            font-family: 'Montserrat Medium', sans-serif;
        }
        .profile-main {
            flex: 1;
            margin-right: 20px;
        }
        .profile-form {
            background: #fff;
            padding: 30px;
            border-radius: 7px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .profile-form h2 {
            font-family: 'Montserrat Bold', sans-serif;
            font-size: 28px;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }
        .form-row {
            display: flex;
            gap: 20px;
        }
        .form-row .form-group {
            flex: 1;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #333;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: border-color .2s;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #615f5f;
        }
        .input-error { border-color: #ff4c4c !important; }
        .error {
            color: #ff4c4c;
            font-size: 13px;
            margin-top: 4px;
        }
        .btn-submit {
            background: #beffe6;
            color: #333;
            padding: 10px 20px;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            transition: background .3s;
        }
        .btn-submit:hover {
            background: #93edca;
        }
        .profile-sidebar {
            width: 240px;
            position: sticky;
            top: 100px;
            align-self: flex-start;
        }
        .sidebar-section {
            background: #fff;
            padding: 30px;
            border-radius: 7px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
            margin-bottom: 20px;
        }
        .sidebar-section h2 {
            font-family: 'Montserrat Bold', sans-serif;
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }
        .avatar-preview {
            width: 100%;
            border-radius: 8px;
            margin-bottom: 20px;
            object-fit: cover;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: transform .2s;
        }
        .avatar-preview:hover {
            transform: translateY(-4px);
        }
        .btn-password {
            display: block;
            background: #beffe6;
            color: #333;
            padding: 10px 0;
            border-radius: 7px;
            text-decoration: none;
            margin-top: 10px;
            transition: background .3s;
            text-align: center;
        }
        .btn-password:hover {
            background: #93edca;
        }
        /* Сетка сертификатов: ровно 3 колонки */
        .certificates-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }
        .cert-item {
            text-align: center;
        }
        .cert-title {
            margin-bottom: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
        }
        /* Картинка 3:2 */
        .cert-image {
            width: 100%;
            aspect-ratio: 3 / 2;
            object-fit: cover;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_student')

    <div class="student-content-wrapper">
        {{-- Левая колонка с формами --}}
        <div class="profile-main">
            @if(session('success'))
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

            {{-- Редактирование профиля --}}
            <div class="profile-form">
                <h2>Редактировать профиль</h2>
                <form action="{{ route('student.profile.update') }}"
                      method="POST"
                      enctype="multipart/form-data"
                      novalidate>
                    @csrf
                    @method('PATCH')
                    <div class="form-row">
                        <div class="form-group">
                            <label>Имя</label>
                            <input type="text"
                                   name="first_name"
                                   value="{{ old('first_name',$user->first_name) }}"
                                   class="@error('first_name') input-error @enderror">
                            @error('first_name')<div class="error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Фамилия</label>
                            <input type="text"
                                   name="last_name"
                                   value="{{ old('last_name',$user->last_name) }}"
                                   class="@error('last_name') input-error @enderror">
                            @error('last_name')<div class="error">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email"
                                   name="email"
                                   value="{{ old('email',$user->email) }}"
                                   class="@error('email') input-error @enderror">
                            @error('email')<div class="error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Телефон</label>
                            <input type="text"
                                   name="phone"
                                   value="{{ old('phone',$user->phone) }}"
                                   class="@error('phone') input-error @enderror">
                            @error('phone')<div class="error">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Дата рождения</label>
                        <input type="date"
                               name="date_birthday"
                               value="{{ old('date_birthday',$user->date_birthday) }}"
                               class="@error('date_birthday') input-error @enderror">
                        @error('date_birthday')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label>Языки и уровень</label>
                        @foreach($user->languages as $lang)
                            <div class="form-row">
                                <div class="form-group" style="flex:2">
                                    <input type="text"
                                           readonly
                                           value="{{ $lang->name }}"
                                           class="form-control-plaintext">
                                </div>
                                <div class="form-group" style="flex:1">
                                    <select disabled class="form-control">
                                        <option>{{ strtoupper($lang->pivot->level ?? '—') }}</option>
                                    </select>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="form-group">
                        <label>Загрузить фото</label>
                        <input type="file"
                               name="avatar"
                               accept="image/*"
                               class="@error('avatar') input-error @enderror">
                        @error('avatar')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn-submit">Сохранить</button>
                </form>
            </div>

            {{-- Блок сертификатов --}}
            <div class="profile-form">
                <h2>Сертификаты</h2>

                @if($certificates->isEmpty())
                    <p class="about-text">Нет сертификатов</p>
                @else
                    <div class="certificates-grid">
                        @foreach($certificates as $cert)
                            <div class="cert-item">
                                <h3 class="cert-title">{{ $cert->title }}</h3>
                                <a href="{{ asset('images/certificates/' . $cert->file_path) }}" target="_blank">
                                    <img src="{{ asset('images/certificates/' . $cert->file_path) }}"
                                         alt="{{ $cert->title }}"
                                         class="cert-image">
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Правая панель --}}
        <div class="profile-sidebar">
            <div class="sidebar-section">
                <h2>Аватар</h2>
                @if($user->file_path)
                    <img src="{{ asset('images/profile/' . $user->file_path) }}"
                         alt="Avatar" class="avatar-preview">
                @else
                    <p class="about-text">Нет фото</p>
                @endif
            </div>

            <a href="{{ route('student.profile.password.show') }}" class="btn-password">
                Сменить пароль
            </a>
        </div>
    </div>
@endsection
