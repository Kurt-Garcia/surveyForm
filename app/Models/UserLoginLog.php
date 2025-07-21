<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserLoginLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_type',
        'user_id',
        'user_name',
        'user_email',
        'action',
        'ip_address',
        'user_agent',
        'action_time',
    ];

    protected $casts = [
        'action_time' => 'datetime',
    ];

    /**
     * Get the user based on user_type
     */
    public function user()
    {
        switch ($this->user_type) {
            case 'admin':
                return $this->belongsTo(Admin::class, 'user_id');
            case 'user':
                return $this->belongsTo(User::class, 'user_id');
            case 'developer':
                return $this->belongsTo(Developer::class, 'user_id');
            default:
                return null;
        }
    }

    /**
     * Scope for filtering by user type
     */
    public function scopeByUserType($query, $userType)
    {
        return $query->where('user_type', $userType);
    }

    /**
     * Scope for filtering by action
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for recent activities
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('action_time', '>=', now()->subDays($days));
    }
}
