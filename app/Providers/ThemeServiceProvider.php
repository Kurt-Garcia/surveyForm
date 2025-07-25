<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\ThemeSetting;
use App\Models\Survey;
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
                } elseif (auth()->guard('web')->check()) {
                    // For regular users, get theme from the admin who created them
                    $user = auth()->guard('web')->user();
                    if ($user && $user->created_by) {
                        $activeTheme = ThemeSetting::getActiveTheme($user->created_by);
                    } else {
                        // Fallback to global theme if no creator admin
                        $activeTheme = ThemeSetting::getActiveTheme(null);
                    }
                } else {
                    // For guests, try to get theme from request context
                    $request = request();
                    
                    // Check if we're viewing a survey-related page and can extract admin_id
                    if ($request->route()) {
                        $survey = $request->route('survey');
                        if ($survey && $survey instanceof \App\Models\Survey) {
                            $activeTheme = ThemeSetting::getActiveTheme($survey->admin_id);
                        } else {
                            // Fallback to global theme for non-survey pages
                            $activeTheme = ThemeSetting::getActiveTheme(null);
                        }
                    } else {
                        // Default fallback to global theme
                        $activeTheme = ThemeSetting::getActiveTheme(null);
                    }
                }
                
                $view->with('activeTheme', $activeTheme);
            }
        });
    }
}