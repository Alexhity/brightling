@extends('layouts.app')

@section('title', 'Админ-панель - Статистика')

@section('styles')
    <style>
        /* Основной контейнер админ-панели */
        .admin-container {
            max-width: 1250px;

            font-family: 'Montserrat Medium', sans-serif;
            font-size: 18px;

            margin-left: 200px;
            /*width: calc(100% - 270px);*/
            padding: 20px;
        }

        /* Статистика */
        .statistics {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 40px;
        }
        .statistic-card {
            flex: 1 1 calc(33.33% - 20px);
            background-color: #f0efff; /* сиреневые оттенки */
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            color: #2B2D42;
        }
        .statistic-card h5 {
            font-family: 'Montserrat Bold', sans-serif;
            margin-bottom: 10px;
            font-size: 1.2em;
        }
        .statistic-card p {
            font-family: 'Montserrat SemiBold', sans-serif;
            font-size: 1.5em;
            margin: 0;
        }
        /* Таблица последних пользователей */
        .latest-users {
            margin-bottom: 40px;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }
        .latest-users-header {
            background-color: #8986FF; /* сиреневый */
            color: #fff;
            padding: 10px 20px;
            font-family: 'Montserrat Bold', sans-serif;
        }
        .latest-users-table {
            width: 100%;
            border-collapse: collapse;
        }
        .latest-users-table th,
        .latest-users-table td {
            padding: 10px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
            font-family: 'Montserrat Medium', sans-serif;
        }
        .latest-users-table th {
            background-color: #f1f1f1;
            font-family: 'Montserrat SemiBold', sans-serif;
            color: #2B2D42;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_admin')
    <div class="admin-container">

        @if(isset($stats))
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
        @endif

        @if(isset($recentUsers))
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
         @endif

    </div>


@endsection
