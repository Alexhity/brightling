@extends('layouts.app')

@section('title', 'Админ-панель - Заявки')

@section('styles')
    <style>
        .admin-container {
            max-width: 1250px;

            font-family: 'Montserrat Medium', sans-serif;
            font-size: 15px;

            margin-left: 150px;
            /*width: calc(100% - 200px);*/
            padding: 20px;
        }

        /* Заявки на бесплатный урок */
        .requests-header {
            font-family: 'Montserrat Bold', sans-serif;
            font-size: 1.5em;
            color: #2B2D42;
            margin-bottom: 20px;
            text-align: center;
        }
        .requests-container {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            max-height: 500px;
            overflow-y: auto;
            background-color: #f8f9fa;
        }
        .request-table {
            width: 100%;
            border-collapse: collapse;
        }
        .request-table th,
        .request-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            font-family: 'Montserrat Medium', sans-serif;
            text-align: left;
        }
        .request-table th {
            background-color: #f1f1f1;
            font-family: 'Montserrat SemiBold', sans-serif;
            color: #2B2D42;
        }
        /* Кнопки */
        .action-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-family: 'Montserrat SemiBold', sans-serif;
            margin-bottom: 5px;
        }
        .btn-primary {
            background-color: #FFE644;
            color: #2B2D42;
        }
        .btn-primary:hover {
            background-color: #feca1c;
        }
        .btn-success {
            background-color: #8986FF;
            color: #fff;
        }
        .btn-success:hover {
            background-color: #5553a3;
        }
        /* Стили для селекта */
        .custom-select {
            padding: 6px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-family: 'Montserrat Medium', sans-serif;
        }
        /* Alert */
        .alert-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 10px;
            border-radius: 4px;
            color: #42674a;
            font-family: 'Montserrat Medium', sans-serif;
            margin-bottom: 20px;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_admin')
    <div class="admin-container">
        <!-- Заявки на бесплатный урок -->
        <h2 class="requests-header">Заявки на бесплатный урок</h2>

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <div class="requests-container">
            <!-- Форма для "Обработать все" -->
            <div style="margin-bottom: 20px;">
                <form action="{{ route('admin.requests.createProfilesAll') }}" method="POST">
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
                                <form action="{{ route('admin.requests.updateRole', $application->id) }}" method="POST">
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
                                <form action="{{ route('admin.requests.createProfile', $application->id) }}" method="POST">
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
    </div>

@endsection
