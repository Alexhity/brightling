@extends('layouts.base')

@section('title', 'Главная')
@section('styles')
    @vite('resources/css/main.css')
    <style>
        .reviews-section { background: #f5f0fc; padding: 40px 0; }
        .reviews-section h2 { color: #6f42c1; margin-bottom: 30px; }
        .review-card {
            background: #fff;
            border: 1px solid #e0d4f3;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .review-card .rating {
            color: #6f42c1;
            font-weight: bold;
        }
        .review-card .meta {
            font-size: 0.9em;
            color: #555;
            margin-bottom: 10px;
        }

        .form-group, .checkbox {
            position: relative;
            /*margin-bottom: 0.5rem; !* резервируем место для ошибок *!*/
        }
        .input-error {
            border-color: #e3342f !important;
        }
        .error-message {
            color: #e3342f;
            font-size: 0.875rem;
            position: absolute;
            left: 0;
            bottom: -1.5rem;
            width: 100%; /* растягивается на всю ширину контейнера */
            margin: 0;
            display: none;
        }

        .form-container {
            position: relative;
        }

        #success-message {
            color: #8b5cf6; /* Фиолетовый цвет текста */
            text-align: center ;
            margin-top: -40px; /* компенсировать padding-bottom заголовка */
        }

        /* Корневой контейнер секции */
        .feedback-section {
            background-color: #f4f1fa; /* светло-серый с фиолетовым оттенком */
            padding: 60px 0;
        }

        /* Центрируем контент внутри секции */
        .feedback-section .container {
            max-width: 700px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Белая карточка с тенью и скруглениями */
        .feedback-section .feedback-content {
            background: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        /* Заголовок секции */
        .feedback-section .feedback-title {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: #4b0082; /* темно-фиолетовый */
            line-height: 1.2;
        }

        /* Акцентная часть заголовка */
        .feedback-section .feedback-title .highlight {
            color: #ffcc00; /* желтый акцент */
        }

        /* Подзаголовок/текст */
        .feedback-section .feedback-text {
            font-size: 1rem;
            margin-bottom: 25px;
            color: #666666; /* серый текст */
        }

        /* Форма */
        .feedback-section .feedback-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        /* Поле ввода сообщения */
        .feedback-section .feedback-input {
            width: 100%;
            min-height: 120px;
            padding: 15px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            font-size: 1rem;
            resize: vertical;
            color: #333333;
        }

        /* Фокус на поле ввода */
        .feedback-section .feedback-input:focus {
            outline: none;
            border-color: #4b0082;
            box-shadow: 0 0 0 2px rgba(75, 0, 130, 0.2);
        }

        /* Кнопка отправки */
        .feedback-section .feedback-btn {
            align-self: center;
            background-color: #4b0082; /* фиолетовый */
            color: #ffffff;
            border: none;
            padding: 15px 30px;
            font-size: 1rem;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        /* Hover на кнопку */
        .feedback-section .feedback-btn:hover {
            background-color: #330050; /* темнее фиолетового */
        }

        /* Сообщение об успешной отправке */
        .feedback-section .feedback-success {
            margin-top: 20px;
            color: #4b0082; /* можно оставить фиолетовым */
            font-weight: 500;
        }


    </style>
@endsection
@section('content')
    <div>Lorem ipsum dolor sit amet.</div>
    <section class="promo-section">
        <div class="container1">
            <!-- Информационный блок -->
            <div class="promo-info">
                <h1>Интересное <br> изучение языков <br> с BrightLing</h1>
                <ul class="promo-list">
                    <li>
                        <div><span class="custom-marker"></span></div>
                        <div>Разработаем стратегию обучения, которая <br> идеально вам подойдет</div>
                    </li>
                    <li>
                        <div><span class="custom-marker"></span> </div>
                        <div>Бесплатная консультация с топовым <br> экспертом</div>
                    </li>
                    <li>
                        <div><span class="custom-marker"></span> </div>
                        <div>Поможем легко начать говорить на новом <br> иностранном языке</div>
                    </li>
                </ul>
            </div>


            <div class="promo-form">
                <h2>Запишитесь на тестовый урок</h2>
                {{-- Сообщение об успешной отправке --}}

                <div class="form-container">
                    <div id="success-message"
                         style="display: {{ session('success') ? 'block' : 'none' }};">
                        {{ session('success') }}
                    </div>
                    <form id="free-lesson-form"
                          action="{{ route('free_lesson_request.store') }}"
                          method="POST"
                          class="form"
                          novalidate>
                        @csrf

                        <div class="form-group">
                            <input type="text"
                                   id="name"
                                   name="name"
                                   placeholder="Имя"
                                   value="{{ old('name') }}">
                            <div class="error-message" data-for="name"></div>
                        </div>

                        <div class="form-group">
                            <input type="tel"
                                   id="phone"
                                   name="phone"
                                   placeholder="+375 (__) ___ __ __"
                                   value="{{ old('phone') }}">
                            <div class="error-message" data-for="phone"></div>
                        </div>

                        <div class="form-group">
                            <input type="email"
                                   id="email"
                                   name="email"
                                   placeholder="example@email.com"
                                   value="{{ old('email') }}"
                                   class="{{ $errors->has('email') ? 'input-error' : '' }}">
                            <div class="error-message"
                                 data-for="email"
                                 style="display: {{ $errors->has('email') ? 'block' : 'none' }};">
                                 {{ $errors->first('email') }}
                            </div>
                        </div>

                        <div class="form-group">
                            <select id="language" name="language">
                                <option value="" disabled {{ old('language') ? '' : 'selected' }}>
                                    Выберите язык
                                </option>
                                @foreach($languages as $language)
                                    <option value="{{ $language->id }}"
                                        {{ old('language') == $language->id ? 'selected' : '' }}>
                                        {{ $language->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="error-message" data-for="language"></div>
                        </div>

                        <button type="submit" class="btn">Хочу на урок</button>

                        <label class="checkbox">
                            <div>
                                <input type="checkbox" id="agreement" name="agreement">
                                <span></span>
                            </div>
                            <div>
                                Соглашаюсь на обработку персональных данных в соответствии с условиями
                                <a href="#">пользовательского соглашения</a>
                            </div>
                            <div class="error-message" data-for="agreement"></div>
                        </label>

                    </form>
                </div>
            </div>


        </div>
    </section>

    <section class="advantages-section">
        <div class="container2">
            <h2>Преимущества изучения языка <br> в школе BrightLing</h2>
            <div class="advantages-grid">
                <div class="advantage-card">
                    <div class="icon">
                        <img
                            src="{{ asset('images/icon_cont3_1.png') }}"
                            alt="Индивидуальный подход">
                    </div>
                    <h3 class="h3">Индивидуальный подход</h3>
                    <p>Мы разработаем программу обучения, которая учитывает ваши цели и уровень владения языком.</p>
                </div>
                <div class="advantage-card">
                    <div class="icon">
                        <img
                            src="{{ asset('images/icon_cont3_2.png') }}"
                            alt="Индивидуальный подход">
                    </div>
                    <h3 class="h3">Старт с любого уровня</h3>
                    <p>Поднимем ваш английский на новый уровень, независимо от вашего текущего старта!</p>
                </div>
                <div class="advantage-card">
                    <div class="icon">
                        <img
                            src="{{ asset('images/icon_cont3_3.png') }}"
                            alt="Индивидуальный подход">
                    </div>
                    <h3 class="h3">Результаты с первого урока</h3>
                    <p>Мы гарантируем вам, что вы начнёте говорить на новом языке уже с первых занятий.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="trial-lesson-section">
        <div class="container3">
            <div>
                <h2>Пробный урок идеально подойдет вам, если вы хотите:</h2>
            </div>
            <div>
                <ul class="benefits-list">
                    <li>
                        <div><span class="custom-icon"></span></div>
                        <div>Точно определить свой текущий уровень и получить обратную связь.</div>
                    </li>
                    <li>
                        <div><span class="custom-icon"></span></div>
                        <div>Мы поможем вам восстановить навыки и уверенность в изучении языка.</div>
                    </li>
                    <li>
                        <div><span class="custom-icon"></span></div>
                        <div>Наши занятия помогут вам преодолеть языковой барьер и начать общаться.</div>
                    </li>
                    <li>
                        <div><span class="custom-icon"></span></div>
                        <div>Познакомиться с увлекательными методиками, которые заменят скучные правила.</div>
                    </li>
                </ul>
            </div>
        </div>
    </section>
    <section class="trial-lesson">
        <div class="container4">
            <h2>Что вас ждет на пробном уроке</h2>
            <div class="card-container">
                <div class="card">
                    <h3>План обучения</h3>
                    <p>Индивидуальный план, основанный на ваших целях и уровне. Результаты с первого занятия.</p>
                </div>
                <div class="card">
                    <h3>Методика</h3>
                    <p>Современные подходы и практические инструменты, которые делают обучение интересным.</p>
                </div>
                <div class="card">
                    <h3>Длительность</h3>
                    <p>Всего 25-30 минут, чтобы погрузиться в процесс обучения и узнать о своих сильных сторонах.</p>
                </div>
                <div class="card">
                    <h3>Подарок</h3>
                    <p>После урока вы получите рекомендации и бонусные материалы для самостоятельной практики.</p>
                </div>
            </div>
        </div>
    </section>
    <section class="tutors">
        <div class="container5">
            <div class="tutors-section">
                <h2 class="section-title">Учитесь у опытных преподавателей</h2>
                <div class="avatars">
                    <img
                        src="{{ asset('images/tutor1.jpg') }}"
                        alt="Учитель 1">
                    <img
                        src="{{ asset('images/tutor2.jpg') }}"
                        alt="Учитель 2">
                    <img
                        src="{{ asset('images/tutor3.jpg') }}"
                        alt="Учитель 3">
                </div>
                <div class="button-container">
                    <button class="button">Подобрать учителя</button>
                </div>
            </div>

            <div class="teacher-section">
                <div class="teacher-image">
                    <img
                        src="{{ asset('images/tutor1.jpg') }}"
                        alt="Павел Смирнов">
                </div>
                <div class="teacher-info">
                    <h3>Павел <br> Смирнов</h3>
                    <div class="text-teacher">
                        <p>Опытный преподаватель английского языка с 5-летним опытом работы. Он специализируется на обучении разговорному английскому и подготовке к международным экзаменам.</p>
                        <div class="tags">
                            <div class="tag1">Опыт работы 5 лет</div>
                            <div class="tag2">Готовит к экзаменам</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="pricing">
        <div class="container6">
            <h2 class="pricing-title">Стоимость и форматы занятий</h2>
            <div class="pricing-cards">
                <div class="card">
                    <h3>Урок с преподавателем школы</h3>
                    <ul class="features">
                        <li class="feature available">Доступ к онлайн-платформе</li>
                        <li class="feature available">Проверка домашнего задания</li>
                        <li class="feature available">Длительность занятия - 50 минут</li>
                        <li class="feature available">Персональный план обучения</li>
                        <li class="feature available">Индивидуальные занятия</li>
                    </ul>
                    <p class="price">От 40 рублей/урок</p>
                    <button class="learn-more">Узнать подробнее</button>
                </div>
                <div class="card">
                    <h3>Занятие в группе с участниками</h3>
                    <ul class="features">
                        <li class="feature available">Доступ к онлайн-платформе</li>
                        <li class="feature available">Проверка домашнего задания</li>
                        <li class="feature available">Длительность занятия - 50 минут</li>
                        <li class="feature unavailable">Персональный план обучения</li>
                        <li class="feature unavailable">Индивидуальные занятия</li>
                    </ul>
                    <p class="price">От 25 рублей/урок</p>
                    <button class="learn-more">Узнать подробнее</button>
                </div>
                <div class="card">
                    <h3>Самостоятельное обучение</h3>
                    <ul class="features">
                        <li class="feature available">Доступ к онлайн-платформе</li>
                        <li class="feature unavailable">Проверка домашнего задания</li>
                        <li class="feature unavailable">Длительность занятия - 50 минут</li>
                        <li class="feature unavailable">Персональный план обучения</li>
                        <li class="feature unavailable">Индивидуальные занятия</li>
                    </ul>
                    <p class="price">От 70 рублей/месяц</p>
                    <button class="learn-more">Узнать подробнее</button>
                </div>
            </div>
        </div>
    </section>
    <div id="contact-anchor"></div>
    <section id="contact" class="contact-section">
        <div class="contact-container">
            <h2 class="contact-title">Связаться с нами</h2>
            <p class="contact-subtitle">Оставьте свои контакты, и мы свяжемся с вами в ближайшее время</p>

            <form method="POST" action="{{ route('contact.send') }}" class="contact-form">
                @csrf

                {{-- Первая строка: имя и email --}}
                <div class="contact-row">
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Ваше имя"
                        class="contact-input contact-input--name"
                        required
                        style="background: white; color: black;"
                    >
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Ваш email"
                        class="contact-input contact-input--email"
                        required
                        style="background: white; color: black;"
                    >
                </div>

                {{-- Вторая строка: сообщение --}}
                <div class="contact-row">
                <textarea
                    name="message"
                    placeholder="Ваше сообщение"
                    class="contact-textarea"
                    required
                    style="background: white; color: black;"
                >{{ old('message') }}</textarea>
                </div>

                {{-- Кнопка отправки --}}
                <div class="contact-row contact-row--button">
                    <button type="submit" class="contact-button">Отправить</button>
                </div>
            </form>
            @if(session('success'))
                <div class="contact-success">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </section>


    {{--    <section class="reviews">--}}
{{--        <div class="review-container">--}}
{{--            <h2>Отзывы студентов</h2>--}}
{{--            @foreach(\App\Models\Review::with('student','course.teachers')->latest()->get() as $rev)--}}
{{--                <div class="review-card">--}}
{{--                    <div class="meta">--}}
{{--                        От {{ $rev->student->first_name }} {{ $rev->student->last_name }}--}}
{{--                        по курсу “{{ $rev->course->title }}”--}}
{{--                        @if($rev->course->teachers->isNotEmpty())--}}
{{--                            — преподаватель {{ $rev->course->teachers->first()->first_name }}--}}
{{--                            {{ $rev->course->teachers->first()->last_name }}--}}
{{--                        @endif--}}
{{--                        , {{ $rev->created_at->format('d.m.Y') }}--}}
{{--                    </div>--}}
{{--                    <div class="rating">Оценка: {{ $rev->rating }} / 5</div>--}}
{{--                    <h4>{{ $rev->title }}</h4>--}}
{{--                    <p>{{ $rev->comment }}</p>--}}
{{--                </div>--}}
{{--            @endforeach--}}
{{--        </div>--}}
{{--    </section>--}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('free-lesson-form');
                const fields = ['name','phone','email','language','agreement'];

                const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                const phoneRe = /^\+375\d{9}$/;

                const phoneInput = document.getElementById('phone');
                phoneInput.addEventListener('focus', () => {
                    if (!phoneInput.value.startsWith('+375')) {
                        phoneInput.value = '+375';
                    }
                });
                phoneInput.addEventListener('input', () => {
                    let v = phoneInput.value.replace(/[^\d\+]/g, '');
                    if (!v.startsWith('+375')) {
                        v = '+375' + v.replace(/^\+?375/, '');
                    }
                    const rest = v.slice(4).slice(0, 9);
                    phoneInput.value = '+375' + rest;
                });

                form.addEventListener('submit', function(e) {
                    fields.forEach(name => {
                        const el = document.getElementById(name);
                        el.classList.remove('input-error');
                        const msg = form.querySelector(`.error-message[data-for="${name}"]`);
                        msg.style.display = 'none';
                        msg.textContent = '';
                    });

                    let hasError = false;

                    if (!form.name.value.trim()) {
                        setError('name', 'Пожалуйста, введите имя.');
                    }
                    if (!phoneRe.test(form.phone.value.trim())) {
                        setError('phone', 'Номер должен быть в формате +375XXXXXXXXX.');
                    }
                    if (!emailRe.test(form.email.value.trim())) {
                        setError('email', 'Введите корректный e-mail.');
                    }
                    if (!form.language.value) {
                        setError('language', 'Выберите язык.');
                    }
                    if (!form.agreement.checked) {
                        setError('agreement', 'Необходимо согласие на обработку данных.');
                    }

                    if (document.querySelectorAll('.input-error').length) {
                        e.preventDefault();
                    }
                });

                function setError(fieldName, message) {
                    const el = document.getElementById(fieldName);
                    el.classList.add('input-error');
                    const msg = form.querySelector(`.error-message[data-for="${fieldName}"]`);
                    msg.textContent = message;
                    msg.style.display = 'block';
                }
            });

        </script>
@endsection
