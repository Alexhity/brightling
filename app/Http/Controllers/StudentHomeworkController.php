<?php

namespace App\Http\Controllers;

use App\Models\Homework;
use Illuminate\Http\Request;

class StudentHomeworkController extends Controller
{
    /**
     * Список домашних заданий, доступных текущему студенту.
     */
    public function studentIndex()
    {
        $userId = auth()->id();

        $homeworks = Homework::with('lesson.course')
            ->whereHas('lesson.users', function($q) use($userId) {
                $q->where('users.id', $userId)
                    ->where('users.role', 'student');
            })
            ->orderBy('deadline','asc')
            ->get();

        return view('auth.student.homeworks', compact('homeworks'));
    }

}
