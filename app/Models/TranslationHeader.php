<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TranslationHeader extends Model
{
    protected $fillable = [
        'name',
        'locale',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get all translations for this language
     */
    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class);
    }

    /**
     * Get survey question translations for this language
     */
    public function surveyQuestionTranslations(): HasMany
    {
        return $this->hasMany(SurveyQuestionTranslation::class);
    }

    /**
     * Get active translation headers
     */
    public static function active()
    {
        return self::where('is_active', true);
    }

    /**
     * Get translation header by locale
     */
    public static function getByLocale($locale)
    {
        return self::where('locale', $locale)->first();
    }

    /**
     * Get all available locales
     */
    public static function getAvailableLocales()
    {
        return self::active()->pluck('locale')->toArray();
    }

    /**
     * Get locale options for forms
     */
    public static function getLocaleOptions()
    {
        return self::active()->pluck('name', 'locale')->toArray();
    }
}