{{-- resources/views/auth/admin/certificates/index.blade.php --}}
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
        .search-form {
            margin-bottom: 20px;
        }
        .search-form input[type="text"] {
            padding: 8px;
            width: 200px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        table.certificates {
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            font-size: 16px;
        }
        .certificates th,
        .certificates td {
            padding: 12px 20px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        .certificates th {
            background: #fff6d0;
            font-family: 'Montserrat SemiBold', sans-serif;
            color: #333333;
            font-size: 16px;
        }
        .certificates td, .btn {
            font-family: 'Montserrat Medium', sans-serif;
            color: #333333;
            font-size: 14px;
            padding: 12px 20px;
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
        }
        .table-action-view {
            background: #eaf4ff;
            color: #333;
        }
        .table-action-view:hover {
            background: #d4eaff;
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


    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_admin')

    <div class="admin-content-wrapper">
        <div class="header-row">
            <h2>Сертификаты</h2>
            <a href="{{ route('admin.certificates.create') }}" class="btn-create">+ Добавить сертификат</a>
        </div>
        <table class="certificates">
            <thead>
            <tr style="background:#e3effc;">
                <th>Пользователь</th>
                <th>Заголовок</th>
                <th>Дата создания</th>
                <th>Файл</th>
                <th>Редактировать</th>
                <th>Удалить</th>
            </tr>
            </thead>
            <tbody>
            @forelse($certificates as $cert)
                <tr>
                    <td>{{ $cert->user->first_name }} {{ $cert->user->last_name }}</td>
                    <td>{{ $cert->title }}</td>
                    <td>{{ $cert->created_at->format('d.m.Y') }}</td>
                    <td>
                        <a href="{{ asset('images/certificates/' . $cert->file_path) }}" target="_blank" class="table-action-btn table-action-view">
                            Просмотреть
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('admin.certificates.edit', $cert->id) }}" class="table-action-btn table-action-edit">
                            Редактировать
                        </a>
                    </td>
                    <td>
                        <form action="{{ route('admin.certificates.destroy', $cert->id) }}" method="POST" data-delete-form data-cert-title="{{ $cert->title }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="table-action-btn table-action-delete">Удалить</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="padding: 20px 0; text-align: center; font-style: italic; color: #666;">
                        У вас пока нет сертификатов
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $certificates->withQueryString()->links() }}
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
                timer: 3000,
                timerProgressBar: true,
            });
            @endif

            document.querySelectorAll('form[data-delete-form]').forEach(form => {
                form.addEventListener('submit', e => {
                    e.preventDefault();
                    const title = form.dataset.certTitle;
                    Swal.fire({
                        title: `Удалить сертификат «${title}»?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Да, удалить',
                        cancelButtonText: 'Отмена',
                        reverseButtons: true,
                    }).then(result => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });
        });
    </script>
@endsection
