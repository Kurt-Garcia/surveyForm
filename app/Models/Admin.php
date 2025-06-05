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
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    
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
}