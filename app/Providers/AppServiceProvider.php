<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Auth::viaRequest('admin', function ($request) {
            if (Auth::guard('admin')->check()) {
                Session::name(config('session.admin_cookie'));
                return Auth::guard('admin')->user();
            }
            return null;
        });

\Illuminate\Pagination\Paginator::useBootstrap();
    }
}
