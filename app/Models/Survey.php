<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Survey extends Model
{
    protected $fillable = ['title', 'admin_id', 'is_active', 'total_questions', 'logo', 'sbu_id'];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
    
    public function sbu(): BelongsTo
    {
        return $this->belongsTo(Sbu::class);
    }
    
    public function sites()
    {
        return $this->belongsToMany(Site::class, 'survey_site');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(SurveyQuestion::class);
    }

    public function responseHeaders(): HasMany
    {
        return $this->hasMany(SurveyResponseHeader::class);
    }

    public function updateQuestionCount(): void
    {
        $this->total_questions = $this->questions()->count();
        $this->save();
    }
    
    /**
     * Check if a survey is available for a specific site.
     *
     * @param int|null $siteId
     * @return bool
     */
    public function isAvailableForSite(?int $siteId): bool
    {
        if (!$siteId) {
            return false;
        }
        
        return $this->sites()->where('sites.id', $siteId)->exists();
    }
}