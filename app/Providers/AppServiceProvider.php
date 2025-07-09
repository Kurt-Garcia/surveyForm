<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Services\TranslationService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Translation Service
        $this->app->singleton(TranslationService::class, function ($app) {
            return new TranslationService();
        });
        
        // Load translation helpers
        require_once app_path('Helpers/TranslationHelper.php');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Auth::viaRequest('admin', function (\Illuminate\Http\Request $request) {
            if (Auth::guard('admin')->check()) {
                Session::name(config('session.admin_cookie'));
                return Auth::guard('admin')->user();
            }
            return null;
        });

\Illuminate\Pagination\Paginator::useBootstrap();
    }
}
