<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Translation extends Model
{
    protected $fillable = [
        'key',
        'locale',
        'value',
        'group'
    ];

    /**
     * Get translation by key and locale
     */
    public static function getTranslation($key, $locale, $default = null)
    {
        $cacheKey = "translation.{$locale}.{$key}";
        
        return Cache::remember($cacheKey, 3600, function() use ($key, $locale, $default) {
            $translation = self::where('key', $key)
                             ->where('locale', $locale)
                             ->first();
            
            return $translation ? $translation->value : $default;
        });
    }

    /**
     * Set translation for a key and locale
     */
    public static function setTranslation($key, $locale, $value, $group = null)
    {
        $translation = self::updateOrCreate(
            ['key' => $key, 'locale' => $locale],
            ['value' => $value, 'group' => $group]
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
            return self::where('locale', $locale)
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
        return self::distinct('locale')->pluck('locale')->toArray();
    }

    /**
     * Get all translations grouped by locale
     */
    public static function getAllTranslations()
    {
        return self::all()->groupBy('locale');
    }
}
