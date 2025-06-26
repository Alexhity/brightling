@extends('layouts.base')

@section('styles')
    <style>
        .admin-content-wrapper {
            max-width: 1300px; /* Ограничение ширины */
            margin: 90px auto;
            width: 100%;
            gap: 20px;
            padding: 0 20px;
            font-family: 'Montserrat-Medium', sans-serif;
        }
        h2 {
            font-family: 'Montserrat Bold', sans-serif;
            font-size: 32px;
            margin-top: 20px;
            margin-bottom: 30px;
        }
        .filter-bar {
            margin-bottom: 30px;
            width: 100%;
            font-size: 16px;
        }
        .filter-bar input,
        .filter-bar select {
            padding: 8px 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            font-family: 'Montserrat-Medium', sans-serif;
        }
        .filter-bar button {
            padding: 8px 3px;
            background: #8986FF;
            color: #fff;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: transform 0.2s;
            font-family: 'Montserrat SemiBold', sans-serif;
        }
        .filter-bar button:hover {
            transform: scale(1.05);
        }

        .grid-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }
        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform .2s;
            font-family: 'Montserrat-Medium', sans-serif;
        }
        .card:hover {
            transform: translateY(-4px);
        }
        .card-header {
            padding: 1rem;
            background: #fef6e0;
        }
        .card-header h3 {
            margin: 0;
            font-family: 'Montserrat SemiBold', sans-serif;
            font-size: 18px;
            color: #2B2D42;
        }
        .card-header small {
            color: #555;
            font-size: 14px;
        }
        .card-body {
            flex: 1;
            padding: 1rem;
        }
        .schedule {
            list-style: none;
            padding: 0;
            margin: 0 0 1rem;
            font-size: 14px;
            color: #2B2D42;
        }
        .schedule li {
            margin-bottom: 0.4rem;
        }
        .price {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 1rem;
            color: #2B2D42;
        }
        .btn-enroll {
            background-color: #fef6e0;
            color: #333;
            text-align: center;
            padding: 0.75rem;
            text-decoration: none;
            font-family: 'Montserrat SemiBold', sans-serif;
            font-size: 16px;
            border-top: 1px solid #e0e0e0;
            transition: transform .2s;
        }
        .btn-enroll:hover {
            transform: scale(1.05);
        }
        .filter-bar {
            display: flex;
            gap: 12px;
            margin-bottom: 30px;
            flex-wrap: wrap;
            width: 100%;
            font-size: 16px;
            align-items: center;
        }
        /* Поисковая строка растягивается, остальные элементы фиксированного размера */
        .filter-bar input[name="search"] {
            flex: 1 1 200px;
            min-width: 200px;
        }
        .filter-bar select,
        .filter-bar .btn,
        .filter-bar .sort-btn {
            flex: 0 0 140px;
            height: 40px;
            padding: 0 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            font-family: 'Montserrat-Medium', sans-serif;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            text-decoration: none;
            color: #2B2D42;
            transition: background .2s, transform .2s;
        }
        .filter-bar select:hover,
        .filter-bar .btn:hover,
        .filter-bar .sort-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.12);
        }

        /* Кнопка Применить */
        .filter-bar button[type="submit"].btn {
            background: #8986FF;
            color: #fff;
            border: none;
            font-family: 'Montserrat SemiBold', sans-serif;
        }

        /* Кнопка Сбросить */
        .filter-bar a.btn-reset {
            background: #ccc;
            color: #333;
            text-decoration: none;
        }

        /* Сортировка: две кнопки со стрелками */
        .filter-bar .sort-btn {
            background: #fff;
            border: 1px solid #ccc;
            position: relative;
            padding: 0;
        }
        .filter-bar .sort-btn[data-dir="asc"]::after {
            content: "▲";
            font-size: 16px;
        }
        .filter-bar .sort-btn[data-dir="desc"]::after {
            content: "▼";
            font-size: 16px;
        }
        /* Сортировщик: теперь с текстом “Цена” + стрелка */
        .filter-bar .sort-btn {
            flex: 0 0 140px;
            height: 40px;
            padding: 0 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            font-family: 'Montserrat-Medium', sans-serif;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            transition: background .2s, transform .2s;
        }
        .filter-bar .sort-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.12);
        }

        /* Слово “Цена” внутри кнопки уже в HTML,
           а стрелка через ::after: */
        .filter-bar .sort-btn::after {
            content: "";
            display: inline-block;
            margin-left: 6px;
            font-size: 16px;
            line-height: 1;
        }
        .filter-bar .sort-btn[data-dir="asc"]::after {
            content: "▲";
        }
        .filter-bar .sort-btn[data-dir="desc"]::after {
            content: "▼";
        }
        .filter-bar .price-unit {
            font-size: 14px;
            color: #555;
            margin-left: 6px;
        }

        /* 1) Очень маленькие экраны (смартфоны portrait, до 480px) */
        @media (max-width: 480px) {
            .filter-bar {
                flex-direction: column;
                gap: 10px;
            }
            .filter-bar input[name="search"],
            .filter-bar select,
            .filter-bar .sort-btn,
            .filter-bar .btn,
            .filter-bar .btn-reset {
                flex: 1 1 100%;
                min-width: unset;
            }
            .grid-cards {
                grid-template-columns: 1fr;
            }

            .admin-content-wrapper {
                margin: 140px auto;
            }

        }

        /* 2) Малые экраны (смартфоны landscape и маленькие планшеты, 481–768px) */
        @media (min-width: 481px) and (max-width: 768px) {
            .filter-bar {
                flex-wrap: wrap;
            }
            .filter-bar input[name="search"] {
                flex: 1 1 100%;
            }
            .filter-bar select,
            .filter-bar .sort-btn,
            .filter-bar .btn,
            .filter-bar .btn-reset {
                flex: 0 0 48%;
            }
            .grid-cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* 3) Средние экраны (планшеты и маленькие ноуты, 769–1024px) */
        @media (min-width: 769px) and (max-width: 1024px) {
            .filter-bar input[name="search"] {
                flex: 1 1 60%;
            }
            .filter-bar select,
            .filter-bar .sort-btn,
            .filter-bar .btn,
            .filter-bar .btn-reset {
                flex: 0 0 18%;
            }
            .grid-cards {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* 4) Большие экраны (от 1025px и выше) */
        @media (max-width: 1025px) {
            .filter-bar input[name="search"] {
                flex: 1 1 70%;
            }
            .filter-bar select,
            .filter-bar .sort-btn,
            .filter-bar .btn,
            .filter-bar .btn-reset {
                flex: 0 0 140px;
            }
            .grid-cards {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            }
        }

    </style>
@endsection

@section('content')
    <div class="admin-content-wrapper">
        @if(session('success'))
            <script>
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: @json(session('success')),
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            </script>
        @endif

        <form method="GET" action="{{ route('courses') }}">
            <div class="filter-bar">
                <input type="text" name="search" placeholder="Поиск курса..." value="{{ $search }}">
                <select name="language">
                    <option value="">Все языки</option>
                    @foreach($languages as $lang)
                        <option value="{{ $lang->id }}" {{ $language==$lang->id?'selected':'' }}>
                            {{ $lang->name }}
                        </option>
                    @endforeach
                </select>
                <select name="level">
                    <option value="">Все уровни</option>
                    @foreach($levels as $key=>$label)
                        <option value="{{ $key }}" {{ $level==$key?'selected':'' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <select name="format">
                    <option value="">Все форматы</option>
                    @foreach($formats as $key=>$label)
                        <option value="{{ $key }}" {{ $format==$key?'selected':'' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                {{-- Кнопка‑переключатель сортировки по цене --}}
                @php
                    $isPriceSort = request('sort') === 'price_total';
                    $currentDir  = $isPriceSort ? request('dir','asc') : 'asc';
                @endphp

                <button type="button"
                        class="sort-btn"
                        data-dir="{{ $currentDir }}"
                        onclick="togglePriceSort()"
                        title="Сортировать по цене">
                    Цена
                </button>

                <button type="submit" class="btn">Применить</button>
                <a href="{{ route('courses') }}" class="btn btn-reset">Сбросить</a>
            </div>
        </form>



        <div class="grid-cards">
            @forelse($courses as $course)
                @php
                    $unit  = $course->price->unit_price ?? 0;
                    $count = $course->lessons_count ?? 0;
                    $total = $unit * $count;
                @endphp
                <div class="card js-course-card"
                     data-id="{{ $course->id }}"
                     data-language="{{ $course->language_id }}"
                     data-level="{{ $course->level }}">
                    <div class="card-header">
                        <h3>{{ $course->title }}</h3>
                        <small>{{ $course->language->name }} • {{ $levels[$course->level] }}</small>
                    </div>
                    <div class="card-body">
                        <!-- …ваш контент… -->
                        <div class="price">{{ $total }} BYN / мес.</div>
                        <span class="price-unit">({{ $unit }} BYN/урок)</span>
                    </div>

                    {{-- вместо <a>…</a> вставляем форму --}}
                    <form action="{{ route('courses.enroll') }}"
                          method="POST"
                          class="enroll-form"
                          style="border-top:1px solid #e0e0e0">
                        @csrf
                        <input type="hidden" name="course_id" value="{{ $course->id }}">
                        <button type="button" class="js-enroll-btn btn-enroll" style="justify-content: center; width: 100%">
                            Записаться
                        </button>
                    </form>
                </div>
            @empty
                <p>Курсы не найдены.</p>
            @endforelse
        </div>

        <div style="margin-top:20px;">{{ $courses->links() }}</div>
    </div>
    <script>
        function togglePriceSort() {
            const url = new URL(window.location.href);
            const sort = url.searchParams.get('sort');
            let dir  = url.searchParams.get('dir') || 'asc';

            // если уже сортируем по price_total, меняем направление
            if (sort === 'price_total') {
                dir = dir === 'asc' ? 'desc' : 'asc';
            } else {
                // иначе включаем сортировку по цене, по возрастанию
                url.searchParams.set('sort', 'price_total');
                dir = 'asc';
            }

            url.searchParams.set('dir', dir);
            // перенаправляем
            window.location.href = url.toString();
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            document.querySelectorAll('.js-enroll-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const card        = btn.closest('.js-course-card');
                    const form        = card.querySelector('.enroll-form');
                    const courseLang  = +card.dataset.language;
                    const courseLevel = card.dataset.level;

                    @guest
                        return Swal.fire({
                        icon: 'info',
                        title: 'Войдите или оставьте заявку',
                        html: 'Чтобы записаться, <a href="{{ route("login") }}">войдите</a> или <a href="{{ route("main") }}">заполните заявку</a>.',
                    });
                    @endguest

                        @auth
                    if ('{{ auth()->user()->role }}' !== 'student') {
                        Swal.fire('Доступно только для студентов', '', 'warning');
                        return;
                    }

                    const userLangLevels = @json(auth()->user()->languages->pluck('pivot.level', 'id'));
                    const studentLevel   = userLangLevels[courseLang] ?? null;
                    if (!studentLevel) {
                        return Swal.fire('У вас не изучается язык этого курса', '', 'error');
                    }
                    if (studentLevel !== courseLevel) {
                        return Swal.fire('Ваш уровень не совпадает с уровнем курса', '', 'error');
                    }

                    Swal.fire({
                        title: 'Подтвердить запись?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Записаться',
                        cancelButtonText: 'Отмена'
                    }).then(result => {
                        if (!result.isConfirmed) return;

                        // Собираем payload
                        const payload = { course_id: form.course_id.value };

                        fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(payload),
                        })
                            .then(res => {
                                if (!res.ok) throw res;
                                return res.json();
                            })
                            .then(data => {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: data.message || 'Вы успешно записались',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true
                                });
                                // Блокируем кнопку, чтобы не дублировать
                                btn.disabled = true;
                                btn.textContent = 'Вы записаны';
                            })
                            .catch(err => {
                                // просто логируем, не показываем пользователю
                                console.error('Enroll error:', err);
                            });
                    });
                    @endauth
                });
            });
        });
    </script>



@endsection

