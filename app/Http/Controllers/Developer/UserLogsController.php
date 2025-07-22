<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Models\UserLoginLog;
use App\Services\UserLogService;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class UserLogsController extends Controller
{
    /**
     * Display the user logs dashboard
     */
    public function index()
    {
        // Get statistics for dashboard
        $stats = [
            'total_logins' => UserLoginLog::whereDate('action_time', today())
                ->where('action', 'login')
                ->count(),
            'active_users' => UserLoginLog::whereDate('action_time', today())
                ->distinct('user_id', 'user_type')
                ->count(),
            'total_activities' => Activity::whereDate('created_at', today())->count(),
            'avg_session_time' => $this->calculateAverageSessionTime()
        ];

        // Get chart data for last 7 days
        $chartData = $this->getLoginChartData();

        return view('developer.logs.index', compact('stats', 'chartData'));
    }

    /**
     * Display user activity logs page
     */
    public function userActivity()
    {
        return view('developer.logs.user-activity');
    }

    /**
     * Display login activity logs page
     */
    public function loginActivity()
    {
        // Get statistics for login activity page
        $stats = [
            'today_logins' => UserLoginLog::whereDate('action_time', today())
                ->where('action', 'login')
                ->count(),
            'unique_users' => UserLoginLog::whereDate('action_time', today())
                ->distinct('user_id', 'user_type')
                ->count(),
            'recent_activity' => UserLoginLog::where('action_time', '>=', now()->subHour())
                ->count(),
            'unique_ips' => UserLoginLog::whereDate('action_time', today())
                ->distinct('ip_address')
                ->count()
        ];

        return view('developer.logs.login-activity', compact('stats'));
    }

    /**
     * Get user activity data for DataTables
     */
    public function getUserActivityData(Request $request)
    {
        $query = Activity::with(['causer'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('user_type')) {
            $userType = $request->user_type;
            $query->where('causer_type', 'like', "%{$userType}%");
        }

        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Handle single activity request
        if ($request->filled('activity_id')) {
            $activity = Activity::find($request->activity_id);
            if ($activity) {
                // Ensure properties are properly formatted
                $activityData = $activity->toArray();
                if (isset($activityData['properties']) && is_string($activityData['properties'])) {
                    $activityData['properties'] = json_decode($activityData['properties'], true) ?? [];
                }
                return response()->json([
                    'data' => [$activityData]
                ]);
            }
            return response()->json([
                'data' => []
            ]);
        }

        return DataTables::of($query)
            ->addColumn('causer', function ($activity) {
                if (!$activity->causer) {
                    return null;
                }
                
                // Return causer data with proper structure
                $causer = $activity->causer;
                $data = [
                    'name' => $causer->name ?? $causer->username ?? 'Unknown User',
                    'email' => $causer->email ?? '',
                    'username' => $causer->username ?? null,
                ];
                
                // Add superadmin property for Admin users
                if (strpos($activity->causer_type, 'Admin') !== false && isset($causer->superadmin)) {
                    $data['superadmin'] = (bool)$causer->superadmin;
                }
                
                return $data;
            })
            ->editColumn('event', function ($activity) {
                return $activity->event ?? 'unknown';
            })
            ->editColumn('subject_type', function ($activity) {
                return $activity->subject_type ?? null;
            })
            ->editColumn('description', function ($activity) {
                return $activity->description ?? null;
            })
            ->editColumn('properties', function ($activity) {
                // Ensure properties are returned as an object/array, not a JSON string
                $properties = $activity->properties;
                if (is_string($properties)) {
                    $properties = json_decode($properties, true) ?? [];
                }
                return $properties ?? [];
            })
            ->editColumn('created_at', function ($activity) {
                return $activity->created_at ? $activity->created_at->toISOString() : null;
            })
            ->rawColumns(['causer'])
            ->make(true);
    }

    /**
     * Get login activity data for DataTables
     */
    public function getLoginActivityData(Request $request)
    {
        $query = UserLoginLog::orderBy('action_time', 'desc');

        // Apply filters
        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('action_time', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('action_time', '<=', $request->date_to);
        }

        if ($request->filled('ip_address')) {
            $query->where('ip_address', 'like', '%' . $request->ip_address . '%');
        }

        if ($request->filled('user_search')) {
            $search = $request->user_search;
            $query->where(function ($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                  ->orWhere('user_email', 'like', "%{$search}%");
            });
        }

        return DataTables::of($query)
            ->addColumn('is_superadmin', function ($log) {
                if ($log->user_type === 'admin') {
                    // Get the admin user to check if they're a superadmin
                    $admin = \App\Models\Admin::find($log->user_id);
                    return $admin ? $admin->superadmin : false;
                }
                return false;
            })
            ->make(true);
    }

    /**
     * Get login chart data for dashboard
     */
    private function getLoginChartData()
    {
        $days = [];
        $adminLogins = [];
        $userLogins = [];
        $developerLogins = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $days[] = $date->format('M d');

            $adminLogins[] = UserLoginLog::whereDate('action_time', $date)
                ->where('user_type', 'admin')
                ->where('action', 'login')
                ->count();

            $userLogins[] = UserLoginLog::whereDate('action_time', $date)
                ->where('user_type', 'user')
                ->where('action', 'login')
                ->count();

            $developerLogins[] = UserLoginLog::whereDate('action_time', $date)
                ->where('user_type', 'developer')
                ->where('action', 'login')
                ->count();
        }

        return [
            'labels' => $days,
            'admin_logins' => $adminLogins,
            'user_logins' => $userLogins,
            'developer_logins' => $developerLogins
        ];
    }

    /**
     * Calculate average session time
     */
    private function calculateAverageSessionTime()
    {
        // Get login-logout pairs from today
        $logins = UserLoginLog::whereDate('action_time', today())
            ->where('action', 'login')
            ->get();

        $totalMinutes = 0;
        $sessionCount = 0;

        foreach ($logins as $login) {
            $logout = UserLoginLog::where('user_type', $login->user_type)
                ->where('user_id', $login->user_id)
                ->where('action', 'logout')
                ->where('action_time', '>', $login->action_time)
                ->first();

            if ($logout) {
                $sessionMinutes = $login->action_time->diffInMinutes($logout->action_time);
                $totalMinutes += $sessionMinutes;
                $sessionCount++;
            }
        }

        if ($sessionCount === 0) {
            return '0m';
        }

        $avgMinutes = round($totalMinutes / $sessionCount);
        
        if ($avgMinutes >= 60) {
            $hours = floor($avgMinutes / 60);
            $minutes = $avgMinutes % 60;
            return $minutes > 0 ? "{$hours}h {$minutes}m" : "{$hours}h";
        }

        return "{$avgMinutes}m";
    }
}
