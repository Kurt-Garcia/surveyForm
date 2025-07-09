<?php

namespace App\Services;

use App\Models\Translation;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class TranslationService
{
    protected $locale;
    protected $fallbackLocale;

    public function __construct()
    {
        $this->locale = App::getLocale();
        $this->fallbackLocale = config('app.fallback_locale', 'en');
    }

    /**
     * Get translation by key
     */
    public function get($key, $replacements = [], $locale = null)
    {
        $locale = $locale ?: $this->locale;
        
        // Get translation from database
        $translation = Translation::getTranslation($key, $locale);
        
        // If not found, try fallback locale
        if (!$translation && $locale !== $this->fallbackLocale) {
            $translation = Translation::getTranslation($key, $this->fallbackLocale);
        }
        
        // If still not found, return the key itself
        if (!$translation) {
            $translation = $key;
        }
        
        // Apply replacements if any
        if (!empty($replacements)) {
            foreach ($replacements as $placeholder => $value) {
                $translation = str_replace(":{$placeholder}", $value, $translation);
            }
        }
        
        return $translation;
    }

    /**
     * Set translation
     */
    public function set($key, $value, $locale = null, $group = null)
    {
        $locale = $locale ?: $this->locale;
        return Translation::setTranslation($key, $locale, $value, $group);
    }

    /**
     * Check if translation exists
     */
    public function has($key, $locale = null)
    {
        $locale = $locale ?: $this->locale;
        return Translation::getTranslation($key, $locale) !== null;
    }

    /**
     * Get all translations for current locale
     */
    public function all($locale = null)
    {
        $locale = $locale ?: $this->locale;
        return Translation::getLocaleTranslations($locale);
    }

    /**
     * Get available locales
     */
    public function getAvailableLocales()
    {
        return Translation::getAvailableLocales();
    }

    /**
     * Set locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        App::setLocale($locale);
    }

    /**
     * Get current locale
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Clear translation cache
     */
    public function clearCache()
    {
        Translation::clearCache();
    }

    /**
     * Import translations from PHP language files
     */
    public function importFromFiles()
    {
        $langPath = resource_path('lang');
        
        if (!is_dir($langPath)) {
            return false;
        }

        $locales = array_filter(scandir($langPath), function($item) use ($langPath) {
            return is_dir($langPath . '/' . $item) && !in_array($item, ['.', '..']);
        });

        foreach ($locales as $locale) {
            $localePath = $langPath . '/' . $locale;
            $files = glob($localePath . '/*.php');
            
            foreach ($files as $file) {
                $group = pathinfo($file, PATHINFO_FILENAME);
                $translations = include $file;
                
                if (is_array($translations)) {
                    $this->importTranslationArray($translations, $locale, $group);
                }
            }
        }
        
        return true;
    }

    /**
     * Import translation array recursively
     */
    private function importTranslationArray($translations, $locale, $group, $prefix = '')
    {
        foreach ($translations as $key => $value) {
            $fullKey = $prefix ? "{$prefix}.{$key}" : $key;
            
            if (is_array($value)) {
                $this->importTranslationArray($value, $locale, $group, $fullKey);
            } else {
                $this->set($fullKey, $value, $locale, $group);
            }
        }
    }
}
