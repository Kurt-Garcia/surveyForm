<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\PageVisitLog;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PageVisitTracker
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only track authenticated users
        if ($this->shouldTrack($request)) {
            $this->trackPageVisit($request);
        }

        return $next($request);
    }

    /**
     * Determine if the request should be tracked
     */
    private function shouldTrack(Request $request): bool
    {
        // Don't track AJAX requests, API calls, or asset requests
        if ($request->ajax() || 
            $request->wantsJson() || 
            $request->is('api/*') ||
            $request->is('*.css') ||
            $request->is('*.js') ||
            $request->is('*.png') ||
            $request->is('*.jpg') ||
            $request->is('*.jpeg') ||
            $request->is('*.gif') ||
            $request->is('*.svg') ||
            $request->is('*.ico')) {
            return false;
        }

        // Check if user is authenticated in any guard
        return Auth::guard('admin')->check() || 
               Auth::guard('web')->check() || 
               Auth::guard('developer')->check();
    }

    /**
     * Track the page visit
     */
    private function trackPageVisit(Request $request): void
    {
        // Close any previous active visit for this session
        $this->closePreviousVisit($request);

        // Get user information
        $userInfo = $this->getUserInfo();
        
        if (!$userInfo) {
            return;
        }

        // Create new page visit log
        PageVisitLog::create([
            'user_type' => $userInfo['type'],
            'user_id' => $userInfo['id'],
            'user_name' => $userInfo['name'],
            'user_email' => $userInfo['email'],
            'page_url' => $request->fullUrl(),
            'page_name' => $this->getPageName($request),
            'route_name' => $request->route() ? $request->route()->getName() : null,
            'start_time' => Carbon::now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => $request->session()->getId(),
        ]);
    }

    /**
     * Close previous active visit for the same session
     */
    private function closePreviousVisit(Request $request): void
    {
        $sessionId = $request->session()->getId();
        
        $activeVisit = PageVisitLog::where('session_id', $sessionId)
            ->whereNull('end_time')
            ->latest('start_time')
            ->first();

        if ($activeVisit) {
            $endTime = Carbon::now();
            $activeVisit->update([
                'end_time' => $endTime,
                'duration_seconds' => $endTime->diffInSeconds($activeVisit->start_time)
            ]);
        }
    }

    /**
     * Get current user information
     */
    private function getUserInfo(): ?array
    {
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            return [
                'type' => 'admin',
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ];
        }

        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            return [
                'type' => 'user',
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ];
        }

        if (Auth::guard('developer')->check()) {
            $user = Auth::guard('developer')->user();
            return [
                'type' => 'developer',
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ];
        }

        return null;
    }

    /**
     * Get human-readable page name
     */
    private function getPageName(Request $request): string
    {
        $routeName = $request->route() ? $request->route()->getName() : null;
        
        if ($routeName) {
            // Convert route names to readable format
            $pageNames = [
                'admin.dashboard' => 'Admin Dashboard',
                'admin.surveys.index' => 'Surveys List',
                'admin.surveys.create' => 'Create Survey',
                'admin.surveys.edit' => 'Edit Survey',
                'admin.admins.index' => 'Admins List',
                'admin.admins.create' => 'Create Admin',
                'admin.users.index' => 'Users List',
                'admin.users.create' => 'Create User',
                'admin.customers.index' => 'Customers List',
                'admin.themes.index' => 'Themes Management',
                'admin.logos.index' => 'Logo Management',
                'admin.translations.index' => 'Translations Management',
                'developer.dashboard' => 'Developer Dashboard',
                'developer.surveys' => 'Developer Surveys',
                'developer.admins' => 'Developer Admins',
                'developer.users' => 'Developer Users',
                'developer.logs.index' => 'User Logs Dashboard',
                'developer.logs.login-activity' => 'Login Activity Logs',
                'developer.logs.user-activity' => 'User Activity Logs',
                'developer.logs.survey-responses' => 'Survey Response Logs',
                'user.dashboard' => 'User Dashboard',
                'surveys.show' => 'Survey Form',
                'surveys.thank-you' => 'Thank You Page',
            ];

            return $pageNames[$routeName] ?? ucwords(str_replace(['.', '-', '_'], ' ', $routeName));
        }

        // Fallback to URL path
        $path = trim($request->getPathInfo(), '/');
        return $path ? ucwords(str_replace(['/', '-', '_'], ' ', $path)) : 'Home';
    }
}
