<?php

namespace App\Http\Middleware;

use App\Models\Survey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSiteAccess
{
    /**
     * Handle an incoming request.
     * This middleware ensures that users can only access surveys that are deployed to their site.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if there's a survey route parameter
        if ($survey = $request->route('survey')) {
            $userSiteId = \Illuminate\Support\Facades\Auth::check() ? \Illuminate\Support\Facades\Auth::user()->site_id : session('site_id');
            
            // If we have a site_id restriction and the survey doesn't belong to this site, deny access
            if (!$survey->isAvailableForSite($userSiteId)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'You do not have access to this survey.'
                    ], 403);
                }
                
                return redirect()->route('index')
                    ->with('error', 'You do not have access to this survey.');
            }
        }
        
        return $next($request);
    }
}
