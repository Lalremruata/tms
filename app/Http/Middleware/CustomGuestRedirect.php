<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomGuestRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $redirectToRoute
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $redirectToRoute = null)
    {
        if (Auth::check()) {
            return redirect($redirectToRoute ?: '/vskdashboard');
        }

        return $next($request);
    }
}
