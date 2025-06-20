@extends('layouts.base')

@section('styles')
    <style>
        .admin-content-wrapper {
            max-width: 1200px;
            margin: 60px auto;
            padding: 0 20px;
            font-family: 'Montserrat-Medium', sans-serif;
        }
        h2.section-title {
            font-family: 'Montserrat Bold', sans-serif;
            font-size: 32px;
            margin-bottom: 30px;
            color: #272727;
        }
        .filter-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 30px;
            align-items: center;
        }
        .filter-bar input[name="search"] {
            flex: 1 1 auto;
            min-width: 150px;
            padding: 8px 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .filter-bar select,
        .filter-bar button,
        .filter-bar a.reset-btn {
            flex: 0 0 140px;
            height: 40px;
            padding: 0 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            cursor: pointer;
            transition: transform .2s, box-shadow .2s;
        }
        .filter-bar button[type="submit"] {
            background: #8986FF;
            color: #fff;
            border: none;
            font-family: 'Montserrat SemiBold', sans-serif;
        }
        .filter-bar a.reset-btn {
            background: #ccc;
            color: #333;
        }
        .filter-bar select:hover,
        .filter-bar button:hover,
        .filter-bar a.reset-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        /* Сортировщик цены */
        .sort-btn {
            position: relative;
        }
        .sort-btn::after {
            content: "{{ $sort==='price' && $dir==='asc' ? '▲' : ($sort==='price' && $dir==='desc' ? '▼' : '▲') }}";
            margin-left: 6px;
        }

        /* Сетка карточек */
        .grid-cards {
            display: grid;
            gap: 20px;
            grid-template-columns: repeat(4, 1fr);
        }
        /* адаптив */
        @media (max-width: 1200px) {
            .grid-cards {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        @media (max-width: 992px) {
            .grid-cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 576px) {
            .grid-cards {
                grid-template-columns: 1fr;
            }
        }

        .price-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform .2s;
        }
        .price-card:hover {
            transform: translateY(-4px);
        }
        .price-header {
            padding: 16px;
            background: #fef6e0;
        }
        .price-name {
            font-family: 'Montserrat SemiBold', sans-serif;
            font-size: 20px;
            margin: 0;
            color: #272727;
        }
        .price-body {
            padding: 16px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .price-info {
            font-size: 16px;
            color: #333;
        }
        .price-footer {
            padding: 16px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
        }
        .price-footer .btn-select {
            display: inline-block;
            padding: 8px 16px;
            background: #8986FF;
            color: #fff;
            border-radius: 4px;
            text-decoration: none;
            font-family: 'Montserrat SemiBold', sans-serif;
            transition: transform .2s;
        }
        .price-footer .btn-select:hover {
            transform: translateY(-2px);
        }

        @media (max-width: 1200px) {
            .filter-bar {
                gap: 8px;
            }
            .filter-bar input[name="search"] {
                flex: 1 1 100%;
            }
            .filter-bar select,
            .filter-bar button,
            .filter-bar a.reset-btn {
                flex: 0 0 48%;
            }
        }

        /* На планшетах портрет (≤992px) — всё в одну колонку */
        @media (max-width: 992px) {
            .filter-bar {
                flex-direction: column;
                gap: 10px;
            }
            .filter-bar input[name="search"],
            .filter-bar select,
            .filter-bar button[type="submit"],
            .filter-bar .sort-btn,
            .filter-bar a.reset-btn {
                flex: 1 1 100%;
                width: 100%;
            }
        }

        /* === Сетка карточек === */
        /* По умолчанию 4 колонки */
        /* ≤1200px — 3 колонки */
        @media (max-width: 1200px) {
            .grid-cards {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        /* ≤992px — 2 колонки */
        @media (max-width: 992px) {
            .grid-cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        /* ≤576px — 1 колонка */
        @media (max-width: 576px) {
            .grid-cards {
                grid-template-columns: 1fr;
            }
        }

        /* === Карточки === */
        /* Немного уменьшим padding и шрифты на узких экранах */
        @media (max-width: 992px) {
            .price-card {
                padding: 12px;
            }
            .price-header {
                padding: 12px;
            }
            .price-name {
                font-size: 18px;
            }
            .price-info {
                font-size: 14px;
            }
            .price-footer {
                padding: 12px;
            }
        }
        @media (max-width: 576px) {
            .price-name {
                font-size: 16px;
            }
            .price-info {
                font-size: 13px;
            }
            .btn-select {
                padding: 6px 12px;
                font-size: 14px;
            }
            .admin-content-wrapper{
                margin-top: 90px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="admin-content-wrapper">

        <form class="filter-bar" method="GET" action="{{ route('prices') }}">
            <input type="text" name="search" placeholder="Поиск по названию" value="{{ $search }}">
            <select name="format">
                <option value="">Все форматы</option>
                <option value="individual" {{ $format==='individual'?'selected':'' }}>Индивидуальный</option>
                <option value="group"      {{ $format==='group'?'selected':'' }}>Групповой</option>
            </select>
            <button type="button"
                    class="sort-btn"
                    onclick="togglePriceSort()"
                    title="Сортировать по цене">
                Цена
            </button>
            <button type="submit">Применить</button>
            <a href="{{ route('prices') }}" class="reset-btn">Сбросить</a>
        </form>

        <div class="grid-cards">
            @forelse($prices as $price)
                <div class="price-card">
                    <div class="price-header">
                        <h3 class="price-name" style="">{{ $price->name }}</h3>
                    </div>
                    <div class="price-body">
                        <div class="price-info"><strong>Формат:</strong>
                            {{ $price->format === 'individual' ? 'Индивидуальный' : 'Групповой' }}
                        </div>
                        <div class="price-info"><strong>Длительность урока:</strong>
                            {{ $price->lesson_duration }} мин.
                        </div>
                        <div class="price-info"><strong>Стоимость за урок:</strong>
                            {{ $price->unit_price }} BYN
                        </div>
                    </div>
                </div>
            @empty
                <p>Тарифы не найдены.</p>
            @endforelse
        </div>

        <div style="margin-top: 20px;">
            {{ $prices->links() }}
        </div>
    </div>

    <script>
        function togglePriceSort() {
            const url  = new URL(window.location.href);
            const sort = 'price';
            let dir     = url.searchParams.get('dir') || 'asc';

            // меняем направление
            dir = dir === 'asc' ? 'desc' : 'asc';
            url.searchParams.set('sort', sort);
            url.searchParams.set('dir', dir);
            window.location.href = url.toString();
        }
    </script>
@endsection
