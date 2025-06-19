<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeveloperAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */    public function handle(Request $request, Closure $next)
    {
        // Check if developer is authenticated
        if (!Auth::guard('developer')->check()) {
            abort(404); // Return 404 instead of unauthorized to hide the existence of this page
        }

        $developer = Auth::guard('developer')->user();
        
        // Check if developer is active
        if (!$developer->isActive()) {
            abort(404); // Return 404 instead of unauthorized to hide the existence of this page
        }

        return $next($request);
    }
}
