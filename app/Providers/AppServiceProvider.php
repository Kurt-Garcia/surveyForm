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
        // Register custom multi-session guard
        Auth::extend('multi-session', function ($app, $name, $config) {
            $guard = new \App\Guards\MultiSessionGuard(
                $name,
                Auth::createUserProvider($config['provider']),
                $app['session.store'],
                $app['request'],
                $config['session_key'] ?? $name
            );

            $guard->setCookieJar($app['cookie']);
            $guard->setDispatcher($app['events']);
            $guard->setRequest($app['request']);

            return $guard;
        });

        \Illuminate\Pagination\Paginator::useBootstrap();
    }
}
