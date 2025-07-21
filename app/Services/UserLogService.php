<?php

namespace App\Services;

use App\Models\UserLoginLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserLogService
{
    /**
     * Log user login activity
     */
    public static function logLogin($user, $userType, Request $request = null)
    {
        $request = $request ?: request();
        
        UserLoginLog::create([
            'user_type' => $userType,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'action' => 'login',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'action_time' => now(),
        ]);
    }

    /**
     * Log user logout activity
     */
    public static function logLogout($user, $userType, Request $request = null)
    {
        $request = $request ?: request();
        
        UserLoginLog::create([
            'user_type' => $userType,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'action' => 'logout',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'action_time' => now(),
        ]);
    }

    /**
     * Get recent login activities
     */
    public static function getRecentLogins($days = 30, $userType = null)
    {
        $query = UserLoginLog::recent($days)
            ->byAction('login')
            ->orderBy('action_time', 'desc');
            
        if ($userType) {
            $query->byUserType($userType);
        }
        
        return $query->get();
    }

    /**
     * Get recent logout activities
     */
    public static function getRecentLogouts($days = 30, $userType = null)
    {
        $query = UserLoginLog::recent($days)
            ->byAction('logout')
            ->orderBy('action_time', 'desc');
            
        if ($userType) {
            $query->byUserType($userType);
        }
        
        return $query->get();
    }

    /**
     * Get all login/logout activities
     */
    public static function getAllLoginActivities($userType = null)
    {
        $query = UserLoginLog::orderBy('action_time', 'desc');
        
        if ($userType) {
            $query->byUserType($userType);
        }
        
        return $query->get();
    }

    /**
     * Get login statistics
     */
    public static function getLoginStats($days = 30)
    {
        $totalLogins = UserLoginLog::recent($days)->byAction('login')->count();
        $totalLogouts = UserLoginLog::recent($days)->byAction('logout')->count();
        $uniqueUsers = UserLoginLog::recent($days)->byAction('login')
            ->selectRaw('COUNT(DISTINCT CONCAT(user_type, "-", user_id)) as count')
            ->first()->count;
            
        return [
            'total_logins' => $totalLogins,
            'total_logouts' => $totalLogouts,
            'unique_users' => $uniqueUsers,
            'admin_logins' => UserLoginLog::recent($days)->byAction('login')->byUserType('admin')->count(),
            'user_logins' => UserLoginLog::recent($days)->byAction('login')->byUserType('user')->count(),
            'developer_logins' => UserLoginLog::recent($days)->byAction('login')->byUserType('developer')->count(),
        ];
    }
}
