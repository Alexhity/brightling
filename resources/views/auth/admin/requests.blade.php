{{-- resources/views/auth/admin/requests/index.blade.php --}}
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
            margin-top: 30px;
            margin-bottom: 30px;
            text-align: center;
        }
        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        }
        .requests th {
            background: #fff6d0;
            font-family: 'Montserrat SemiBold', sans-serif;
            color: #333333;
            font-size: 16px;
        }
        .requests td {
            font-family: 'Montserrat Medium', sans-serif;
            color: #333333;
            font-size: 14px;
        }
        .table-action-delete {
            display: inline-block;
            padding: 6px 12px;
            background: #ffcccc;
            color: black;
            font-family: 'Montserrat Medium', sans-serif;
            font-size: 14px;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            transition: background .2s;
            text-decoration: none;
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
            <h2>Заявки на бесплатный урок</h2>
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
            @php session()->forget('success'); @endphp
        @endif

        @if($requests->isEmpty())
            <p style="text-align:center; font-style:italic; color:#666;">Нет заявок.</p>
        @else
            <table class="requests">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Имя</th>
                    <th>Телефон</th>
                    <th>Email</th>
                    <th>Язык</th>
                    <th>Роль</th>
                    <th>Дата заявки</th>
                    <th>Дата урока</th>
                    <th>Время урока</th>
                    <th>Удалить</th>
                </tr>
                </thead>
                <tbody>
                @foreach($requests as $app)
                    <tr>
                        <td>{{ $app->id }}</td>
                        <td>{{ $app->name }}</td>
                        <td>{{ $app->phone }}</td>
                        <td>{{ $app->email }}</td>
                        <td>{{ $app->language->name ?? '—' }}</td>
                        <td>{{ __('roles.' . $app->requested_role) }}</td>
                        <td>{{ $app->created_at->format('d.m.Y H:i') }}</td>
                        <td>{{ optional($app->lesson)->date?->format('d.m.Y') ?? '—' }}</td>
                        <td>{{ optional($app->lesson)->time?->format('H:i') ?? '—' }}</td>
                        <td>
                            <form action="{{ route('admin.requests.destroy', $app->id) }}"
                                  method="POST"
                                  data-delete-form
                                  data-app-id="{{ $app->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="table-action-delete">Удалить</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {{ $requests->withQueryString()->links() }}
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('form[data-delete-form]').forEach(form => {
                form.addEventListener('submit', e => {
                    e.preventDefault();
                    const id = form.dataset.appId;
                    Swal.fire({
                        title: `Удалить заявку #${id}?`,
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
