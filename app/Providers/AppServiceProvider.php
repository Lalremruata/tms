<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }


    public function boot()
    {
        View::composer('*', function ($view) {
            $user = Auth::user();
            $username = $user ? $user->tch_profile->first() : null; // Adjust as needed
            $view->with('username', $username);
        });
        Paginator::useBootstrap();
    }
}
