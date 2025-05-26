<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function isMainSite(): bool
    {
        return $this->is_main;
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->sbu->name} - {$this->name}";
    }
}