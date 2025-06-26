{{-- resources/views/auth/teacher/profile/edit-slot.blade.php --}}
@extends('layouts.app')

@section('styles')
    <style>
        .teacher-content-wrapper {
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
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #333;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: border-color 0.2s;
        }
        .form-group input:focus,
        .form-group select:focus,
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
            transition: background 0.3s;
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
            transition: transform 0.2s;
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
            transition: background 0.3s;
        }
        .btn-password:hover {
            background: #93edca;
        }
        /* Override базовых стилей Tom Select */
        .ts-control {
            background-color: #f0f8ff;
            border-radius: 5px;
        }
        .ts-control .ts-dropdown {
            border-radius: 0 0 5px 5px;
        }
        .ts-control .ts-dropdown .ts-option.ts-selected {
            background-color: #93edca;
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
        /* Tom Select overrides */
        .ts-control {
            background-color: #f0f8ff;
            border-radius: 5px;
        }
        .ts-control .ts-dropdown {
            border-radius: 0 0 5px 5px;
        }
        .ts-control .ts-dropdown .ts-option.ts-selected {
            background-color: #93edca;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_teacher')

    <div class="teacher-content-wrapper">
        {{-- Левая колонка --}}
        <div class="profile-main">
            @if(session('success'))

                <script>
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: @json(session('success')),
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true,
                    });
                </script>
            @endif

            <div class="profile-form">
                <h2>Редактировать профиль</h2>
                <form action="{{ route('teacher.profile.update') }}" novalidate
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
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
                    {{-- Имя + Фамилия --}}
                    <div class="form-row">
                        <div class="form-group">
                            <label>Имя</label>
                            <input type="text"
                                   name="first_name"
                                   value="{{ old('first_name', $user->first_name) }}"
                                   class="@error('first_name') input-error @enderror">
                            @error('first_name')<div class="error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Фамилия</label>
                            <input type="text"
                                   name="last_name"
                                   value="{{ old('last_name', $user->last_name) }}"
                                   class="@error('last_name') input-error @enderror">
                            @error('last_name')<div class="error">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Email + Телефон --}}
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email"
                                   name="email"
                                   value="{{ old('email', $user->email) }}"
                                   class="@error('email') input-error @enderror">
                            @error('email')<div class="error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Телефон</label>
                            <input type="text"
                                   name="phone"
                                   value="{{ old('phone', $user->phone) }}"
                                   class="@error('phone') input-error @enderror">
                            @error('phone')<div class="error">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Дата рождения --}}
                    <div class="form-group">
                        <label>Дата рождения</label>
                        <input type="date"
                               name="date_birthday"
                               value="{{ old('date_birthday', $user->date_birthday) }}"
                               class="@error('date_birthday') input-error @enderror">
                        @error('date_birthday')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    {{-- О себе --}}
                    <div class="form-group">
                        <label>О себе</label>
                        <textarea name="description"
                                  rows="4"
                                  class="@error('description') input-error @enderror">{{ old('description', $user->description) }}</textarea>
                        @error('description')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    {{-- Вместо отдельного <select name="level"> и Tom Select --}}
                    <div class="form-group">
                        <label>Языки и уровень</label>

                        {{-- Контейнер для строк языков --}}
                        <div id="languages-list">
                            @foreach($user->languages as $lang)
                                <div class="form-row mb-2" data-lang-id="{{ $lang->id }}">
                                    {{-- Скрытое поле, чтобы передать id языка --}}
                                    <input type="hidden" name="languages[]" value="{{ $lang->id }}">

                                    {{-- Название языка --}}
                                    <div class="form-group" style="flex:2">
                                        <input type="text"
                                               readonly
                                               value="{{ $lang->name }}"
                                               class="form-control-plaintext">
                                    </div>

                                    {{-- Выбор уровня --}}
                                    <div class="form-group" style="flex:1">
                                        <select name="languages_levels[{{ $lang->id }}]"
                                                class="form-control">
                                            <option value="">— уровень —</option>
                                            @foreach($levels as $key => $label)
                                                <option value="{{ $key }}"
                                                    {{ $lang->pivot->level === $key ? 'selected' : '' }}>
                                                    {{ strtoupper($label) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Удалить язык --}}
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger ml-2 remove-language">
                                        ✕
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        {{-- Кнопка «Добавить язык» --}}
                        <button type="button" id="add-language" class="btn btn-sm btn-primary">
                            + Добавить язык
                        </button>
                    </div>

                    {{-- Шаблон новой строки для языка --}}
                    <template id="language-template">
                        <div class="form-row mb-2 new-language">
                            <input type="hidden" name="languages[]" value="">
                            <div class="form-group" style="flex:2">
                                <select class="form-control lang-select">
                                    <option value="">— выберите язык —</option>
                                    @foreach($languages as $opt)
                                        <option value="{{ $opt->id }}">{{ $opt->name }}</option>
                                    @endforeach
                                </select>
                            </div>
{{--                            <div class="form-group" style="flex:1">--}}
{{--                                <select name="languages_levels[new]" class="form-control level-select">--}}
{{--                                    <option value="">— уровень —</option>--}}
{{--                                    @foreach($levels as $key => $label)--}}
{{--                                        <option value="{{ $key }}">{{ strtoupper($label) }}</option>--}}
{{--                                    @endforeach--}}
{{--                                </select>--}}
{{--                            </div>--}}
                            <button type="button" class="btn btn-sm btn-outline-danger ml-2 remove-language">
                                ✕
                            </button>
                        </div>
                    </template>
                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            const list = document.getElementById('languages-list');
                            const tmpl = document.getElementById('language-template').content;

                            // Добавить новую строку
                            document.getElementById('add-language').addEventListener('click', () => {
                                const clone = document.importNode(tmpl, true);
                                // По выбору языка подставляем value в hidden
                                const selectLang = clone.querySelector('.lang-select');
                                const hiddenInput = clone.querySelector('input[name="languages[]"]');
                                selectLang.addEventListener('change', () => {
                                    hiddenInput.value = selectLang.value;
                                });
                                list.appendChild(clone);
                            });

                            // Удалить строку
                            list.addEventListener('click', e => {
                                if (e.target.matches('.remove-language')) {
                                    e.target.closest('.form-row').remove();
                                }
                            });
                        });
                    </script>

                    {{-- Загрузка фото --}}
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

                {{-- Сертификаты --}}
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
                         alt="Avatar"
                         class="avatar-preview">
                @else
                    <p class="about-text">Нет фото</p>
                @endif


            </div>
            <a href="{{ route('teacher.profile.password.show') }}"
               class="btn-password"
                style="text-align: center"
            >
                Сменить пароль
            </a>
        </div>
    </div>
@endsection

{{--@section('scripts')--}}
{{--    @vite('resources/js/app.js')--}}

{{--    <script>--}}
{{--        document.addEventListener('DOMContentLoaded', () => {--}}
{{--            // Если вдруг нужно кастомное поведение--}}
{{--        });--}}
{{--    </script>--}}
{{--@endsection--}}
