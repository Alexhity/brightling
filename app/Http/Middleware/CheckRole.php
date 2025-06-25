<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Обрабатывает входящий запрос.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string    $roles  — роль, которую нужно проверить (например, 'admin', 'teacher', 'student')
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $roles)
    {

        {
            if (! $request->user()) {
                // неавторизованный
                return redirect()->route('login');
            }

            // разбиваем список ролей через |
            $allowed = explode('|', $roles);

            // допустим, у вас в таблице users есть колонка role
            if (! in_array($request->user()->role, $allowed, true)) {
                // можно показать 403 страницу
                abort(403, 'У вас нет прав для просмотра этой страницы.');
            }

            return $next($request);
    }}}
