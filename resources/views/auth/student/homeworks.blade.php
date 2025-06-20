@extends('layouts.app')

@section('styles')
    <style>
        .container {
            max-width: 900px;
            margin: 60px auto;
            padding: 0 20px;
            font-family: 'Montserrat Medium', sans-serif;
        }
        h2 {
            font-family: 'Montserrat Bold', sans-serif;
            font-size: 32px;
            margin-bottom: 30px;
            color: #272727;
        }
        .homework-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .homework-item {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 16px;
            transition: transform .2s;
        }
        .homework-item:hover {
            transform: translateY(-2px);
        }
        .homework-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }
        .homework-course {
            font-family: 'Montserrat SemiBold', sans-serif;
            font-size: 18px;
            color: #333;
        }
        .homework-deadline {
            font-size: 14px;
            color: #555;
        }
        .homework-desc {
            font-size: 15px;
            color: #444;
            line-height: 1.6;
            margin-bottom: 12px;
        }
        .homework-link a {
            color: #8986FF;
            text-decoration: none;
        }
        .homework-link a:hover {
            text-decoration: underline;
        }
        .no-homeworks {
            text-align: center;
            font-size: 16px;
            color: #777;
            margin-top: 40px;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_student')
    <div class="container">
        <h2>Мои домашние задания</h2>

        @if($homeworks->isEmpty())
            <p class="no-homeworks">У вас пока нет домашних заданий.</p>
        @else
            <ul class="homework-list">
                @foreach($homeworks as $hw)
                    <li class="homework-item">
                        <div class="homework-header">
                        <span class="homework-course">
                            {{ $hw->lesson->course->title }}
                        </span>
                            <span class="homework-deadline">
                            До {{ $hw->deadline->format('d.m.Y H:i') }}
                        </span>
                        </div>
                        <p class="homework-desc">
                            {{ $hw->description }}
                        </p>
                        @if($hw->link)
                            <p class="homework-link">
                                <a href="{{ $hw->link }}" target="_blank">Перейти по ссылке</a>
                            </p>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection
