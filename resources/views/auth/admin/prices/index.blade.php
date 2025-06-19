{{-- resources/views/auth/admin/prices/index.blade.php --}}
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
            background-color: #c4b6f3; /* Цвет при наведении, измените при необходимости */
        }
        table.prices {
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            font-size: 16px;
        }
        .prices th,
        .prices td {
            padding: 12px 20px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        .prices th {
            background: #fff6d0;
            font-family: 'Montserrat SemiBold', sans-serif;
            color: #333333;
            font-size: 16px;

        }
        .prices td, .btn {
            font-family: 'Montserrat Medium', sans-serif;
            color: #333333;
            font-size: 14px;
            padding: 12px 20px;
        }
        .table-action-btn {
            display: inline-block;          /* чтобы width работал */
            width: 150px;                   /* фиксированная ширина для обеих */
            padding: 6px 0;                 /* вертикальный padding, горизонтальный — через width */
            text-align: center;             /* центрируем текст */
            font-family: 'Montserrat Medium', sans-serif;
            font-size: 14px;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;          /* для <a> */
        }

        /* Редактировать */
        .table-action-edit {
            background: #f0f0f0;
            color: black;
        }
        .table-action-edit:hover {
            background: #d9d9d9;
        }

        /* Удалить */
        .table-action-delete {
            background: #ffcccc;
            color: black;
        }
        .table-action-delete:hover {
            background: #ffaaaa;
        }
        /* Заголовки‑ссылки */
        .prices th a.sortable {
            color: inherit;
            text-decoration: none;
            cursor: pointer;
            position: relative;
            padding-right: 20px; /* место под стрелку */
            transition: color 0.2s;
        }
        .prices th a.sortable:hover {
            text-decoration: underline;
        }

        /* Стрелка в текущем столбце */
        .prices th a.sortable .arrow {
            position: absolute;
            right: 4px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 16px;
            line-height: 1;
        }


    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_admin')
    <h1>TEST STRING</h1>
    <div class="admin-content-wrapper" >
        <div class="header-row" style="">
            <h2>Тарифы</h2>
            <a href="{{ route('admin.prices.create') }}" class="btn-create">
                + Создать тариф
            </a>
        </div>

        <table class="prices">
            <thead>
            <tr style="background:#e3effc;">
                {{-- Название --}}
                <th class="sortable {{ $sort=='name' ? 'active' : '' }}">
                    <a href="{{ route('admin.prices.index', ['sort'=>'name','dir'=>($sort=='name'&&$dir=='asc')?'desc':'asc']) }}"
                       class="sortable">
                        Название
                        @if($sort=='name')
                            <span class="arrow">{{ $dir=='asc' ? '▲' : '▼' }}</span>
                        @endif
                    </a>
                </th>

                {{-- Длительность --}}
                <th class="sortable {{ $sort=='lesson_duration' ? 'active' : '' }}">
                    <a href="{{ route('admin.prices.index', ['sort'=>'lesson_duration','dir'=>($sort=='lesson_duration'&&$dir=='asc')?'desc':'asc']) }}"
                       class="sortable">
                        Длительность (мин)
                        @if($sort=='lesson_duration')
                            <span class="arrow">{{ $dir=='asc' ? '▲' : '▼' }}</span>
                        @endif
                    </a>
                </th>

                {{-- Цена --}}
                <th class="sortable {{ $sort=='unit_price' ? 'active' : '' }}">
                    <a href="{{ route('admin.prices.index', ['sort'=>'unit_price','dir'=>($sort=='unit_price'&&$dir=='asc')?'desc':'asc']) }}"
                       class="sortable">
                        Цена за урок, BYN
                        @if($sort=='unit_price')
                            <span class="arrow">{{ $dir=='asc' ? '▲' : '▼' }}</span>
                        @endif
                    </a>
                </th>

                <th colspan="2">Действия</th>
            </tr>
            </thead>
            <tbody>
            @if($prices->isEmpty())
                <tr>
                    <td colspan="5" style="padding: 20px 0; text-align: center; font-style: italic; color: #666;">
                        У вас пока нет тарифов
                    </td>
                </tr>
            @else
            @foreach($prices as $price)
                <tr>
                    <td>{{ $price->name }}</td>
                    <td>{{ $price->lesson_duration }}</td>
                    <td>{{ $price->unit_price }}</td>
                    <td>
                        <a href="{{ route('admin.prices.edit', $price) }}"
                           class="table-action-btn table-action-edit">
                            Редактировать
                        </a>
                    </td>
                    <td>
                        <form action="{{ route('admin.prices.destroy', $price) }}"
                              method="POST"
                              data-delete-form
                              data-tariff-name="{{ $price->name }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="table-action-btn table-action-delete">
                                Удалить
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            @endif
            </tbody>
        </table>
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
                    const name = form.dataset.tariffName;
                    Swal.fire({
                        title: `Удалить тариф «${name}»?`,
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
