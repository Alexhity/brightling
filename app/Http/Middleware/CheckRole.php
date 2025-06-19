<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Обрабатывает входящий запрос.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string    $role  — роль, которую нужно проверить (например, 'admin', 'teacher', 'student')
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Если пользователь не аутентифицирован или его роль не совпадает с требуемой, прерываем выполнение
        if (!$request->user() || $request->user()->role !== $role) {
            abort(403, 'Unauthorized.');
        }

        // Иначе пропускаем запрос дальше
        return $next($request);
    }
}
