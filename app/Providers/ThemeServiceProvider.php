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
        // Share the active theme with all views to ensure it's always defined
        View::composer('*', function (\Illuminate\View\View $view) {
            // Only set if not already defined (controllers can override)
            if (!$view->offsetExists('activeTheme')) {
                $activeTheme = null;
                
                // Check if we're in admin context
                if (auth()->guard('admin')->check()) {
                    $admin = auth()->guard('admin')->user();
                    $activeTheme = ThemeSetting::getActiveTheme($admin->id);
                }
                
                $view->with('activeTheme', $activeTheme);
            }
        });
    }
}