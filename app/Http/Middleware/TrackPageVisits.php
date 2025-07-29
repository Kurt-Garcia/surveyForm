<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\PageVisitService;
use Symfony\Component\HttpFoundation\Response;

class TrackPageVisits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only track GET requests to avoid tracking form submissions, API calls, etc.
        if ($request->isMethod('GET') && !$request->ajax() && !$request->wantsJson()) {
            $this->startPageVisitTracking($request);
        }

        $response = $next($request);

        return $response;
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     * Note: We don't end page visits here because terminate() is called immediately
     * after response is sent, not when user navigates away. Page visits are ended
     * when a new page visit starts or when session expires.
     */
    public function terminate(Request $request, Response $response): void
    {
        // Do nothing - page visits are ended when new visits start
    }

    /**
     * Start tracking page visit
     */
    private function startPageVisitTracking(Request $request)
    {
        // Check which guard is authenticated and get user info
        $userInfo = $this->getAuthenticatedUserInfo();
        
        if ($userInfo) {
            try {
                PageVisitService::startPageVisit(
                    $userInfo['user'], 
                    $userInfo['type'], 
                    $request
                );
            } catch (\Exception $e) {
                // Log error but don't break the application
                \Illuminate\Support\Facades\Log::error('Page visit tracking error: ' . $e->getMessage());
            }
        }
    }

    /**
     * End tracking page visit
     */
    private function endPageVisitTracking(Request $request)
    {
        try {
            PageVisitService::endPageVisit(null, $request);
        } catch (\Exception $e) {
            // Log error but don't break the application
            \Illuminate\Support\Facades\Log::error('Page visit end tracking error: ' . $e->getMessage());
        }
    }

    /**
     * Get authenticated user info from any guard
     */
    private function getAuthenticatedUserInfo()
    {
        $path = request()->path();
        
        // Determine guard priority based on the current route path
        if (str_starts_with($path, 'c2VjcmV0LWRldi1hY2Nlc3MtZmFzdGRldi0yMDI1')) {
            // Developer routes - check developer guard first
            if (Auth::guard('developer')->check()) {
                return [
                    'user' => Auth::guard('developer')->user(),
                    'type' => 'developer'
                ];
            }
        } elseif (str_starts_with($path, 'admin')) {
            // Admin routes - check admin guard first
            if (Auth::guard('admin')->check()) {
                return [
                    'user' => Auth::guard('admin')->user(),
                    'type' => 'admin'
                ];
            }
        } else {
            // Regular user routes - check web guard first
            if (Auth::guard('web')->check()) {
                return [
                    'user' => Auth::guard('web')->user(),
                    'type' => 'user'
                ];
            }
        }
        
        // Fallback: check all guards in order if path-based detection didn't work
        if (Auth::guard('developer')->check()) {
            return [
                'user' => Auth::guard('developer')->user(),
                'type' => 'developer'
            ];
        }
        
        if (Auth::guard('admin')->check()) {
            return [
                'user' => Auth::guard('admin')->user(),
                'type' => 'admin'
            ];
        }
        
        if (Auth::guard('web')->check()) {
            return [
                'user' => Auth::guard('web')->user(),
                'type' => 'user'
            ];
        }
        
        return null;
    }
}