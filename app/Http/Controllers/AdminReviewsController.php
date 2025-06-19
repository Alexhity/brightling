<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewsController extends Controller
{
    /**
     * Отображает список всех отзывов с пагинацией.
     */
    public function index(Request $request)
    {
        // Можно фильтровать по статусу? например, ?status=pending
        $query = Review::with(['user', 'course']);

        if ($request->filled('status') && in_array($request->status, ['pending','approved','rejected'])) {
            $query->where('status', $request->status);
        }

        $reviews = $query->latest()->paginate(15);

        return view('auth.admin.reviews.index', compact('reviews'));
    }

    /**
     * Меняет статус отзыва (approved/rejected).
     */
    public function updateStatus(Request $request, Review $review)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $review->status = $data['status'];
        $review->save();

        // Формируем понятное сообщение
        if ($data['status'] === 'approved') {
            $message = "Вы одобрили отзыв «{$review->title}».";
        } elseif ($data['status'] === 'rejected') {
            $message = "Вы отклонили отзыв «{$review->title}».";
        } else {
            $message = "Статус отзыва «{$review->title}» переведён в режим «на модерации».";
        }

        return redirect()
            ->route('admin.reviews.index')
            ->with('success', $message);
    }

    /**
     * Удаляет отзыв.
     */
    public function destroy(Review $review)
    {
        $title = $review->title;
        $review->delete();

        return redirect()
            ->route('admin.reviews.index')
            ->with('success', "Отзыв «{$title}» успешно удалён.");
    }
}
