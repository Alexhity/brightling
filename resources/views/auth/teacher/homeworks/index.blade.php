@extends('layouts.app')

@section('styles')
    <style>
        .admin-content-wrapper {
            margin-left: 200px;
            width: calc(100% - 200px);
            font-family: 'Montserrat Medium', sans-serif;
            padding: 20px;
        }
        h2 { font-family: 'Montserrat Bold', sans-serif; font-size:32px; margin-bottom:20px; }
        .header-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
        .btn-create { padding:10px 16px; background:#e6e2f8; border-radius:7px; text-decoration:none; color:#000; }
        .btn-create:hover { background:#c4b6f3; }
        .filter-bar { margin-bottom:20px; display:flex; gap:12px; }
        .filter-bar select,
        .filter-bar input,
        .filter-bar button,
        .filter-bar a.reset-btn {
            padding:8px 12px; font-size:16px; border:1px solid #ccc; border-radius:4px; background:#fff;
        }
        .filter-bar button { background:#8986FF; color:#fff; border:none; cursor:pointer; }
        .filter-bar a.reset-btn { background:#ccc; color:#333; text-decoration:none; }
        .homeworks-table { width:100%; border-collapse:collapse; box-shadow:0 2px 8px rgba(0,0,0,0.1); }
        .homeworks-table th,
        .homeworks-table td { padding:12px 10px; border-bottom:1px solid #ddd; text-align:left; }
        .homeworks-table th { background:#fef6e0; font-family:'Montserrat SemiBold',sans-serif; }
        .table-action-edit,
        .table-action-delete {
            display:inline-block; padding:6px 0; width:120px; text-align:center; border:none;
            border-radius:7px; text-decoration:none; font-size:14px; transition:background .2s;
        }
        .table-action-edit { background:#f0f0f0; color:#000; }
        .table-action-edit:hover { background:#d9d9d9; }
        .table-action-delete { background:#ffcccc; color:#000; }
        .table-action-delete:hover { background:#ffaaaa; }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_teacher')
    <div class="admin-content-wrapper">
        <div class="header-row">
            <h2>Домашние задания</h2>
            <a href="{{ route('teacher.homeworks.create') }}" class="btn-create">+ Создать задание</a>
        </div>
        @php
            $statusLabels = [
                'pending'   => 'Ожидает',
                'submitted' => 'Отправлено',
                'rejected'  => 'Отклонено',
            ];
        @endphp



        <table class="homeworks-table">
            <thead>
            <tr>
                <th>Курс</th>
                <th>Описание</th>
                <th>Дедлайн</th>
                <th>Ссылка</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            @forelse($homeworks as $hw)
                <tr>
                    <td>{{ $hw->lesson->course->title }}</td>
                    <td>{{ Str::limit($hw->description, 50) }}</td>
                    <td>{{ $hw->deadline->format('d.m.Y H:i') }}</td>
                    <td>
                        @if($hw->link)
                            <a href="{{ $hw->link }}" target="_blank">Перейти</a>
                        @else
                            —
                        @endif
                    </td>

                    <td>
                        <a href="{{ route('teacher.homeworks.edit', $hw) }}"
                           class="table-action-edit">Редактировать</a>
                        <form action="{{ route('teacher.homeworks.destroy', $hw) }}"
                              method="POST" style="display:inline"
                              class="form-delete">
                            @csrf @method('DELETE')
                            <button class="table-action-delete">Удалить</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align:center;">Домашние задания не найдены.</td></tr>
            @endforelse
            </tbody>
        </table>

        <div style="margin-top:20px;">{{ $homeworks->links() }}</div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: @json(session('success')),
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
            });
            @endif
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.form-delete').forEach(form => {
                form.addEventListener('submit', e => {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Удалить это задание?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Да, удалить',
                        cancelButtonText: 'Отмена',
                        reverseButtons: true
                    }).then(result => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection
