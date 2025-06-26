{{-- resources/views/auth/admin/requests/index.blade.php --}}
@php use Carbon\Carbon; @endphp
@php
    $roleLabels = [
        'student' => 'Студент',
        'teacher' => 'Преподаватель',
        'admin'   => 'Администратор',
    ];
@endphp
@extends('layouts.app')

@section('styles')
    <style>
        .admin-content-wrapper {
            margin-left: 200px;
            width: calc(100% - 200px);
            font-family: 'Montserrat Medium', sans-serif;
        }
        h2 {
            font-family: 'Montserrat Bold', sans-serif;
            color: #333333;
            font-size: 32px;
            margin: 30px 0;
            text-align: center;
        }
        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .btn-create {
            display: inline-block;
            padding: 10px 16px;
            background-color: #e6e2f8;
            color: black;
            text-decoration: none;
            border-radius: 7px;
            font-family: 'Montserrat Medium', sans-serif;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-create:hover {
            background-color: #c4b6f3;
        }
        .table-responsive {
            overflow-x: auto;
            margin-bottom: 1rem;
        }
        table.requests {
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            font-size: 16px;
        }
        .requests th,
        .requests td {
            padding: 12px 20px;
            border-bottom: 1px solid #ddd;
            text-align: center;
            font-family: 'Montserrat Medium', sans-serif;
            color: #333333;
        }
        .requests th {
            background: #fff6d0;
            font-family: 'Montserrat SemiBold', sans-serif;
            font-size: 16px;
        }
        .expand-btn {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: #333333;
        }
        .custom-select {
            font-size: 14px;
            padding: 4px 8px;
            border-radius: 4px;
            font-family: 'Montserrat Medium', sans-serif;
        }
        .btn-delete {
            background: none;
            border: none;
            color: #c0392b;
            cursor: pointer;
            font-size: 18px;
            transition: color .2s;
        }
        .btn-delete:hover {
            color: #a93226;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_admin')

    <div class="admin-content-wrapper">
        <div class="header-row">
            <h2>Заявки на бесплатный урок</h2>
            <button id="process-all-btn" class="btn-create">Обработать все</button>
            <form id="process-all-form" action="{{ route('admin.requests.createProfilesAll') }}" method="POST" style="display:none">
                @csrf
            </form>
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
                        customClass: { popup: 'swal2-toast' }
                    });
                });
            </script>
            @php session()->forget('success'); @endphp
        @endif

        <div class="table-responsive">
            <table class="requests">
                <thead>
                <tr>
                    <th></th>
                    <th>ID</th>
                    <th>Имя</th>
                    <th>Email</th>
                    <th>Язык</th>
                    <th>Роль</th>
                    <th>Дата заявки</th>
                    <th>Удалить</th>
                </tr>
                </thead>
                <tbody>
                @forelse($requests as $app)
                    <tr
                        data-phone="{{ $app->phone }}"
                        data-lesson-date="{{ optional($app->lesson)->date
                            ? Carbon::parse($app->lesson->date)->format('d.m.Y')
                            : '—' }}"
                        data-lesson-time="{{ optional($app->lesson)->time
                            ? Carbon::parse($app->lesson->time)->format('H:i')
                            : '—' }}"
                    >
                        {{-- Плюсик --}}
                        <td><button class="expand-btn">＋</button></td>

                        {{-- Основные колонки --}}
                        <td>{{ $app->id }}</td>
                        <td>{{ $app->name }}</td>
                        <td>{{ $app->email }}</td>
                        <td>{{ $app->language->name ?? '—' }}</td>
                        <td>
                            <form action="{{ route('admin.requests.updateRole', $app->id) }}"
                                  method="POST" style="display:inline">
                                @csrf @method('PATCH')
                                <select name="requested_role"
                                        class="custom-select"
                                        onchange="this.form.submit()">
                                    @foreach($roleLabels as $key => $label)
                                        <option value="{{ $key }}"
                                            {{ $app->requested_role === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td>{{ $app->created_at->format('d.m.Y H:i') }}</td>

                        {{-- Удаление --}}
                        <td>
                            <form action="{{ route('admin.requests.destroy', $app->id) }}"
                                  method="POST"
                                  data-app-id="{{ $app->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-delete">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="font-style:italic; color:#666;">
                            Нет заявок.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Подтверждение «Обработать все»
            document.getElementById('process-all-btn').addEventListener('click', () => {
                Swal.fire({
                    title: 'Обработать все заявки?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Да, обработать',
                    cancelButtonText: 'Отмена'
                }).then(res => {
                    if (res.isConfirmed) {
                        document.getElementById('process-all-form').submit();
                    }
                });
            });

            // Раскрытие деталей
            document.querySelectorAll('.expand-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const tr = btn.closest('tr');
                    Swal.fire({
                        title: `Заявка #${tr.children[1].textContent}`,
                        html: `
                        <p><strong>Телефон:</strong> ${tr.dataset.phone}</p>
                        <p><strong>Дата урока:</strong> ${tr.dataset.lessonDate}</p>
                        <p><strong>Время урока:</strong> ${tr.dataset.lessonTime}</p>
                    `,
                        width: 500,
                        confirmButtonText: 'Закрыть'
                    });
                });
            });

            // Подтверждение удаления
            document.querySelectorAll('button.btn-delete').forEach(btn => {
                btn.addEventListener('click', () => {
                    const form = btn.closest('form');
                    const id = form.dataset.appId;
                    Swal.fire({
                        title: `Удалить заявку #${id}?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Да, удалить',
                        cancelButtonText: 'Отмена',
                        reverseButtons: true
                    }).then(res => {
                        if (res.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection

