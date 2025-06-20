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
        $user = auth()->user();

        // Получаем домашние задания для курсов, на которые записан студент
        $homeworks = Homework::whereHas('lesson.course.students', function($query) use ($user) {
            $query->where('users.id', $user->id);
        })
            ->with('lesson.course') // Жадная загрузка
            ->orderBy('deadline', 'asc')
            ->get();

        return view('auth.student.homeworks', compact('homeworks'));
    }

}
