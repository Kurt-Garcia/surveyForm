<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Admin;

class AccountStatusController extends Controller
{    /**
     * Show the account disabled page
     * 
     * @param Request $request
     */    public function showAccountDisabled(Request $request)
    {
        $disabledReason = null;
        $accountType = null;
        
        // Debug: Log request data
        Log::info('Account Disabled Debug:', [
            'url_params' => $request->all(),
            'session_data' => $request->session()->all(),
            'has_uid' => $request->has('uid'),
            'has_aid' => $request->has('aid'),
            'uid_value' => $request->get('uid'),
            'aid_value' => $request->get('aid'),
        ]);
        
        // Check for user ID in URL parameter
        if ($request->has('uid')) {
            $userId = $request->get('uid');
            $cacheKey = 'disabled_user_' . $userId;
            Log::info('Checking user cache:', ['cache_key' => $cacheKey]);
            
            $cacheData = Cache::get($cacheKey);
            Log::info('User cache data:', ['cache_data' => $cacheData]);
            
            if ($cacheData) {
                $disabledReason = $cacheData['disabled_reason'];
                $accountType = $cacheData['account_type'];
                Cache::forget($cacheKey); // Clean up
                Log::info('Found user cache data:', ['reason' => $disabledReason, 'type' => $accountType]);
            }
        }
        
        // Check for admin ID in URL parameter
        if ($request->has('aid')) {
            $adminId = $request->get('aid');
            $cacheKey = 'disabled_admin_' . $adminId;
            Log::info('Checking admin cache:', ['cache_key' => $cacheKey]);
            
            $cacheData = Cache::get($cacheKey);
            Log::info('Admin cache data:', ['cache_data' => $cacheData]);
            
            if ($cacheData) {
                $disabledReason = $cacheData['disabled_reason'];
                $accountType = $cacheData['account_type'];
                Cache::forget($cacheKey); // Clean up
                Log::info('Found admin cache data:', ['reason' => $disabledReason, 'type' => $accountType]);
            }
        }
          // Fallback: Check session (for backward compatibility)
        if (!$disabledReason) {
            // Check backup cache for admin
            $backupData = Cache::get('disabled_admin_backup');
            if ($backupData && $request->has('aid') && $backupData['id'] == $request->get('aid')) {
                $disabledReason = $backupData['disabled_reason'];
                $accountType = $backupData['account_type'];
                Cache::forget('disabled_admin_backup');
                Log::info('Found admin backup cache data:', ['reason' => $disabledReason, 'type' => $accountType]);
            }
            
            // Check backup cache for user
            $backupData = Cache::get('disabled_user_backup');
            if ($backupData && $request->has('uid') && $backupData['id'] == $request->get('uid')) {
                $disabledReason = $backupData['disabled_reason'];
                $accountType = $backupData['account_type'];
                Cache::forget('disabled_user_backup');
                Log::info('Found user backup cache data:', ['reason' => $disabledReason, 'type' => $accountType]);
            }
            
            // Check if there's a user ID in session (set during logout process)
            if ($request->session()->has('disabled_user_id')) {
                $userId = $request->session()->get('disabled_user_id');
                $user = User::find($userId);
                Log::info('Found disabled user:', ['user_id' => $userId, 'user' => $user]);
                if ($user && $user->status == 0) {
                    $disabledReason = $user->disabled_reason;
                    $accountType = 'User';
                }
                $request->session()->forget('disabled_user_id');
            }
            
            // Check if there's an admin ID in session (set during logout process)
            if ($request->session()->has('disabled_admin_id')) {
                $adminId = $request->session()->get('disabled_admin_id');
                $admin = Admin::find($adminId);
                Log::info('Found disabled admin:', ['admin_id' => $adminId, 'admin' => $admin]);
                if ($admin && $admin->status == 0) {
                    $disabledReason = $admin->disabled_reason;
                    $accountType = 'Admin';
                }
                $request->session()->forget('disabled_admin_id');
            }
        }
          // Provide default values if null
        $disabledReason = $disabledReason ?? '';
        $accountType = $accountType ?? '';
        
        Log::info('Final values:', [
            'disabledReason' => $disabledReason,
            'accountType' => $accountType
        ]);
        
        return view('auth.account-disabled', compact('disabledReason', 'accountType'));
    }
}
