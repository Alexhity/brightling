@extends('layouts.app')

@section('title','Сообщения')

@section('styles')
    <style>
        .teacher-content-wrapper {
            margin-left: 200px;
            width: calc(100% - 200px);
            padding: 20px;
            font-family: 'Montserrat Medium', sans-serif;
        }
        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .header-row h2 {
            font-family: 'Montserrat Bold', sans-serif;
            font-size: 32px;
            margin: 0;
            color: #2B2D42;
        }
        .btn-create {
            background: #e6e2f8;       /* светло‑фиолетовый */
            padding: 8px 16px;
            border-radius: 7px;
            text-decoration: none;
            color: #2B2D42;
            font-family: 'Montserrat Medium', sans-serif;
            transition: background .3s;
        }
        .btn-create:hover {
            background: #c4b6f3;
        }

        .filter-row {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }
        .filter-row a {
            text-decoration: none;
            font-family: 'Montserrat Medium', sans-serif;
            color: #2B2D42;
            padding: 6px 12px;
            border-radius: 5px;
            transition: background .2s;
        }
        .filter-row a:hover {
            background: #f0f0f0;
        }
        .filter-row a.active {
            background: #dddddd;
            font-family: 'Montserrat SemiBold', sans-serif;
        }

        table.messages {
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .messages th,
        .messages td {
            padding: 12px 20px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        .messages th {
            background: #fef6e0;
            font-family: 'Montserrat SemiBold', sans-serif;
            color: #2B2D42;
        }
        .messages td {
            font-size: 14px;
            font-family: 'Montserrat Medium', sans-serif;
        }
        .table-action-btn {
            background: #e6f7ff;
            padding: 6px 12px;
            border-radius: 7px;
            text-decoration: none;
            color: #2B2D42;
            font-family: 'Montserrat Medium', sans-serif;
            transition: background .2s;
        }
        .table-action-btn:hover {
            background: #b3e5ff;
        }
        .badge-pending {
            background: #fff3cd;
            color: #856404;
            padding: 4px 8px;
            border-radius: 4px;
        }
        .badge-answered {
            background: #d4edda;
            color: #155724;
            padding: 4px 8px;
            border-radius: 4px;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_teacher')

    <div class="teacher-content-wrapper">


        <div class="header-row">
            <h2>Сообщения</h2>
            <a href="{{ route('teacher.messages.create') }}" class="btn-create">+ Новое сообщение</a>
        </div>

        <div class="filter-row">
            <a href="{{ route('teacher.messages.index') }}"
               class="{{ is_null($statusFilter) ? 'active' : '' }}">
                Все
            </a>
            |
            <a href="{{ route('teacher.messages.index', ['status'=>'pending']) }}"
               class="{{ $statusFilter==='pending' ? 'active' : '' }}">
                Ожидают ответа
            </a>
            |
            <a href="{{ route('teacher.messages.index', ['status'=>'answered']) }}"
               class="{{ $statusFilter==='answered' ? 'active' : '' }}">
                Уже отвечено
            </a>
        </div>

        <table class="messages">
            <thead>
            <tr>
                <th>Отправитель</th>
                <th>Получатель</th>
                <th>Сообщение</th>
                <th>Статус</th>
                <th>Дата</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            @forelse($messages as $msg)
                <tr>
                    <td>{{ $msg->sender->first_name }} {{ $msg->sender->last_name }}</td>
                    <td>{{ $msg->recipient->first_name }} {{ $msg->recipient->last_name }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($msg->question_text, 30) }}</td>
                    <td>
                        @if($msg->status==='pending')
                            <span class="badge-pending">Ожидает</span>
                        @else
                            <span class="badge-answered">Отвечено</span>
                        @endif
                    </td>
                    <td>{{ $msg->question_sent_at->format('d.m.Y H:i') }}</td>
                    <td>
                        <a href="{{ route('teacher.messages.show',$msg) }}"
                           class="table-action-btn">Просмотр</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">Нет сообщений</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
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
        @php
            // Удаляем, чтобы при «Назад» больше не появлялось
            session()->forget('success');
        @endphp
    @endif
@endsection
