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
        }
        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .form-inline {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 30px;
        }
        .form-inline input[type="month"] {
            padding: 8px 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-family: 'Montserrat Medium', sans-serif;
        }
        .form-inline button {
            padding: 10px 16px;
            background-color: #e6e2f8;
            color: black;
            border: none;
            border-radius: 7px;
            font-size: 16px;
            cursor: pointer;
            font-family: 'Montserrat SemiBold', sans-serif;
            transition: background-color 0.3s;
        }
        .form-inline button:hover {
            background-color: #c4b6f3;
        }
        table.attendance {
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            font-size: 16px;
        }
        .attendance th,
        .attendance td {
            padding: 12px 20px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        .attendance th {
            background: #fff6d0;
            font-family: 'Montserrat SemiBold', sans-serif;
            color: #333333;
            font-size: 16px;
        }
        .attendance td {
            font-family: 'Montserrat Medium', sans-serif;
            color: #333333;
            font-size: 14px;
        }
        .attendance tr:hover {
            background-color: #f9f9f9;
        }
        .total-row td {
            font-family: 'Montserrat SemiBold', sans-serif;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_student')

    <div class="admin-content-wrapper">
        <div class="header-row">
            <h2>Посещаемость</h2>
            <form method="GET" class="form-inline">
                <label for="month">За месяц:</label>
                <input type="month" id="month" name="month" value="{{ $month }}">
                <button type="submit">Показать</button>
            </form>
        </div>

        @foreach($courses as $block)
            <div class="course-section" style="margin-bottom: 40px;">
                <h3 style="font-family:'Montserrat SemiBold',sans-serif; color:#333; margin-bottom:12px;">
                    Курс «{{ $block['course']->title }}» ({{ $block['course']->language->name }})
                </h3>

                <table class="attendance">
                    <thead>
                    <tr>
                        <th>Дата</th>
                        <th>Время</th>
                        <th>Статус</th>
                        <th>Цена, BYN</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($block['lines'] as $line)
                        <tr>
                            <td>{{ $line['datetime']->format('d.m.Y') }}</td>
                            <td>{{ $line['datetime']->format('H:i') }}</td>
                            <td>
                                {{ $line['status']==='present'
                                    ? 'Присутствовал'
                                    : 'Отсутствовал' }}
                            </td>
                            <td>{{ $line['price'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="padding:20px 0; font-style:italic; color:#666;">
                                За этот месяц уроков нет
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                    <tfoot>
                    <tr class="total-row">
                        <td colspan="3" style="text-align:right;">Итого:</td>
                        <td>{{ $block['total'] }} BYN</td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        @endforeach
    </div>
@endsection
