<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Проверяем, что пользователь аутентифицирован и является администратором
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        // Если нет - редирект на главную с сообщением
        return redirect('/')->with('error', 'У вас нет доступа к административной панели');
    }
}
