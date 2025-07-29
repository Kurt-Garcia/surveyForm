<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class PageVisitLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_type',
        'user_id',
        'user_name',
        'user_email',
        'page_url',
        'page_title',
        'route_name',
        'start_time',
        'end_time',
        'duration_seconds',
        'ip_address',
        'user_agent',
        'session_id',
        'additional_data',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'additional_data' => 'array',
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
     * Scope for filtering by route name
     */
    public function scopeByRoute($query, $routeName)
    {
        return $query->where('route_name', $routeName);
    }

    /**
     * Scope for recent visits
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('start_time', '>=', now()->subDays($days));
    }

    /**
     * Scope for completed visits (with end time)
     */
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('end_time');
    }

    /**
     * Scope for active visits (without end time)
     */
    public function scopeActive($query)
    {
        return $query->whereNull('end_time');
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute()
    {
        if (!$this->duration_seconds) {
            return 'N/A';
        }

        $hours = floor($this->duration_seconds / 3600);
        $minutes = floor(($this->duration_seconds % 3600) / 60);
        $seconds = $this->duration_seconds % 60;

        if ($hours > 0) {
            return sprintf('%dh %dm %ds', $hours, $minutes, $seconds);
        } elseif ($minutes > 0) {
            return sprintf('%dm %ds', $minutes, $seconds);
        } else {
            return sprintf('%ds', $seconds);
        }
    }

    /**
     * Calculate and update duration
     */
    public function calculateDuration()
    {
        if ($this->start_time && $this->end_time) {
            // Ensure end_time is after start_time
            if ($this->end_time->greaterThan($this->start_time)) {
                $this->duration_seconds = $this->start_time->diffInSeconds($this->end_time);
            } else {
                // If end_time is same or before start_time, set a minimum duration of 1 second
                $this->duration_seconds = 1;
            }
            $this->save();
        }
    }

    /**
     * Get page title from URL if not set
     */
    public function getDisplayTitleAttribute()
    {
        return $this->page_title ?: $this->route_name ?: $this->page_url;
    }
}