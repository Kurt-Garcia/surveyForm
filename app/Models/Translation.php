<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class Translation extends Model
{
    protected $table = 'translation_detail';
    
    protected $fillable = [
        'key',
        'translation_header_id',
        'value'
    ];

    /**
     * Get the translation header that owns the translation
     */
    public function translationHeader(): BelongsTo
    {
        return $this->belongsTo(TranslationHeader::class);
    }

    /**
     * Get translation by key and locale
     */
    public static function getTranslation($key, $locale, $default = null)
    {
        $cacheKey = "translation.{$locale}.{$key}";
        
        return Cache::remember($cacheKey, 3600, function() use ($key, $locale, $default) {
            $translation = self::where('key', $key)
                             ->whereHas('translationHeader', function($query) use ($locale) {
                                 $query->where('locale', $locale);
                             })
                             ->first();
            
            return $translation ? $translation->value : $default;
        });
    }

    /**
     * Set translation for a key and locale
     */
    public static function setTranslation($key, $locale, $value)
    {
        $translationHeader = TranslationHeader::where('locale', $locale)->first();
        
        if (!$translationHeader) {
            throw new \Exception("Translation header not found for locale: {$locale}");
        }

        $translation = self::updateOrCreate(
            ['key' => $key, 'translation_header_id' => $translationHeader->id],
            ['value' => $value]
        );

        // Clear cache for this translation
        Cache::forget("translation.{$locale}.{$key}");
        
        return $translation;
    }

    /**
     * Get all translations for a locale
     */
    public static function getLocaleTranslations($locale)
    {
        $cacheKey = "translations.{$locale}";
        
        return Cache::remember($cacheKey, 3600, function() use ($locale) {
            return self::whereHas('translationHeader', function($query) use ($locale) {
                         $query->where('locale', $locale);
                     })
                      ->pluck('value', 'key')
                      ->toArray();
        });
    }

    /**
     * Clear all translation cache
     */
    public static function clearCache()
    {
        Cache::flush();
    }

    /**
     * Get all available locales
     */
    public static function getAvailableLocales()
    {
        return TranslationHeader::getAvailableLocales();
    }

    /**
     * Get all translations grouped by locale
     */
    public static function getAllTranslations()
    {
        return self::with('translationHeader')
                  ->get()
                  ->groupBy('translationHeader.locale');
    }
}
