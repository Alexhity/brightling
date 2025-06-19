@extends('layouts.base') {{-- или другой ваш основной layout, где подключается шапка --}}

@section('styles')

    <style>

        .promo-section {
            padding: 60px 0;
        }
        .promo-section h2 {
            font-family: 'Montserrat Bold', sans-serif;
            font-size: 32px;
            color: #333;
            text-align: left;
            margin-bottom: 40px;
        }

        /* Контейнер с двумя колонками */
        .container1 {
            display: grid;
            max-width: 1300px;
            margin: 0 auto;
            width: 100%;
            gap: 20px;
            padding: 0 20px;
        }

        /* Заголовки секций */
        .advantages-section h2 {
            font-family: 'Montserrat Bold', sans-serif;
            font-size: 48px;
            color: #272727;
            padding-bottom: 50px;
            padding-top: 60px;
            margin: 0;
        }

        /* Общий текст */
        .about-text, .about-text p {
            font-size: 18px;
            font-family: 'Montserrat Medium', sans-serif;
            color: #333;
            line-height: 1.6;
            margin-bottom: 16px;
        }

        /* FAQ: стилизация */
        .faq-accordion .faq-item {
            border-bottom: 1px solid #e0e0e0;
        }
        .faq-question {
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 0;
            cursor: pointer;
        }
        .faq-title {
            font-size: 18px;
            font-family: 'Montserrat Medium', sans-serif;
            color: #272727;
        }
        .faq-icon {
            font-size: 24px;
            line-height: 1;
            transition: transform 0.2s ease;
            color: #272727;
        }
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        .faq-answer.open {
            max-height: 500px; /* должно быть больше, чем самый большой ответ */
        }

        .reviews-cards {
            display: grid;
            /* по умолчанию 4 колонки */
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
        }

        /* Средние экраны — 2 колонки */
        @media (max-width: 992px) {
            .reviews-cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Мобильные — 1 колонка */
        @media (max-width: 576px) {
            .reviews-cards {
                grid-template-columns: 1fr;
            }
        }

        .review-card {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            padding: 20px;
            display: flex;
            flex-direction: column;
            transition:
                transform 0.2s ease-out,
                box-shadow 0.2s ease-out;
        }

        /* Эффект «поднятия» */
        .review-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        .review-header {
            margin-bottom: 12px;
        }
        .review-title {
            font-family: 'Montserrat SemiBold', sans-serif;
            font-size: 20px;
            color: #333;
            margin: 0 0 8px;
        }
        .review-rating .star {
            font-size: 22px;
            color: #ccc;
            margin-right: 2px;
        }
        .review-rating .star.filled {
            color: #f4c150;
        }
        .review-body {
            font-family: 'Montserrat Medium', sans-serif;
            font-size: 14px;
            color: #555;
            line-height: 1.6;
            margin: 0 0 16px;
            flex-grow: 1;
        }
        .review-footer {
            font-family: 'Montserrat Medium', sans-serif;
            font-size: 13px;
            color: #777;
            text-align: right;
        }
        .review-author {
            font-style: italic;
        }
        .review-course {
            font-weight: bold;
        }

        /* Адаптив */
        @media (max-width: 800px) {
            .container1 {
                grid-template-columns: 1fr;
            }
            .promo-section {
                padding: 30px 0;
            }
            .advantages-section h2 {
                font-size: 36px;
                padding-bottom: 40px;
            }
            .reviews-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')


    {{-- Секция «Часто задаваемые вопросы» --}}
    <section id="faq" class="promo-section">
        <div class="container1">
            {{-- Единственная колонка: FAQ на всю ширину --}}
            <div class="content-full">
                <div class="advantages-section">
                    <h2>Часто задаваемые вопросы</h2>
                </div>
                <div class="faq-accordion">
                    {{-- Вопрос 1 --}}
                    <div class="faq-item">
                        <button class="faq-question">
                            <span class="faq-title">Сколько длится одно занятие?</span>
                            <span class="faq-icon">+</span>
                        </button>
                        <div class="faq-answer">
                            <p class="about-text">
                                Одно занятие длится 60 минут (1 академический час). Если вы выбрали формат курса с удлинёнными уроками, тогда каждое занятие может длиться 90 минут. Все подробности можно узнать при записи.
                            </p>
                        </div>
                    </div>
                    {{-- Вопрос 2 --}}
                    <div class="faq-item">
                        <button class="faq-question">
                            <span class="faq-title">Как проходят онлайн-занятия?</span>
                            <span class="faq-icon">+</span>
                        </button>
                        <div class="faq-answer">
                            <p class="about-text">
                                Онлайн-занятия проводятся через платформу Zoom. Вам достаточно установить приложение Zoom на компьютер или мобильное устройство и перейти по ссылке, которую мы отправим вам за 10 минут до начала урока.
                            </p>
                        </div>
                    </div>
                    {{-- Вопрос 3 --}}
                    <div class="faq-item">
                        <button class="faq-question">
                            <span class="faq-title">Можно ли перенести или отменить занятие?</span>
                            <span class="faq-icon">+</span>
                        </button>
                        <div class="faq-answer">
                            <p class="about-text">
                                Да, вы можете перенести занятие при условии уведомления не позднее чем за 24 часа до назначенного времени. Если вы не предупредили заранее, занятие считается пропущенным. Подробности в договоре.
                            </p>
                        </div>
                    </div>
                    {{-- Вопрос 4 --}}
                    <div class="faq-item">
                        <button class="faq-question">
                            <span class="faq-title">Предоставляете ли вы учебные материалы?</span>
                            <span class="faq-icon">+</span>
                        </button>
                        <div class="faq-answer">
                            <p class="about-text">
                                Да, все учебные материалы (презентации, раздаточные листы и тексты) мы предоставляем бесплатно в электронном виде. После каждой темы преподаватель высылает вам ссылки для скачивания.
                            </p>
                        </div>
                    </div>
                    {{-- Вопрос 5 --}}
                    <div class="faq-item">
                        <button class="faq-question">
                            <span class="faq-title">Есть ли возрастные ограничения для учеников?</span>
                            <span class="faq-icon">+</span>
                        </button>
                        <div class="faq-answer">
                            <p class="about-text">
                                Мы принимаем учеников от 6 лет и старше. Для детей до 12 лет разработаны специальные программы с элементами игровых методик. Взрослые студенты могут выбирать курсы от общего английского до специализированных (бизнес, подготовка к экзаменам и т.д.).
                            </p>
                        </div>
                    </div>
                    {{-- Вопрос 6 --}}
                    <div class="faq-item">
                        <button class="faq-question">
                            <span class="faq-title">Как выбрать уровень курса?</span>
                            <span class="faq-icon">+</span>
                        </button>
                        <div class="faq-answer">
                            <p class="about-text">
                                После регистрации мы предлагаем пройти короткий онлайн-тест, который поможет определить ваш текущий уровень. По результатам теста наш методист порекомендует подходящую группу.
                            </p>
                        </div>
                    </div>
                    {{-- Вопрос 7 --}}
                    <div class="faq-item">
                        <button class="faq-question">
                            <span class="faq-title">Какие способы оплаты возможны?</span>
                            <span class="faq-icon">+</span>
                        </button>
                        <div class="faq-answer">
                            <p class="about-text">
                                Мы принимаем оплату банковской картой через надёжный платёжный сервис, а также через электронные кошельки. Счёт на оплату можно получить прямо в личном кабинете, а также по запросу на почту.
                            </p>
                        </div>
                    </div>
                    {{-- Вопрос 8 --}}
                    <div class="faq-item">
                        <button class="faq-question">
                            <span class="faq-title">Можно ли учиться в нерабочее время (по выходным)?</span>
                            <span class="faq-icon">+</span>
                        </button>
                        <div class="faq-answer">
                            <p class="about-text">
                                Да, у нас есть группы по выходным и по вечерам, чтобы вы могли совмещать учёбу с работой или учёбой. Расписание формируется ежемесячно в зависимости от количества запросов.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Секция «Отзывы» --}}
    <section id="reviews" class="promo-section">
        <div class="container1">
            <div class="content-full">
                <div class="advantages-section">
                    <h2>Отзывы</h2>
                </div>

                {{-- Если нет отзывов, можно вывести сообщение --}}
                @if($reviews->isEmpty())
                    <p class="about-text">Пока у нас нет отзывов. Будьте первым, кто оставит свой отзыв!</p>
                @else
                    <div class="reviews-cards">
                        @foreach($reviews as $review)
                            <div class="review-card">
                                <div class="review-header">
                                    <h3 class="review-title">{{ $review->title }}</h3>
                                    <div class="review-rating">
                                        {{-- Отображаем звёздочки в зависимости от оценки --}}
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <span class="star filled">★</span>
                                            @else
                                                <span class="star">☆</span>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                                <p class="about-text">{{ $review->comment }}</p>
                                <div class="review-footer">
                                    <span class="review-author">— {{ $review->user->first_name ?? 'Студент' }}</span>,
                                    <span class="review-course">{{ $review->course->title ?? '' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Секция «История школы» --}}
    <section id="history" class="promo-section">
        <div class="container1">
            <div class="content-full">
                <div class="advantages-section">
                    <h2>История школы</h2>
                </div>
                <div class="about-text">
                    <p>Наша школа BrightLing основана в 2025 году как полностью онлайн‑платформа, призванная изменить традиционные методы изучения языков. Идею создания BrightLing подали несколько преподавателей, которые работали удалённо во время пандемии и увидели, что ученики нуждаются в гибком формате без привязки к географии.</p>
                    <p>В первые месяцы мы запустили несколько тестовых групп по английскому и испанскому. Благодаря отзывам первых студентов, уже к середине 2025 года сформировалась полнофункциональная платформа: личный кабинет, система тестирования и подбор преподавателей по индивидуальным запросам.</p>
                    <p>Осенью 2025 года открылись первые авторские курсы «Бизнес‑английский» и «Разговорный английский для путешествий». Мы сразу сделали ставку на интерактив: преподаватели проводят живые обсуждения новостных статей и онлайн‑игры, чтобы студенты максимально быстро погружались в язык.</p>
                    <p>Ежемесячно появляются новые курсы — от подготовки к школьным экзаменам до специализированного английского для IT‑специалистов. Наши преподаватели имеют международные сертификаты (CELTA, TESOL), а наша методика сочетает коммуникативный подход, индивидуальный план и практику реальных ситуаций.</p>
                    <p>Мы строго следим за качеством: каждое занятие записывается, и студенты могут пересмотреть его в личном кабинете. Также действует круглосуточная поддержка, чтобы любые технические или организационные вопросы решались мгновенно.</p>
                    <p>BrightLing продолжает развиваться: уже в 2026 году мы планируем запуск мобильного приложения и добавление новых языков — немецкого и французского. Наша миссия — дать каждому возможность учиться эффективно и комфортно, независимо от места и времени.</p>
                </div>
            </div>
        </div>
    </section>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var items = document.querySelectorAll('.faq-item');

                items.forEach(function(item) {
                    var btn = item.querySelector('.faq-question');
                    var answer = item.querySelector('.faq-answer');
                    var icon = item.querySelector('.faq-icon');

                    btn.addEventListener('click', function() {
                        var isOpen = answer.classList.contains('open');

                        // Закрыть все остальные ответы
                        document.querySelectorAll('.faq-answer.open').forEach(function(openAnswer) {
                            openAnswer.classList.remove('open');
                            var parent = openAnswer.parentElement;
                            parent.querySelector('.faq-icon').textContent = '+';
                        });

                        if (!isOpen) {
                            answer.classList.add('open');
                            icon.textContent = '–';
                        } else {
                            answer.classList.remove('open');
                            icon.textContent = '+';
                        }
                    });
                });
            });
        </script>
@endsection


