@extends('layouts.base')

@section('title', 'Главная')
@section('styles')
    @vite('resources/css/main.css')
    <style>
        @font-face {
            font-family: 'Montserrat Medium';
            src: url('./fonts/montserrat/Montserrat-Medium.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'Montserrat Bold';
            src: url('./fonts/montserrat/Montserrat-Bold.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'Montserrat SemiBold';
            src: url('./fonts/montserrat/Montserrat-SemiBold.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'OpenSans Regular';
            src: url('./fonts/opensans/OpenSans.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }



        /* ГЛАВНАЯ СЕКЦИЯ */
        .promo-section{
            padding: 50px 0;
        }

        .container1{
            display: grid;
            grid-template-columns: 1.3fr 1fr;
            max-width: 1300px; /* Ограничение ширины */
            margin: 0 auto;
            width: 100%;
            gap: 20px;
            padding: 0 20px;
        }

        .promo-info{
            background-color: #8986FF;
            border-radius: 30px;
            padding: 80px 100px 80px ;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Тень */

        }

        .promo-list li {
            position: relative; /* Устанавливаем позиционирование для псевдоэлемента */


            font-family: 'Montserrat SemiBold', sans-serif;
            font-size: 18px;
            color: #F2F2F2;
            margin-bottom: 25px;

            display: grid;
            grid-template-columns: 1fr 15fr;
        }

        /* Убираем нижний отступ у последнего элемента */
        .promo-list li:last-child {
            margin-bottom: 0;
        }

        .promo-list .custom-marker {
            display: inline-block; /* Делаем маркер блочным элементом */
            width: 15px; /* Ширина маркера */
            height: 15px; /* Высота маркера */
            background-color: #FFE644; /* Цвет маркера */
            border-radius: 50%; /* Делаем маркер круглым */
            margin-top: 15px;
        }


        .promo-info h1{
            font-family: 'Montserrat Bold', sans-serif;
            font-size: 48px;
            line-height: 60px;
            color: #FFFFFF;

            padding-bottom: 12%;
        }

        .promo-form{
            background-color: #FFFFFF;
            border-radius: 30px;

            padding: 40px 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Тень */
        }

        .promo-form h2{
            font-family: 'Montserrat SemiBold';
            color: #272727;
            font-size: 18px;
            text-align: center;
            padding-bottom: 40px;
        }

        .promo-form form{
            display: grid;
            gap: 35px;
            margin: 0 auto;
            width: 350px;
        }

        .promo-form input, select{
            font-family: 'Montserrat Medium';
            font-size: 18px;
            padding: 10px 20px;
            width: 100%;
            border: 2px solid #F2F2F2;
            border-radius: 7px;
            background-color: #F2F2F2;
            color: #272727;
        }

        .promo-form button{
            background-color: #FFE644; /* Желтый фон кнопки */
            padding: 10px 20px;
            color: #272727; /* Основной цвет текста */
            font-family: 'Montserrat Medium', sans-serif;
            font-size: 18px;
            width: 100%;
            border: 0;
            border-radius: 7px;
            transition: background-color 0.3s;
            cursor: pointer;
        }

        .promo-form button:hover {
            background-color: #feca1c; /* Потемнение желтого */
        }


        .promo-form input:focus,
        .promo-form select:focus {
            outline: none;
            border-color: #8986FF;
            background-color: #FFFFFF;
        }

        .promo-form label{
            font-family: 'OpenSans Regular';
            font-size: 14px;
            color: #A6A6A6;
        }

        .promo-form label a{
            text-decoration: underline;
            color: #A6A6A6;
        }

        .promo-form label{
            padding: 0;
        }

        .promo-form .checkbox{
            display: grid;
            grid-template-columns: 1fr 11fr;
        }

        .promo-form .checkbox input[type="checkbox"] {
            display: none;

        }

        .promo-form /* Стили для кастомного чекбокса */
        .checkbox span {
            display: inline-block;
            width: 10px;
            height: 10px;
            border: 2px solid #D9D9D9; /* Серый цвет границы */
            background-color: #D9D9D9; /* Серый фон по умолчанию */
            border-radius: 3px; /* Закругленные углы */

            position: relative;
            cursor: pointer;
        }

        .promo-form .checkbox span::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 4px;
            height: 8px;
            border: solid #fff;
            border-width: 0 2px 2px 0;
            transform: translate(-45%, -70%) rotate(45deg); /* Центрируем галочку */
            opacity: 0; /* Прячем галочку по умолчанию */
        }

        /* Стили для активного состояния чекбокса */
        .checkbox input[type="checkbox"]:checked + span {
            background-color: #FFE644; /* Желтый фон */
            border-color: #fce344; /* Желтая граница */
        }

        .checkbox input[type="checkbox"]:checked + span::after {
            border-color: #272727; /* Черная галочка */
            opacity: 1; /* Отображаем галочку */
        }

        .form-group {
            position: relative;

        }

        /* Если произошла ошибка, обводим input красным */
        input.is-invalid {
            border: 1px solid red;
            /* При необходимости добавьте box-shadow для более яркого эффекта */
            box-shadow: 0 0 5px red;
        }

        /* Сообщение об ошибке */
        .invalid-feedback {
            color: red;
            font-size: 0.875em;
            position: absolute;
            bottom: -20px;
            left: 0;
            width: 100%;
            white-space: nowrap;
        }


        /* СЕКЦИЯ 2 */
        .advantages-section{
            padding: 50px 0;
        }

        .container2{
            max-width: 1300px;
            margin: 0 auto;
            width: 100%;
            padding: 0 20px;
        }

        .advantages-section h2{
            font-family: 'Montserrat Bold';
            font-size: 48px;
            color: #272727;
            padding-bottom: 70px;
        }

        .advantages-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px; /* Расстояние между карточками */
        }

        .advantage-card{
            background-color: #FFFFFF;
            border-radius: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Тень */
            padding: 50px;
            display: grid;
            gap: 35px;
            transition: transform 0.3s, box-shadow 0.5s;
        }

        .advantage-card h3{
            font-family: 'Montserrat SemiBold';
            font-size: 26px;
            color: #272727;
        }

        .advantage-card:hover {
            transform: translateY(-5px); /* Поднимается при наведении */
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.15);
        }

        .advantage-card p{
            font-family: 'OpenSans Regular';
            font-size: 16px;
            color: #272727;
        }

        .icon {
            width: 80px;
            height: 80px;
            background-color: #E5E1DA;
            border-radius: 50%;
            display: grid;
            place-items: center; /* Центрирует иконку */
        }

        .icon img {
            width: 50px;
            height: 50px;
        }


        /* СЕКЦИЯ 3 */
        .trial-lesson-section{
            padding: 50px 0;
        }

        .container3{
            max-width: 1300px;
            margin: 0 auto;
            width: 100%;

            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            padding: 0 20px;
        }

        .container3 h2{
            font-family: 'Montserrat SemiBold', sans-serif;
            font-size: 26px;
            color: #272727;
        }

        .container3 .benefits-list{
            font-family: 'Montserrat Medium', sans-serif;
            font-size: 16px;
        }

        .container3 li {
            position: relative; /* Устанавливаем позиционирование для псевдоэлемента */
            margin-bottom: 25px;
            display: grid;
            grid-template-columns: 1fr 15fr;
        }

        /* Убираем нижний отступ у последнего элемента */
        .container3 li:last-child {
            margin-bottom: 0;
        }

        .container3 .custom-icon {
            display: inline-block; /* Делаем маркер блочным элементом */
            width: 15px; /* Ширина маркера */
            height: 15px; /* Высота маркера */
            background-color: #8986FF; /* Цвет маркера */
            border-radius: 50%; /* Делаем маркер круглым */
            margin-top: 15px;
        }




        /* СЕКЦИЯ 4 */
        .trial-lesson{
            padding: 50px 0;
        }

        .container4{
            max-width: 1300px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .container4 h2{
            font-family: 'Montserrat Bold', sans-serif;
            font-size: 48px;
            color: #272727;
            padding-bottom: 70px;
        }

        .container4 .card-container {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 20px; /* Расстояние между карточками */
        }

        .container4 .card{
            background-color: #E5E1DA;
            border-radius: 30px;
            border: 0;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Тень */
            transition: transform 0.3s, box-shadow 0.5s;
        }

        .container4 .card:hover {
            transform: translateY(-5px); /* Поднимается при наведении */
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.15);
        }

        .container4 .card h3{
            font-family: 'Montserrat SemiBold', sans-serif;
            font-size: 26px;
            color: #272727;
            text-align: center;
            padding-bottom: 30px;
        }

        .container4 .card p{
            font-family: 'OpenSans Regular', sans-serif;
            font-size: 16px;
            color: #272727;
        }

        /* СЕКЦИЯ 5*/
        .tutors{
            padding: 50px 0;
        }

        .container5{
            max-width: 1300px;
            margin: 0 auto;
            width: 100%;
            padding: 0 20px;
            background-color: #FFFFFF;
            border-radius: 30px;

            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .container5 h2{
            color: #272727;
            font-family: 'Montserrat Bold';
            font-size: 48px;
            padding-bottom: 130px;
        }

        .container5 .tutors-section{
            padding: 60px 0 20px 60px;
        }

        .container5 .avatars img{
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 35px;
        }

        .container5 .avatars{
            margin-left: 14%;
            display: flex;
            gap: 35px;
        }

        .container5 button{
            background-color: #FFE644; /* Желтый фон кнопки */
            padding: 10px 20px;
            color: #272727; /* Основной цвет текста */
            font-family: 'Montserrat Medium';
            font-size: 18px;

            border: 0;
            border-radius: 7px;
            transition: background-color 0.3s;
            cursor: pointer;
            margin-left: 20%;
        }

        .container5 button:hover {
            background-color: #feca1c; /* Потемнение желтого */
        }

        .container5 .teacher-section{
            display: grid;
            grid-template-columns: 1fr 1fr;
            padding: 60px 60px 20px 0;
        }

        .container5 .teacher-section img{
            width: 100%;
            height: 88%;
            object-fit: cover;
            border-radius: 30px;
        }

        .container5 .teacher-section h3{
            font-size: 26px;
            font-family: 'Montserrat SemiBold';
            color: #272727;
            text-align: center;
        }

        .container5 .teacher-section p{
            font-size: 16px;
            font-family: 'OpenSans Regular';
            color: #272727;
            margin-bottom: 70px;
        }

        .container5 .tag1,
        .container5 .tag2
        {
            font-size: 18px;
            font-family: 'Montserrat Medium';
            color: #272727;
            text-align: center;
            background-color: #E5E1DA;
            border-radius: 30px;
            padding: 5px;
        }

        .container5 .tag1{
            margin-bottom: 25px;
        }


        .container5 .tags{
            padding-bottom: 25px;
        }

        .container5 .text-teacher{
            padding: 30px;
        }









        /* СЕКЦИЯ 6 */
        .pricing{
            padding: 50px 0;
        }

        .container6{
            max-width: 1300px;
            margin: 0 auto;
            width: 100%;
            padding: 0 20px;
        }

        .container6 h2{
            font-family: 'Montserrat Bold';
            font-size: 48px;
            color: #272727;
            margin-bottom: 50px;
        }

        .container6 .pricing-cards {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
        }

        .container6 .card {
            background-color: #fff;
            border-radius: 30px;
            padding: 35px 30px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Тень */
        }

        .container6 .card h3,
        .container6 .card p {
            font-family: 'Montserrat SemiBold';
            font-size: 26px;
            color: #272727;
            margin-bottom: 35px;
        }

        .container6 .card button{
            background-color: #FFE644; /* Желтый фон кнопки */
            padding: 10px 20px;
            color: #272727; /* Основной цвет текста */
            font-family: 'Montserrat Medium';
            font-size: 18px;

            border: 0;
            border-radius: 7px;
            transition: background-color 0.3s;
            cursor: pointer;
        }

        .container6 button:hover {
            background-color: #feca1c; /* Потемнение желтого */
        }


        .features li{
            margin-bottom: 35px;
        }


        .features {
            margin-left: 15%;
        }

        .feature {
            font-size: 16px;
            font-family: 'Open Sans Regular';
            margin: 10px 0;
            display: flex;
            align-items: center;
            padding-left: 10%; /* Отступ для иконок */
            background-repeat: no-repeat;
            background-size: 16px;
            background-position: left center;
            text-align: left;
        }

        .feature.available {
            background-image: url('/public/images/check_icon.png'); /* Путь к картинке галочки */
            color: #272727;
        }

        .feature.unavailable {
            background-image: url('/public/images/cross_icon.png'); /* Путь к картинке крестика */
            color: #A6A6A6;
        }









        /* СЕКЦИЯ 7 */
        .reviews{
            padding: 50px 0;
        }

        .container7{
            max-width: 1300px;
            margin: 0 auto;
            width: 100%;
            padding: 0 20px;
        }

        .container7 h2{
            font-family: 'Montserrat Bold';
            font-size: 48px;
            color: #272727;
            margin-bottom: 50px;
        }

        .container7 .reviews-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            justify-items: center;
        }

        .container7 .review-card {
            background-color: #fff;
            padding: 40px;
            border-radius: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .container7 .review-avatar {
            border-radius: 50%;
            width: 50px;
            height: 50px;
            object-fit: cover;
        }

        .container7 .reviews-container h3 {
            font-size: 18px;
            margin-bottom: 10px;
            font-family: 'Montserrat Medium';
            color: #272727;
        }

        .container7 .reviews-container p {
            font-size: 16px;
            margin-bottom: 10px;
            font-family: 'OpenSans Regular';
            color: #272727;
        }

        .container7 .review-inf{
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
        }

        .review-navigation {
            display: flex;
            justify-content: center;
            gap: 25px;
            margin-top: 35px;
        }

        .nav-button {
            background-color: #8986FF;
            border: none;

            padding: 5px 12px;
            font-size: 25px;
            cursor: pointer;
            border-radius: 50%;
            color: #fff;
        }

        .nav-button:hover {
            background-color: #A6A6A6;
        }

        .feedback-section {
            background-color: #f4f1fa; /* светло-серый с фиолетовым оттенком */
            padding: 60px 0;
        }

        .feedback-section .container {
            max-width: 700px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .feedback-content {
            background: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        .feedback-title {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: #4b0082; /* темно-фиолетовый */
        }

        .feedback-title .highlight {
            color: #ffcc00; /* желтый акцент */
        }

        .feedback-text {
            font-size: 1rem;
            margin-bottom: 25px;
            color: #666666; /* серый текст */
        }

        .feedback-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .feedback-input {
            width: 100%;
            min-height: 120px;
            padding: 15px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            font-size: 1rem;
            resize: vertical;
            color: #333333;
        }

        .feedback-input:focus {
            outline: none;
            border-color: #4b0082;
            box-shadow: 0 0 0 2px rgba(75, 0, 130, 0.2);
        }

        .feedback-btn {
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

        .feedback-btn:hover {
            background-color: #330050; /* темнее фиолетового */
        }

        .feedback-success {
            margin-top: 20px;
            color: #2e7d32; /* зеленый для успеха */
            font-weight: 500;
        }



        /* МЕДИА-ЗАПРОСЫ */

        /* Мобильная версия (до 1024px) */
        @media (max-width: 1024px) {

            .footer-container, .footer-bottom, .container0, .container1{
                padding: 0 20px;
                max-width: 88%;
            }




            /* HEADER */


            .nav {
                display: none; /* Скрываем навигацию по умолчанию */
            }

            .nav.open {
                display: flex; /* Показываем меню */
            }

            .burger-menu {
                display: block; /* Показываем бургер-меню */
            }

            .actions {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .actions .login,
            .actions button {
                font-size: 16px;
            }


            /* FOOTER */
            .footer{
                font-size: 12px;
            }

            .footer h4{
                font-size: 14px;
            }

            .footer-container{
                grid-template-columns: 1fr; /* Все блоки в одну колонку */
                text-align: center;
                gap: 15px;
                padding-top: 15px;
            }

            .nav-column{
                grid-template-columns: 1fr;
            }

            .footer-logo-section{
                display: flex;
                justify-content: space-between;
            }

            .footer-bottom {
                grid-template-columns: 1fr; /* Одна колонка */
                text-align: center; /* Центрирование текста */
                gap: 15px;
            }

            .footer .logo img{
                width: 80px;
            }

            .footer .social-icons a .inst{
                width: 39px;
                height: 39px;
                padding-right: 10px;
            }

            .footer .social-icons a .tg{
                width: 37px;
                height: 37px;
            }

            .footer-line {
                border: none; /* Убираем стандартную линию */
                border-top: 1px solid #A6A6A6; /* Добавляем тонкую линию */
                max-width: 80%;
                width: 80%;
                margin: 25px auto;
                padding: 0 30px;
            }

            /* КНОПКА СКРОЛЛ ВВЕРХ */
            #scrollToTopBtn {
                bottom: 60px;
                right: 10vw;
                font-size: 20px;
                width: 35px;
                height: 35px;
            }

            /* ГЛАВНАЯ СЕКЦИЯ */
            .container1 {
                grid-template-columns: 1fr; /* Один столбец */
                gap: 40px; /* Увеличиваем отступ между блоками */
                padding: 0 20px; /* Уменьшаем боковые отступы */
            }

            .promo-info {
                padding: 40px 20px; /* Уменьшаем отступы внутри блока */
                text-align: center;
            }

            .promo-form {
                padding: 20px; /* Уменьшаем отступы внутри формы */
            }

            .promo-info h1 {
                font-size: 32px; /* Уменьшаем размер шрифта заголовка */
                line-height: 1.2; /* Увеличиваем межстрочный интервал */
            }

            .promo-list li {
                font-size: 16px; /* Уменьшаем шрифт в списке */
            }

            .promo-form form {
                width: 100%; /* Увеличиваем ширину формы */
                gap: 20px; /* Уменьшаем расстояние между элементами */
            }

            .promo-form input, .promo-form select {
                font-size: 16px; /* Уменьшаем шрифт в полях ввода */

            }

            .promo-form button {
                font-size: 16px; /* Уменьшаем шрифт на кнопке */
                padding: 10px 15px; /* Уменьшаем внутренние отступы */
            }

            .promo-form label {
                font-size: 12px; /* Уменьшаем размер текста в соглашении */
            }

            /* СЕКЦИЯ 2 */

            .advantages-grid {
                grid-template-columns: 1fr 1fr; /* Две колонки */
                gap: 30px; /* Увеличиваем расстояние между карточками */
            }

            .advantages-section h2 {
                font-size: 36px; /* Уменьшаем размер заголовка */
            }

            .advantage-card {
                padding: 40px; /* Уменьшаем отступы внутри карточки */
            }

            .advantage-card h3 {
                font-size: 22px; /* Уменьшаем размер заголовка карточки */
            }

            .advantage-card p {
                font-size: 14px; /* Уменьшаем размер текста */
            }

            /* СЕКЦИЯ */
            .container4 h2 {
                font-size: 36px; /* Уменьшаем размер заголовка */
                padding-bottom: 50px; /* Уменьшаем отступ */
                text-align: center; /* Центрируем текст */
            }

            .container4 .card-container {
                grid-template-columns: 1fr 1fr; /* Два столбца */
                gap: 20px; /* Сохраняем расстояние между карточками */
            }

            .container4 .card {
                padding: 20px; /* Уменьшаем отступы внутри карточек */
            }

            .container4 .card h3 {
                font-size: 22px; /* Уменьшаем заголовок карточки */
                padding-bottom: 20px; /* Уменьшаем отступ */
            }

            .container4 .card p {
                font-size: 14px; /* Уменьшаем текст */
            }

            /* СЕКЦИЯ */
            .container5 {
                grid-template-columns: 1fr; /* Перестраиваем секцию в один столбец */
                padding: 20px;
            }

            .container5 h2 {
                font-size: 36px; /* Уменьшаем заголовок */
                padding-bottom: 50px; /* Уменьшаем отступ */
                text-align: center; /* Центрируем заголовок */
            }

            .container5 .tutors-section {
                padding: 30px 20px;
            }

            .container5 .avatars {
                margin: 0 auto; /* Центрируем аватарки */
                gap: 20px; /* Уменьшаем расстояние между аватарами */
                justify-content: center;
            }

            .container5 .avatars img {
                width: 60px; /* Уменьшаем размер аватаров */
                height: 60px;
            }

            .container5 button {
                margin: 20px auto;
                display: block;
                font-size: 16px; /* Уменьшаем шрифт кнопки */
                padding: 8px 16px;
            }

            .container5 .teacher-section {
                grid-template-columns: 1fr; /* Один столбец */
                padding: 5px;
                justify-content: center;
                text-align: center;
            }

            .container5 .teacher-section img {
                width: 40vw; /* Делаем высоту адаптивной */
                text-align: center;

            }

            .container5 .teacher-section h3 {
                font-size: 22px; /* Уменьшаем заголовок */
                margin: 15px 0;
            }

            .container5 .teacher-section p {
                font-size: 14px; /* Уменьшаем текст */
                margin-bottom: 30px; /* Уменьшаем отступ */
            }

            .container5 .tag1, .container5 .tag2 {
                font-size: 14px; /* Уменьшаем теги */
                padding: 4px;
            }

            .container5 .text-teacher {
                padding: 20px; /* Уменьшаем отступы текста */
            }

            /* СЕКЦИЯ */

            .container6 {
                max-width: 95%; /* Увеличиваем ширину для более плотной компоновки */
                padding: 20px;
            }

            .container6 h2 {
                font-size: 36px; /* Уменьшаем размер заголовка */
                margin-bottom: 30px;
                text-align: center; /* Центрируем заголовок */
            }

            .container6 .pricing-cards {
                grid-template-columns: 1fr 1fr; /* Две колонки */
                gap: 15px; /* Уменьшаем расстояние между карточками */
            }

            .container6 .card {
                padding: 25px 20px; /* Уменьшаем внутренние отступы */
                border-radius: 20px; /* Немного уменьшаем скругление */
            }

            .container6 .card h3,
            .container6 .card p {
                font-size: 20px; /* Уменьшаем шрифт заголовков */
                margin-bottom: 20px;
            }

            .features li {
                margin-bottom: 25px; /* Уменьшаем расстояние между списками */
            }

            .feature {
                font-size: 14px; /* Уменьшаем шрифт текста в списке */
                padding-left: 8%; /* Уменьшаем отступ под иконки */
            }

            .container6 .card button {
                font-size: 16px; /* Уменьшаем шрифт кнопки */
                padding: 8px 16px;
            }

            .container7 {
                max-width: 94%; /* Уменьшаем ширину контейнера */
            }

            .container7 h2 {
                font-size: 36px; /* Уменьшаем размер заголовка */
                margin-bottom: 40px; /* Уменьшаем отступ */
                text-align: center; /* Центрируем заголовок */
            }

            .container7 .reviews-container {
                grid-template-columns: 1fr; /* Одна колонка отзывов */
                gap: 20px; /* Уменьшаем расстояние между карточками */
            }

            .container7 .review-card {
                padding: 30px; /* Уменьшаем внутренние отступы */
                border-radius: 20px; /* Скругляем углы меньше */
            }

            .container7 .review-avatar {
                width: 40px; /* Уменьшаем размер аватара */
                height: 40px;
            }

            .container7 .reviews-container h3 {
                font-size: 16px; /* Уменьшаем шрифт имени */
            }

            .container7 .reviews-container p {
                font-size: 14px; /* Уменьшаем шрифт текста */
            }

            .review-navigation {
                gap: 15px; /* Уменьшаем расстояние между кнопками */
            }

            .nav-button {
                padding: 5px 10px; /* Компактнее кнопки */
                font-size: 20px; /* Уменьшаем шрифт кнопок */
            }


            .container8 {
                max-width: 94%; /* Уменьшаем ширину контейнера */
            }

            .container8 h2 {
                font-size: 36px; /* Уменьшаем размер заголовка */
                text-align: center; /* Центрируем заголовок */
                margin-bottom: 20px;
            }

            .container8 p {
                font-size: 20px; /* Уменьшаем шрифт текста */
                text-align: center; /* Центрируем текст */
                margin-bottom: 20px;
            }

            .container8 .content {
                flex-direction: column; /* Перестраиваем блоки в колонку */
                align-items: center; /* Центрируем контент */
                gap: 30px; /* Добавляем отступы между блоками */
            }
            .container8 .content1 {
                text-align: center;
                justify-content: center;
                align-items: center;
            }

            .container8 img {
                width: 70%; /* Уменьшаем изображение */
                height: auto; /* Сохраняем пропорции */
                text-align: center;
            }

            .container8 .form-container form {
                width: 80%; /* Растягиваем форму на всю ширину */
                padding: 40px 20px; /* Уменьшаем внутренние отступы */
            }

            .container8 .form-container input,
            .container8 .form-container select {
                font-size: 16px; /* Уменьшаем шрифт полей */
                padding: 8px 15px; /* Компактные отступы */
            }

            .container8 .form-container button {
                font-size: 16px; /* Уменьшаем шрифт кнопки */
                padding: 8px 15px; /* Компактные отступы */
            }

            .container8 .form-container label {
                font-size: 12px; /* Уменьшаем текст для соглашения */
            }

            .container8 .form-container .checkbox {
                grid-template-columns: 1fr 10fr; /* Уменьшаем колонку текста */
            }
        }

        @media (max-width: 768px) {
            .advantages-grid {
                grid-template-columns: 1fr; /* Одна колонка */
                gap: 40px; /* Увеличиваем расстояние между карточками для визуального комфорта */
            }

            .advantages-section h2 {
                font-size: 28px; /* Уменьшаем заголовок ещё больше */
            }

            .advantage-card {
                padding: 30px; /* Ещё меньше отступы внутри карточки */
            }

            .icon {
                width: 60px;
                height: 60px;
            }

            .icon img {
                width: 40px;
                height: 40px;
            }

            .advantage-card h3 {
                font-size: 20px; /* Снижаем размер заголовка */
            }

            .advantage-card p {
                font-size: 14px; /* Уменьшаем размер текста */
            }

            .footer .email{
                font-size: 10px;
            }

            /* СЕКЦИЯ 3 */
            .container3 {
                grid-template-columns: 1fr; /* Один столбец */
                gap: 40px; /* Уменьшаем расстояние между элементами */
                text-align: center; /* Центрируем текст */
            }

            .container3 h2 {
                font-size: 24px; /* Уменьшаем размер заголовка */
                line-height: 1.4;
                margin-bottom: 20px;
            }

            .container3 .benefits-list {
                font-size: 16px;
            }

            .container3 li {
                grid-template-columns: 1fr 9fr; /* Подгоняем маркер и текст */
                align-items: center; /* Центрируем по вертикали */
                gap: 10px;
            }

            .container3 .custom-icon {
                width: 12px; /* Уменьшаем размер маркера */
                height: 12px;
                margin-top: 0; /* Убираем дополнительное расстояние */
            }


        }

        /* Мобильная версия (до 500px) */
        @media (max-width: 500px) {
            /* HEADER */
            .nav ul {
                display: flex;
                flex-direction: column; /* Навигация вертикальная */
                gap: 20px;
                padding: 0;
                margin: 0;
            }

            .actions {
                display: flex;
                justify-content: left;
                width: 100%;
                gap: 10px;
            }

            .actions button {
                display: none;
            }

            .burger-menu {
                display: block;
                position: absolute;
                top: 20px;
                right: 20px;
            }

            /* FOOTER */
            .nav-footer {
                grid-template-columns: 1fr 1fr; /* Две колонки */
                gap: 20px; /* Отступы между элементами */
                justify-items: center; /* Центрирование колонок */
            }

            .footer .logo img{
                width: 70px;
            }


            .footer .social-icons a .inst{
                width: 32px;
                height: 32px;
                padding-right: 10px;
            }

            .footer .social-icons a .tg{
                width: 31px;
                height: 31px;
            }

            /* ГЛАВНАЯ СЕКЦИЯ */
            .promo-info {
                padding: 30px 15px; /* Еще больше уменьшаем отступы */
            }

            .promo-info h1 {
                font-size: 24px; /* Уменьшаем шрифт для маленьких экранов */
            }

            .promo-form {
                padding: 15px;
            }

            .promo-form form {
                gap: 15px;
            }

            .promo-form input, .promo-form select {
                font-size: 14px;
            }

            .promo-form button {
                font-size: 14px;
                padding: 8px 10px;
            }

            .advantage-card {
                padding: 20px; /* Минимальные отступы */
            }

            .icon {
                width: 50px;
                height: 50px;
            }

            .icon img {
                width: 30px;
                height: 30px;
            }

            /* СЕКЦИЯ 2 */
            .advantage-card h3 {
                font-size: 18px; /* Ещё меньше текст заголовка */
            }

            .advantage-card p {
                font-size: 12px; /* Ещё меньше текст описания */
            }

            /* СЕКЦИЯ 3 */
            .container3 h2 {
                font-size: 20px; /* Делаем текст меньше */
            }

            .container3 .benefits-list {
                font-size: 14px; /* Уменьшаем размер текста */
            }

            .container3 li {
                gap: 5px; /* Минимальный отступ */
            }

            .container3 .custom-icon {
                width: 8px; /* Совсем небольшой маркер */
                height: 8px;
            }

            /* СЕКЦИЯ */
            .container4 h2 {
                font-size: 28px; /* Ещё меньше заголовок */
                padding-bottom: 30px; /* Ещё меньше отступ */
            }

            .container4 .card-container {
                grid-template-columns: 1fr; /* Один столбец */
                gap: 15px; /* Уменьшаем расстояние между карточками */
            }

            .container4 .card {
                padding: 15px; /* Компактные отступы внутри карточек */
            }

            .container4 .card h3 {
                font-size: 20px; /* Заголовок карточки ещё меньше */
                padding-bottom: 15px; /* Уменьшаем отступ */
            }

            .container4 .card p {
                font-size: 12px; /* Текст ещё компактнее */
            }

            /* СЕКЦИЯ */
            .container5 {
                padding: 15px;
            }

            .container5 h2 {
                font-size: 20px; /* Ещё меньше заголовок */
                padding-bottom: 30px; /* Ещё меньше отступ */
            }

            .container5 .avatars {
                flex-direction: column; /* Аватары в колонку */
                gap: 15px; /* Расстояние между аватарами */
                align-items: center; /* Центрируем */
            }

            .container5 .avatars img {
                width: 50px; /* Ещё меньше аватаров */
                height: 50px;
            }

            .container5 button {
                font-size: 14px; /* Ещё меньше текст кнопки */
                padding: 6px 12px;
                margin: 15px auto;
            }

            .container5 .teacher-section img {
                border-radius: 15px; /* Уменьшаем скругление */
            }

            .container5 .teacher-section h3 {
                font-size: 18px; /* Уменьшаем заголовок */
            }

            .container5 .teacher-section p {
                font-size: 10px; /* Ещё меньше текст */
            }

            .container5 .tag1, .container5 .tag2 {
                font-size: 12px; /* Уменьшаем размер тегов */
                padding: 3px;
            }

            .container5 .text-teacher {
                padding: 15px; /* Минимальные отступы текста */
            }

            /* СЕКЦИЯ */
            .container6 {
                padding: 15px; /* Минимизируем отступы */
            }

            .container6 h2 {
                font-size: 28px; /* Ещё меньше заголовок */
                margin-bottom: 20px;
            }

            .container6 .pricing-cards {
                grid-template-columns: 1fr; /* Один столбец */
                gap: 15px; /* Оптимальное расстояние между карточками */
            }

            .container6 .card {
                padding: 20px; /* Уменьшаем отступы */
                border-radius: 15px; /* Минимизируем скругление */
            }

            .container6 .card h3,
            .container6 .card p {
                font-size: 18px; /* Ещё меньше шрифт */
                margin-bottom: 15px;
            }

            .features li {
                margin-bottom: 15px; /* Минимизируем расстояние */
            }

            .feature {
                font-size: 12px; /* Компактный размер текста */
                padding-left: 5%; /* Уменьшаем отступ под иконки */
            }

            .container6 .card button {
                font-size: 14px; /* Минимальный размер шрифта */
                padding: 6px 12px;
            }

            .container7 h2 {
                font-size: 28px; /* Ещё меньше заголовок */
                margin-bottom: 30px; /* Уменьшаем отступ */
            }

            .container7 .reviews-container {
                gap: 15px; /* Уменьшаем расстояние между карточками */
            }

            .container7 .review-card {
                padding: 20px; /* Минимальные внутренние отступы */
                border-radius: 15px; /* Ещё меньше скругление */
            }

            .container7 .review-avatar {
                width: 35px; /* Ещё меньше аватар */
                height: 35px;
            }

            .container7 .reviews-container h3 {
                font-size: 14px; /* Минимальный размер имени */
            }

            .container7 .reviews-container p {
                font-size: 12px; /* Минимальный размер текста */
            }

            .review-navigation {
                gap: 10px; /* Минимальное расстояние между кнопками */
            }

            .nav-button {
                padding: 4px 8px; /* Минимальные отступы */
                font-size: 18px; /* Минимальный размер шрифта */
            }

            .container8 h2 {
                font-size: 28px; /* Ещё меньше размер заголовка */
            }

            .container8 p {
                font-size: 16px; /* Уменьшаем шрифт текста */
                margin-bottom: 15px;
            }

            .container8 img {
                width: 100%; /* Растягиваем изображение на ширину контейнера */
            }

            .container8 .form-container form {
                gap: 20px; /* Уменьшаем расстояние между полями */
                padding: 20px 15px; /* Ещё меньше внутренние отступы */
            }

            .container8 .form-container input,
            .container8 .form-container select {
                font-size: 14px; /* Минимальный размер шрифта полей */
                padding: 6px 10px; /* Компактные отступы */
            }

            .container8 .form-container button {
                font-size: 14px; /* Уменьшаем шрифт кнопки */
                padding: 6px 10px; /* Компактные отступы */
            }

            .container8 .form-container label {
                font-size: 10px; /* Минимальный размер текста для соглашения */
            }

            .container8 .form-container .checkbox {
                grid-template-columns: 1fr 9fr; /* Ещё меньше колонка текста */
            }
        }

        /*Форма обратной связи*/

        .contact-section {
            background-color: #8986FF;
            /* Эти строки отвечают за «выламывание» секции на всю ширину экрана */
            width: 100vw;               /* ширина — ровно 100 % окна браузера */
            position: relative;         /* чтобы left/right работали относительно родителя */
            left: 50%;                  /* сдвигаем секцию на 50 % вправо */
            margin-left: -50vw;         /* сдвигаем обратно на 50 % ширины окна */
            padding: 60px 0 0 0;
        }
        .contact-container {
            max-width: 800px;
            margin: 0 auto;
            color: #FFFFFF;
        }
        .contact-title {
            font-size: 32px;
            margin-bottom: 8px;
            text-align: center;
        }
        .contact-subtitle {
            font-size: 16px;
            margin-bottom: 32px;
            text-align: center;
        }
        .contact-form {
            display: flex;
            flex-direction: column;
        }
        .contact-row {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }
        .contact-input {
            flex: 1;
            min-width: 200px;
            margin: 4px;
            padding: 12px 16px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
        }
        .contact-input::placeholder,
        .contact-textarea::placeholder {
            color: rgba(0, 0, 0, 0.6);
        }
        .contact-textarea {
            width: 100%;
            min-height: 70px;
            margin: 4px;
            padding: 12px 16px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            resize: vertical;
        }
        .contact-button {
            background-color: #FFFFFF;
            color: #8986FF;
            border: none;
            border-radius: 4px;
            padding: 10px 24px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin: 0 auto;
            transition: background-color 0.2s ease;
        }
        .contact-button:hover {
            background-color: rgba(255, 255, 255, 0.9);
        }

        /** Адаптивность **/
        @media (max-width: 600px) {
            .contact-row {
                flex-direction: column;
            }
            .contact-input {
                width: 100%;
                margin: 4px 0;
            }
            .contact-textarea {
                margin: 4px 0;
            }
        }

        .contact-success {
            background-color: rgba(255, 255, 255, 0.8);
            color: #333333;
            padding: 12px 16px;
            border-radius: 4px;
            margin-bottom: 16px;
            text-align: center;
        }

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
                    <p>После урока вы получите рекомендации для самостоятельной практики.</p>
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

                <button class="button"
                        onclick="window.location='{{ route('teachers') }}'">
                    Наши преподаватели
                </button>
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
                        <li class="feature available">Длительность занятия - 60 минут</li>
                        <li class="feature available">Персональный план обучения</li>
                        <li class="feature available">Индивидуальные занятия</li>
                    </ul>
                    <p class="price">От 40 рублей/урок</p>
                    <button class="learn-more"
                            onclick="window.location='{{ route('prices') }}'">
                        Узнать подробнее
                    </button>
                </div>
                <div class="card">
                    <h3>Занятия в паре с другим участником</h3>
                    <ul class="features">
                        <li class="feature available">Доступ к онлайн-платформе</li>
                        <li class="feature available">Проверка домашнего задания</li>
                        <li class="feature available">Длительность занятия - 60 минут</li>
                        <li class="feature available">Персональный план обучения</li>
                        <li class="feature unavailable">Индивидуальные занятия</li>
                    </ul>
                    <p class="price">От 30 рублей/урок</p>
                    <button class="learn-more"
                            onclick="window.location='{{ route('prices') }}'">
                        Узнать подробнее
                    </button>
                </div>
                <div class="card">
                    <h3>Занятие в группе с участниками</h3>
                    <ul class="features">
                        <li class="feature available">Доступ к онлайн-платформе</li>
                        <li class="feature available">Проверка домашнего задания</li>
                        <li class="feature available">Длительность занятия - 60 минут</li>
                        <li class="feature unavailable">Персональный план обучения</li>
                        <li class="feature unavailable">Индивидуальные занятия</li>
                    </ul>
                    <p class="price">От 25 рублей/урок</p>
                    <button class="learn-more"
                            onclick="window.location='{{ route('prices') }}'">
                        Узнать подробнее
                    </button>
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
