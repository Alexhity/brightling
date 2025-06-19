<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class AboutSchoolController extends Controller
{
    public function index(Request $request)
    {
        // Берём все одобренные отзывы
        $reviews = Review::with(['user', 'course'])
            ->where('status', 'approved')
            ->latest()
            ->get();

        return view('aboutSchool', compact('reviews'));
    }
}
