@extends('layouts.base')

@section('styles')
    <style>
        .admin-content-wrapper {
            max-width: 1200px;
            margin: 90px auto;
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
        .filter-bar select:hover,
        .filter-bar button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .grid-cards {
            display: grid;
            gap: 20px;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        }
        .teacher-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform .2s;
        }
        .teacher-card:hover {
            transform: translateY(-4px);
        }
        .teacher-avatar {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .teacher-info {
            padding: 16px;
            flex: 1;
        }
        .teacher-name {
            font-family: 'Montserrat SemiBold', sans-serif;
            font-size: 20px;
            margin: 0 0 8px;
            color: #272727;
        }
        .teacher-desc {
            font-size: 14px;
            color: #555;
            line-height: 1.5;
            margin-bottom: 12px;
            flex: 1;
        }
        .teacher-langs {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .lang-badge {
            background: #f1f1f1;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 13px;
            color: #333;
            display: inline-flex;
            align-items: center;
        }
        .lang-badge .level {
            margin-left: 4px;
            font-weight: 600;
            color: #8986FF;
        }

        /* Специфика кнопок */
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

        /* Hover‑эффект */
        .filter-bar select:hover,
        .filter-bar button:hover,
        .filter-bar a.reset-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        /* Адаптив: на узких экранах — всё в колонку */
        @media (max-width: 768px) {
            .filter-bar {
                flex-direction: column;
            }

            .filter-bar input[name="search"],
            .filter-bar select,
            .filter-bar button,
            .filter-bar a.reset-btn {
                flex: 1 1 100%;
                width: 100%;
            }
        }

        .grid-cards {
            display: grid;
            gap: 20px;
            grid-template-columns: repeat(4, 1fr);
        }

        /* Большие планшеты / маленькие ноуты (≤1200px): 3 колонки */
        @media (max-width: 1200px) {
            .grid-cards {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* Планшеты / ландшафтные смартфоны (≤992px): 2 колонки */
        @media (max-width: 992px) {
            .grid-cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Смартфоны портрет (≤576px): 1 колонка */
        @media (max-width: 576px) {
            .grid-cards {
                grid-template-columns: 1fr;
            }
        }

        /* Дополнительные правки для карточек */
        @media (max-width: 992px) {
            .teacher-avatar {
                height: 180px;   /* уменьшили высоту изображения */
            }
            .teacher-name {
                font-size: 18px; /* чуть меньше заголовок */
            }
            .teacher-desc {
                font-size: 13px; /* уменьшили текст описания */
            }
        }

        @media (max-width: 576px) {
            .teacher-avatar {
                height: 150px;
            }
            .teacher-name {
                font-size: 16px;
            }
            .teacher-desc {
                font-size: 12px;
            }
            .lang-badge {
                font-size: 12px;
                padding: 3px 6px;
            }
        }

    </style>
@endsection

@section('content')
    <div class="admin-content-wrapper">

        <form class="filter-bar" method="GET" action="{{ route('teachers') }}">
            <input type="text" name="search" placeholder="Поиск по имени" value="{{ $search }}">
            <select name="language">
                <option value="">Все языки</option>
                @foreach($languages as $lang)
                    <option value="{{ $lang->id }}" {{ $language == $lang->id ? 'selected' : '' }}>
                        {{ $lang->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit">Применить</button>
            <a href="{{ route('teachers') }}" class="reset-btn">Сбросить</a>
        </form>

        <div class="grid-cards">
            @forelse($teachers as $teacher)
                <div class="teacher-card">
                    <img
                        src="{{ $teacher->file_path ? asset($teacher->file_path) : asset('images/default-avatar.png') }}"
                        alt="{{ $teacher->first_name }}"
                        class="teacher-avatar">
                    <div class="teacher-info">
                        <h3 class="teacher-name">
                            {{ $teacher->first_name }} {{ $teacher->last_name }}
                        </h3>
                        <p class="teacher-desc">
                            {{ \Illuminate\Support\Str::limit($teacher->description, 100, '...') }}
                        </p>
                        <div class="teacher-langs">
                            @foreach($teacher->languages as $lang)
                                <span class="lang-badge">
                                {{ $lang->name }}
                                    @if($lang->pivot->level)
                                        <span class="level">({{ $levels[$lang->pivot->level] }})</span>
                                    @endif
                            </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <p>Преподаватели не найдены.</p>
            @endforelse
        </div>

        <div style="margin-top:20px;">
            {{ $teachers->links() }}
        </div>
    </div>
@endsection
