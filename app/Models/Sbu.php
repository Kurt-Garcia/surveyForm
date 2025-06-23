<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sbu extends Model
{
    protected $fillable = ['name'];

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

    public function mainSites()
    {
        return $this->sites()->where('is_main', true);
    }

    public function subSites()
    {
        return $this->sites()->where('is_main', false);
    }
    
    /**
     * Get all admins that have access to this SBU (many-to-many relationship).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(Admin::class, 'admin_sbu', 'sbu_id', 'admin_id')->withTimestamps();
    }
}