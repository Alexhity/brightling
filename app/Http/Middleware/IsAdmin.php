<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     * @param Closure(Request): (Response) $next
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check() && $request->user()->role === 'admin') {
            return $next($request);
        }

        return redirect()->back()->withErrors('Вы не являетесь администратором');
    }
}
