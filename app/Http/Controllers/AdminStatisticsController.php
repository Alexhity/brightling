<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use App\Models\Language;

class AdminStatisticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

        // Считаем статистику
        $stats = [
            'users'     => User::count(),
            'courses'   => Course::count(),
            'languages' => Language::count(),
        ];

        // Получаем последних 5 пользователей
        $recentUsers = User::orderBy('created_at', 'desc')->take(10)->get();


        return view('auth.admin.statistics', compact('stats', 'recentUsers'));
    }
}
