<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    protected $fillable = ['sbu_id', 'name', 'is_main'];

    protected $casts = [
        'is_main' => 'boolean',
    ];

    public function sbu(): BelongsTo
    {
        return $this->belongsTo(Sbu::class);
    }

    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(Admin::class, 'admin_site', 'site_id', 'admin_id')->withTimestamps();
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function isMainSite(): bool
    {
        return $this->is_main;
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->sbu->name} - {$this->name}";
    }
}