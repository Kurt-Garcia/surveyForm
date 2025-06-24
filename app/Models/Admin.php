<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $guard = 'admin';
    protected $table = 'admin_users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'contact_number',
        'status',
        'disabled_reason',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'status' => 'integer',
        ];
    }

    /**
     * Check if the admin is active (not disabled).
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === 1;
    }

    /**
     * Check if the admin is disabled.
     *
     * @return bool
     */
    public function isDisabled(): bool
    {
        return $this->status === 0;
    }

    /**
     * Scope to filter only active admins.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope to filter only disabled admins.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDisabled($query)
    {
        return $query->where('status', 0);
    }
    
    /**
     * Get all SBUs that the admin has access to (many-to-many relationship).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sbus(): BelongsToMany
    {
        return $this->belongsToMany(Sbu::class, 'admin_sbu', 'admin_id', 'sbu_id')->withTimestamps();
    }
    
    /**
     * Get all Sites that the admin has access to (many-to-many relationship).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sites(): BelongsToMany
    {
        return $this->belongsToMany(Site::class, 'admin_site', 'admin_id', 'site_id')->withTimestamps();
    }
    
    /**
     * Get all themes created by the admin.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function themes()
    {
        return $this->hasMany(\App\Models\ThemeSetting::class);
    }
}