<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomRedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards)
    {

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // If the user is authenticated, redirect them to the desired route
                return redirect()->route('vskdashboard');
            }
        }

        // If the user is not authenticated, redirect them to another route
        if (!Auth::check()) {
            return redirect()->route('vskloginget');
        }


        return $next($request);
    }
}
