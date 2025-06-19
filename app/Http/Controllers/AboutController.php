<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function index()
    {
        // Получаем все отзывы с привязкой к пользователю и курсу
        $reviews = Review::with(['user', 'course'])
            ->latest()
            ->get();

        // Передаём переменную $reviews в шаблон
        return view('aboutSchool', compact('reviews'));
    }
}
