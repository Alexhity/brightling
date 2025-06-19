<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentReviewsController extends Controller
{
    /**
     * Показывает форму и список прошлых отзывов студента.
     */
    public function index()
    {
        $student     = Auth::user();
        $courses     = $student->studentCourses; // только курсы роли student
        $ownReviews  = $student->reviews()->with('course')->latest()->get();

        return view('auth.student.reviews', compact('courses', 'ownReviews'));
    }

    /**
     * Сохраняет новый отзыв.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'     => 'required|string|max:255',
            'rating'    => 'required|integer|min:1|max:5',
            'comment'   => 'nullable|string|max:2000',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        Review::create([
            'title'     => $data['title'],
            'rating'    => $data['rating'],
            'comment'   => $data['comment'] ?? null,
            'user_id'   => Auth::id(),
            'course_id' => $data['course_id'] ?? null,
            // статус по умолчанию — pending
        ]);

        return redirect()
            ->route('student.reviews.index')
            ->with('success', 'Спасибо за ваш отзыв! Он будет опубликован после модерации.');
    }

}
