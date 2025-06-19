@extends('layouts.app')

@section('styles')
    <style>
        .admin-content-wrapper{
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
        .header-row{
            display:flex;
            justify-content:space-between;
            align-items:center;
        }
        .btn-create {
            display: inline-block;
            padding: 10px 16px;
            background-color: #e6e2f8;
            color: black;
            text-decoration: none;
            border-radius: 7px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-create:hover {
            background-color: #c4b6f3;
        }
        table.users {
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            font-size: 16px;
        }
        .users th,
        .users td {
            padding: 12px 20px;
            border-bottom: 1px solid #ddd;
            text-align: center;
            font-family: 'Montserrat Medium', sans-serif;
            color: #333333;
        }
        .users th {
            background: #fff6d0;
            font-family: 'Montserrat SemiBold', sans-serif;
            font-size: 16px;
        }
        /* Header background row */
        .users thead tr {
            background: #e3effc;
        }
        .table-action-btn {
            display: inline-block;
            width: 150px;
            padding: 6px 0;
            text-align: center;
            font-family: 'Montserrat Medium', sans-serif;
            font-size: 14px;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;
            color: #333333;
        }
        .users td {
            font-family: 'Montserrat Medium', sans-serif;
            color: #333333;
            font-size: 14px;
            padding: 12px 20px;
        }
        .table-action-edit {
            background: #f0f0f0;
        }
        .table-action-edit:hover {
            background: #d9d9d9;
        }
        .table-action-delete {
            background: #ffcccc;
        }
        .table-action-delete:hover {
            background: #ffaaaa;
        }
        .expand-btn {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: #333333;
        }
        .extra-info-row td {
            text-align: left;
            background: #f9f9f9;
            font-family: 'Montserrat Medium', sans-serif;
            padding: 16px 20px;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_admin')
    <div class="admin-content-wrapper">
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
        @endif

        <div class="header-row">
            <h2>Пользователи</h2>
            <a href="{{ route('admin.users.create') }}" class="btn-create">+ Создать пользователя</a>
        </div>


        <table class="users">
            <thead>
            <tr>
                <th></th>
                <th>Имя</th>
                <th>Фамилия</th>
                <th>Email</th>
                <th>Роль</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td><button class="expand-btn" onclick="toggleDetails(this)">+</button></td>
                    <td>{{ $user->first_name }}</td>
                    <td>{{ $user->last_name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td>
                        <a href="{{ route('admin.users.edit', $user) }}" class="table-action-btn table-action-edit">Редактировать</a>
                        <form action="{{ route('admin.users.destroy', $user) }}"
                              method="POST"
                              data-delete-form
                              data-user-name="{{ $user->first_name }} {{ $user->last_name }}"
                              style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="table-action-btn table-action-delete">
                                Удалить
                            </button>
                        </form>

                    </td>
                </tr>
                <tr class="extra-info-row" style="display:none;">
                    <td colspan="6">
                        <strong>Телефон:</strong> {{ $user->phone ?? '—' }}<br>
                        <strong>Дата рождения:</strong> {{ $user->date_birthday ?? '—' }}<br>
                        <strong>Уровень:</strong> {{ $user->level ?? '—' }}<br>
                        <strong>Описание:</strong> {{ $user->description ?? '—' }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <script>
        function toggleDetails(button) {
            const row = button.closest('tr');
            const details = row.nextElementSibling;
            const isVisible = details.style.display === 'table-row';
            details.style.display = isVisible ? 'none' : 'table-row';
            button.textContent = isVisible ? '+' : '–';
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Подтверждение удаления через SweetAlert2
            document.querySelectorAll('form[data-delete-form]').forEach(form => {
                form.addEventListener('submit', e => {
                    e.preventDefault();
                    const name = form.dataset.userName;
                    Swal.fire({
                        title: `Удалить пользователя «${name}»?`,
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
        });
    </script>
@endsection
