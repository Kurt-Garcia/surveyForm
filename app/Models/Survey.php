<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\TranslationHeader;

class Survey extends Model
{
    protected $fillable = ['title', 'admin_id', 'is_active', 'total_questions', 'logo', 'department_logo'];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
    
    public function sbus()
    {
        return $this->belongsToMany(Sbu::class, 'survey_sbu');
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

    /**
     * Alias for responseHeaders - used in views
     */
    public function responses(): HasMany
    {
        return $this->hasMany(SurveyResponseHeader::class);
    }

    /**
     * Get all response details for this survey through response headers
     */
    public function responseDetails()
    {
        return $this->hasManyThrough(SurveyResponseDetail::class, SurveyResponseHeader::class, 'survey_id', 'header_id');
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
        
        return $this->sites()->where('site_id', $siteId)->exists();
    }
    
    /**
     * Check if a survey is available for any of the provided site IDs.
     *
     * @param array $siteIds
     * @return bool
     */
    public function isAvailableForAnySite(array $siteIds): bool
    {
        if (empty($siteIds)) {
            return false;
        }
        
        return $this->sites()->whereIn('site_id', $siteIds)->exists();
    }
    
    /**
     * Get available languages for this survey
     *
     * @return array
     */
    public function getAvailableLanguages(): array
    {
        $languages = ['English']; // English is always available as the default
        
        // Get all active translation headers that have translations for this survey's questions
        $availableLocales = TranslationHeader::whereHas('surveyQuestionTranslations', function($query) {
            $query->whereHas('surveyQuestion', function($subQuery) {
                $subQuery->where('survey_id', $this->id);
            });
        })->where('is_active', true)->pluck('name')->toArray();
        
        // Add available languages to the array
        $languages = array_merge($languages, $availableLocales);
        
        return array_unique($languages);
    }
}