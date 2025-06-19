@extends('layouts.app')

@section('title', 'Админ-панель - Языки')

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
            margin-top: 30px;
            margin-bottom: 30px;
        }
        .lang-form {
            margin-bottom: 20px;
            width: 100%;
        }
        .lang-form form {
            display: flex;
            width: 100%;
            gap: 12px;
        }
        .lang-form input[type="text"] {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 7px;
            font-family: 'Montserrat Medium', sans-serif;
            font-size: 16px;
            width: 100%;
            gap: 12px;
        }
        .lang-form input:focus {
            outline: none;
            border-color: #615f5f;
        }
        .btn-create {
            padding: 10px 16px;
            background-color: #e6e2f8;
            color: black;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            border: none;
            font-family: 'Montserrat Medium', sans-serif;
            border-radius: 7px;
        }
        .btn-create:hover {
            background-color: #c4b6f3;
        }
        table.languages {
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            font-size: 16px;
        }
        .languages th,
        .languages td {
            padding: 12px 20px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        .languages th {
            background: #fff6d0;
            font-family: 'Montserrat SemiBold', sans-serif;
            color: #333333;
            font-size: 16px;
        }
        .languages td {
            font-family: 'Montserrat Medium', sans-serif;
            color: #333333;
            font-size: 14px;
        }
        .table-action-btn {
            display: inline-block;
            padding: 6px 12px;
            margin-right: 8px;
            text-align: center;
            font-family: 'Montserrat Medium', sans-serif;
            font-size: 14px;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;
        }
        .table-action-edit {
            background: #f0f0f0;
            color: black;
        }
        .table-action-edit:hover {
            background: #d9d9d9;
        }
        .table-action-delete {
            background: #ffcccc;
            color: black;
        }
        .table-action-delete:hover {
            background: #ffaaaa;
        }
        .table-action-save {
            background: #d4ffd8;
            color: #333333;
        }
        .table-action-save:hover {
            background: #b1e7b5;
        }
        .sortable {
            color: inherit;
            text-decoration: none;
            cursor: pointer;
            position: relative;
            padding-right: 20px;
            transition: color 0.2s;
        }
        .sortable:hover {
            text-decoration: underline;
        }
        .sortable .arrow {
            position: absolute;
            right: 4px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 16px;
            line-height: 1;
        }

        .input-error {
            border: 2px solid #e74c3c;
        }
        .text-error {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 4px;
            font-family: 'Montserrat', sans-serif;
        }
        .lang-form {
            margin-bottom: 24px;
        }
        .lang-form input {
            padding: 8px 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-family: 'Montserrat Medium', sans-serif;
            transition: border-color 0.2s;
        }
        .lang-form button.btn-create {
            margin-left: 8px;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_admin')

    <div class="admin-content-wrapper">
        <h2>Управление языками</h2>

        {{-- Форма добавления нового языка --}}
        <div class="lang-form">
            <form action="{{ route('admin.languages.index') }}" method="GET" class="d-none">
                {{-- для сортировки --}}
            </form>
            <form action="{{ route('admin.languages.create') }}" method="POST">
                @csrf
                <input type="text" name="name" placeholder="Введите название нового языка" required>
                <button type="submit" class="btn-create">+ Добавить язык</button>
            </form>
        </div>

        {{-- Список языков с возможностью инлайн‑редактирования --}}
        <div class="lang-list">
            <table class="languages">
                <thead>
                <tr>
                    <th>
                        <a href="{{ route('admin.languages.index', ['sort' => 'name', 'dir' => ($sort == 'name' && $dir == 'asc') ? 'desc' : 'asc']) }}" class="sortable">
                            Название
                            @if($sort == 'name')
                                <span class="arrow">{{ $dir == 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </a>
                    </th>
                    <th colspan="2">Действия</th>
                </tr>
                </thead>
                <tbody>
                @foreach($languages as $lang)
                    <tr>
                        <td>{{ $lang->name }}</td>
                        <td colspan="2">
                            <button type="button" class="table-action-btn table-action-edit edit-btn" data-id="{{ $lang->id }}">
                                Редактировать
                            </button>
                            <form action="{{ route('admin.languages.destroy', $lang->id) }}" method="POST" class="d-inline" data-delete-form data-language-name="{{ $lang->name }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="table-action-btn table-action-delete">Удалить</button>
                            </form>
                        </td>
                    </tr>

                    <tr class="edit-row" data-id="{{ $lang->id }}" style="display: none; background-color: #f9f9f9;">
                        <td colspan="3">
                            <form action="{{ route('admin.languages.update', $lang->id) }}" method="POST" class="inline-edit-form" style="display: flex; align-items: center; gap: 8px;">
                                @csrf
                                @method('PUT')
                                <label for="name-{{ $lang->id }}">Новое имя:</label>
                                <input type="text" id="name-{{ $lang->id }}" name="name" value="{{ old('name', $lang->name) }}" placeholder="Новое название" required style="flex: 1; padding: 6px 8px; border: 1px solid #ccc; border-radius: 4px; font-size:14px;">

                                <button type="submit" class="table-action-btn table-action-save">Сохранить</button>
                                <button type="button" class="table-action-btn table-action-delete cancel-btn" data-id="{{ $lang->id }}">Отмена</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Toast об успехе
            @if(session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: @json(session('success')),
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
            @endif

            // Подтверждение удаления через SweetAlert2
            document.querySelectorAll('form[data-delete-form]').forEach(form => {
                form.addEventListener('submit', e => {
                    e.preventDefault();
                    const name = form.dataset.languageName;
                    Swal.fire({
                        title: `Удалить язык «${name}»?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Да, удалить',
                        cancelButtonText: 'Отмена',
                        reverseButtons: true,
                    }).then(result => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // Инлайн‑редактирование
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const id = button.dataset.id;
                    const row = document.querySelector(`.edit-row[data-id="${id}"]`);
                    row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
                });
            });

            document.querySelectorAll('.cancel-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const id = button.dataset.id;
                    const row = document.querySelector(`.edit-row[data-id="${id}"]`);
                    if (row) row.style.display = 'none';
                });
            });
        });
    </script>
@endsection
