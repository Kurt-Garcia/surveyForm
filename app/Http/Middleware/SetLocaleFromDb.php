<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Models\Translation;

class SetLocaleFromDb
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from session, URL parameter, or default
        $locale = $request->get('locale') ?? 
                  Session::get('locale') ?? 
                  config('app.locale', 'en');
        
        // Check if the locale is available in the database
        $availableLocales = Translation::getAvailableLocales();
        
        if (in_array($locale, $availableLocales)) {
            App::setLocale($locale);
            Session::put('locale', $locale);
        } else {
            // Fallback to default if locale not available
            $defaultLocale = config('app.locale', 'en');
            App::setLocale($defaultLocale);
            Session::put('locale', $defaultLocale);
        }
        
        return $next($request);
    }
}
