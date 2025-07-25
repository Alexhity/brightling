{{-- Список сообщений --}}
@extends('layouts.app')

@section('title','Сообщения')

@section('styles')
    <style>
        .admin-content-wrapper {
            margin-left: 200px;
            width: calc(100% - 200px);
            padding: 20px;
            font-family: 'Montserrat Medium',sans-serif;
        }
        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .header-row h2 {
            font-family: 'Montserrat Bold',sans-serif;
            font-size: 32px;
            color: #333;
            margin: 0;
        }
        .btn-create {
            background:#e6e2f8;
            padding:8px 16px;
            border-radius:7px;
            text-decoration:none;
            color:#000;
            font-family:'Montserrat Medium',sans-serif;
            transition: background .3s;
        }
        .btn-create:hover { background:#c4b6f3; }

        .filter-row {
            text-align: left;

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
            background: #f0f0f0;   /* серый фон для выбранного */
        }


        table.messages {
            width:100%;
            border-collapse:collapse;
            box-shadow:0 2px 8px rgba(0,0,0,0.1);
            font-size:16px;
        }
        .messages th,
        .messages td {
            padding:12px 20px;
            border-bottom:1px solid #ddd;
            text-align:center;
        }
        .messages th {
            background:#fff6d0;
            font-family:'Montserrat SemiBold',sans-serif;
            color:#333;
        }
        .messages td {
            font-size:14px; /* вот тут */
            font-family:'Montserrat Medium',sans-serif;
        }
        .table-action-btn {
            background:#e6f7ff;
            padding:6px 12px;
            border-radius:7px;
            text-decoration:none;
            color:#2b2d42;
            font-family:'Montserrat Medium',sans-serif;
            transition:background .2s;
        }
        .table-action-btn:hover { background:#b3e5ff; }
        .badge-pending {
            background:#fff3cd;color:#856404;padding:4px 8px;border-radius:4px;
        }
        .badge-answered {
            background:#d4edda;color:#155724;padding:4px 8px;border-radius:4px;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_admin')
    <div class="admin-content-wrapper">


        <div class="header-row">
            <h2>Сообщения</h2>
            <a href="{{ route('admin.messages.create') }}" class="btn-create">+ Создать сообщение</a>
        </div>
            <div class="filter-row">
                <a href="{{ route('admin.messages.index') }}"
                   class="{{ is_null($statusFilter) ? 'active' : '' }}">
                    Все
                </a>
                |
                <a href="{{ route('admin.messages.index', ['status'=>'pending']) }}"
                   class="{{ $statusFilter==='pending' ? 'active' : '' }}">
                    Ожидают ответа
                </a>
                |
                <a href="{{ route('admin.messages.index', ['status'=>'answered']) }}"
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
                    <td>{{ \Illuminate\Support\Str::limit($msg->question_text,30) }}</td>
                    <td>
                        @if($msg->status==='pending')
                            <span class="badge-pending">Ожидает</span>
                        @else
                            <span class="badge-answered">Отвечено</span>
                        @endif
                    </td>
                    <td>{{ $msg->question_sent_at->format('d.m.Y H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.messages.show',$msg) }}"
                           class="table-action-btn">
                            Просмотр
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">Нет сообщений</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            @if(session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: @json(session('success')),
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
            });
            @endif
        });
    </script>
@endsection
