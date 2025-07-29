<?php

namespace App\Services;

use App\Models\PageVisitLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

class PageVisitService
{
    /**
     * Start tracking a page visit
     */
    public static function startPageVisit($user, $userType, Request $request = null)
    {
        $request = $request ?: request();
        $route = Route::current();
        
        // End any active visits for this user/session first
        self::endActiveVisits($user, $userType, $request->session()->getId());
        
        $pageVisit = PageVisitLog::create([
            'user_type' => $userType,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'page_url' => $request->fullUrl(),
            'page_title' => self::getPageTitle($route),
            'route_name' => $route ? $route->getName() : null,
            'start_time' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => $request->session()->getId(),
            'additional_data' => [
                'method' => $request->method(),
                'parameters' => $route ? $route->parameters() : [],
            ],
        ]);
        
        // Store the visit ID in session for later reference
        $request->session()->put('current_page_visit_id', $pageVisit->id);
        
        return $pageVisit;
    }

    /**
     * End a page visit
     */
    public static function endPageVisit($visitId = null, Request $request = null)
    {
        $request = $request ?: request();
        
        if (!$visitId) {
            $visitId = $request->session()->get('current_page_visit_id');
        }
        
        if ($visitId) {
            $pageVisit = PageVisitLog::find($visitId);
            if ($pageVisit && !$pageVisit->end_time) {
                $pageVisit->update([
                    'end_time' => now(),
                ]);
                $pageVisit->calculateDuration();
                
                // Remove from session
                $request->session()->forget('current_page_visit_id');
                
                return $pageVisit;
            }
        }
        
        return null;
    }

    /**
     * End all active visits for a user/session
     */
    public static function endActiveVisits($user, $userType, $sessionId)
    {
        $activeVisits = PageVisitLog::where('user_type', $userType)
            ->where('user_id', $user->id)
            ->where('session_id', $sessionId)
            ->whereNull('end_time')
            ->get();
            
        foreach ($activeVisits as $visit) {
            $visit->update(['end_time' => now()]);
            $visit->calculateDuration();
        }
    }

    /**
     * Get page title from route
     */
    private static function getPageTitle($route)
    {
        if (!$route) return null;
        
        $routeName = $route->getName();
        if (!$routeName) return null;
        
        // Convert route names to readable titles
        $titles = [
            'admin.dashboard' => 'Admin Dashboard',
            'admin.customers.index' => 'Customer Management',
            'admin.admins.create' => 'Create Admin',
            'admin.users.index' => 'User Management',
            'admin.surveys.index' => 'Survey Management',
            'developer.dashboard' => 'Developer Dashboard',
            'developer.logs.index' => 'User Logs Dashboard',
            'developer.logs.user-activity' => 'User Activity Logs',
            'developer.logs.login-activity' => 'Login Activity Logs',
            'developer.logs.survey-responses' => 'Survey Response Logs',
            'developer.logs.page-visits' => 'Page Visit Logs',
            'index' => 'Survey Form',
            'welcome' => 'Welcome Page',
        ];
        
        return $titles[$routeName] ?? ucwords(str_replace(['.', '-', '_'], ' ', $routeName));
    }

    /**
     * Get recent page visits
     */
    public static function getRecentVisits($days = 30, $userType = null)
    {
        $query = PageVisitLog::recent($days)
            ->completed()
            ->orderBy('start_time', 'desc');
            
        if ($userType) {
            $query->byUserType($userType);
        }
        
        return $query->get();
    }

    /**
     * Get page visit statistics
     */
    public static function getPageVisitStats($days = 30)
    {
        $totalVisits = PageVisitLog::recent($days)->completed()->count();
        $uniquePages = PageVisitLog::recent($days)->completed()
            ->distinct('route_name')
            ->count('route_name');
        $avgDuration = PageVisitLog::recent($days)->completed()
            ->whereNotNull('duration_seconds')
            ->avg('duration_seconds');
            
        return [
            'total_visits' => $totalVisits,
            'unique_pages' => $uniquePages,
            'avg_duration' => $avgDuration ? round($avgDuration) : 0,
            'admin_visits' => PageVisitLog::recent($days)->completed()->byUserType('admin')->count(),
            'user_visits' => PageVisitLog::recent($days)->completed()->byUserType('user')->count(),
            'developer_visits' => PageVisitLog::recent($days)->completed()->byUserType('developer')->count(),
        ];
    }

    /**
     * Get most visited pages
     */
    public static function getMostVisitedPages($days = 30, $limit = 10)
    {
        return PageVisitLog::recent($days)
            ->completed()
            ->selectRaw('route_name, page_title, COUNT(*) as visit_count, AVG(duration_seconds) as avg_duration')
            ->whereNotNull('route_name')
            ->groupBy('route_name', 'page_title')
            ->orderBy('visit_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get user activity summary
     */
    public static function getUserActivitySummary($days = 30)
    {
        return PageVisitLog::recent($days)
            ->completed()
            ->selectRaw('user_type, user_name, user_email, COUNT(*) as visit_count, SUM(duration_seconds) as total_duration')
            ->groupBy('user_type', 'user_id', 'user_name', 'user_email')
            ->orderBy('visit_count', 'desc')
            ->get();
    }
}