<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'sbu_id',
        'site_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    /**
     * Get the SBU that the admin belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sbu(): BelongsTo
    {
        return $this->belongsTo(Sbu::class);
    }
    
    /**
     * Get the Site that the admin belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}