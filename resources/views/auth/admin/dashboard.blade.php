@extends('layouts.app')

@section('title', 'Админ-панель - Заявки')

@section('styles')
    @vite(['resources/css/app.css', 'resources/css/admin.css'])
@endsection

@section('content')
    @include('layouts.left_sidebar_admin')
    <div class="admin-container">
        <div class="admin-header">
            <h1>Панель администратора</h1>
        </div>

        <!-- Статистика -->
        <div class="statistics">
            <div class="statistic-card">
                <h5>Пользователи</h5>
                <p>{{ $stats['users'] }}</p>
            </div>
            <div class="statistic-card" style="background-color: #eff7ff;">
                <h5>Курсы</h5>
                <p>{{ $stats['courses'] }}</p>
            </div>
            <div class="statistic-card" style="background-color: #fef6e0;">
                <h5>Языки</h5>
                <p>{{ $stats['languages'] }}</p>
            </div>
        </div>

        <!-- Последние пользователи -->
        <div class="latest-users">
            <div class="latest-users-header">Последние добавления</div>
            <table class="latest-users-table">
                <thead>
                <tr>
                    <th>Имя</th>
                    <th>Email</th>
                    <th>Роль</th>
                </tr>
                </thead>
                <tbody>
                @foreach($recentUsers as $user)
                    <tr>
                        <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->role }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- Заявки на бесплатный урок -->
        <h2 class="requests-header">Заявки на бесплатный урок</h2>

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <div class="requests-container">
            <!-- Форма для "Обработать все" -->
            <div style="margin-bottom: 20px;">
                <form action="{{ route('free_lesson_request.createProfilesAll') }}" method="POST">
                    @csrf
                    <button type="submit" class="action-btn btn-primary" onclick="return confirm('Вы действительно хотите обработать все заявки и создать личные кабинеты для всех пользователей ниже?');">
                        Обработать все
                    </button>
                </form>
            </div>

            @if($requests->isEmpty())
                <p>Нет новых заявок.</p>
            @else
                <table class="request-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Имя</th>
                        <th>Телефон</th>
                        <th>Email</th>
                        <th>Язык</th>
                        <th>Роль</th>
                        <th>Дата</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($requests as $application)
                        <tr>
                            <td>{{ $application->id }}</td>
                            <td>{{ $application->name }}</td>
                            <td>{{ $application->phone }}</td>
                            <td>{{ $application->email }}</td>
                            <td>{{ $application->language ? $application->language->name : 'Не выбран' }}</td>
                            <td>
                                <!-- Форма для изменения запрошенной роли -->
                                <form action="{{ route('free_lesson_request.updateRole', $application->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <select name="requested_role" class="custom-select" onchange="this.form.submit()">
                                        <option value="student" {{ $application->requested_role === 'student' ? 'selected' : '' }}>Student</option>
                                        <option value="teacher" {{ $application->requested_role === 'teacher' ? 'selected' : '' }}>Teacher</option>
                                        <option value="admin"   {{ $application->requested_role === 'admin'   ? 'selected' : '' }}>Admin</option>
                                    </select>
                                </form>
                            </td>
                            <td>{{ $application->created_at->format('d.m.Y H:i') }}</td>
                            <td>
                                <!-- Форма для обработки (создания личного кабинета) для отдельной заявки -->
                                <form action="{{ route('admin.courses.students.edit', $application->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="action-btn btn-success" onclick="return confirm('Вы действительно хотите обработать заявку и создать личный кабинет?');">
                                        Обработать
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>


        <!-- Раздел управления языками -->
        <div class="lang-container">
            <h2>Управление языками</h2>

            @if(session('success'))
                <div class="alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Форма для добавления нового языка -->
            <div class="lang-form" style="margin-bottom: 20px;">
                <form action="{{ route('admin.language.store') }}" method="POST">
                    @csrf
                    <input type="text" name="name" placeholder="Введите название нового языка" required>
                    <button type="submit">Добавить язык</button>
                </form>
            </div>

            <!-- Список добавленных языков -->
            <div class="lang-list">
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Язык</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($languages as $lang)
                        <tr>
                            <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{ $lang->id }}</td>
                            <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{ $lang->name }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="course-form">
            <h2>Создать новый курс</h2>

            @if ($errors->any())
                <div class="alert alert-danger" style="margin-bottom:20px;">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.courses.store') }}" method="POST">
                @csrf

                <label for="title">Название курса</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required>

                <label for="description">Описание курса</label>
                <textarea name="description" id="description" rows="4">{{ old('description') }}</textarea>

                <label for="format">Формат</label>
                <input type="text" name="format" id="format" value="{{ old('format') }}" placeholder="Например: онлайн, оффлайн" required>

                <label for="age_group">Возрастная группа</label>
                <input type="text" name="age_group" id="age_group" value="{{ old('age_group') }}" placeholder="Например: 10-15">

                <label for="lesson_count">Количество уроков</label>
                <input type="number" name="lesson_count" id="lesson_count" value="{{ old('lesson_count') }}">

                <label for="duration">Длительность курса</label>
                <input type="text" name="duration" id="duration" value="{{ old('duration') }}" placeholder="Например: 6 месяцев" required>

                <label for="teacher_id">Преподаватель</label>
                <select name="teacher_id" id="teacher_id" required>
                    <option value="">Выберите преподавателя</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->first_name }} {{ $teacher->last_name }}
                        </option>
                    @endforeach
                </select>

                <label for="language_id">Язык</label>
                <select name="language_id" id="language_id" required>
                    <option value="">Выберите язык курса</option>
                    @foreach($languages as $lang)
                        <option value="{{ $lang->id }}" {{ old('language_id') == $lang->id ? 'selected' : '' }}>
                            {{ $lang->name }}
                        </option>
                    @endforeach
                </select>

                <label for="pricing_id">Тариф (Цена)</label>
                <select name="pricing_id" id="pricing_id" required>
                    <option value="">Выберите тариф</option>
                    @foreach($pricings as $price)
                        <option value="{{ $price->id }}" {{ old('pricing_id') == $price->id ? 'selected' : '' }}>
                            {{ $price->title }} - {{ $price->amount }} {{ $price->currency }}
                        </option>
                    @endforeach
                </select>

                <button type="submit">Создать курс</button>
            </form>
        </div>

{{--        <div class="container">--}}
{{--            @foreach($courses as $course)--}}
{{--            <h1>Добавить студентов в курс: {{ $course->title }}</h1>--}}

{{--            @if(session('success'))--}}
{{--                <div class="alert-success">--}}
{{--                    {{ session('success') }}--}}
{{--                </div>--}}
{{--            @endif--}}

{{--            <form action="{{ route('admin.courses.students.update', $course->id) }}" method="POST">--}}
{{--                @csrf--}}
{{--                <label for="student_ids">Выберите студентов для добавления (Ctrl+Click для множественного выбора):</label>--}}
{{--                <select name="student_ids[]" id="student_ids" multiple required>--}}
{{--                    @forelse($availableStudents as $student)--}}
{{--                        <option value="{{ $student->id }}">--}}
{{--                            {{ $student->first_name }} {{ $student->last_name }} ({{ $student->email }})--}}
{{--                        </option>--}}
{{--                    @empty--}}
{{--                        <option disabled>Нет доступных студентов для добавления</option>--}}
{{--                    @endforelse--}}
{{--                </select>--}}
{{--                <button type="submit">Добавить студентов</button>--}}
{{--            </form>--}}
{{--            @endforeach--}}
{{--        </div>--}}
    </div>


@endsection
