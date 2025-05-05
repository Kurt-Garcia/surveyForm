<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\ThemeSetting;
use Illuminate\Support\Facades\View;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share the active theme with all views
        View::composer('*', function ($view) {
            $activeTheme = ThemeSetting::getActiveTheme();
            $view->with('activeTheme', $activeTheme);
        });
    }
}