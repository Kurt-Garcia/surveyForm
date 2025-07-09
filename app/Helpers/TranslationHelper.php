<?php

use App\Services\TranslationService;

if (!function_exists('__db')) {
    /**
     * Database-based translation function
     */
    function __db($key, $replacements = [], $locale = null)
    {
        $translator = app(TranslationService::class);
        return $translator->get($key, $replacements, $locale);
    }
}

if (!function_exists('trans_db')) {
    /**
     * Database-based translation function (alias)
     */
    function trans_db($key, $replacements = [], $locale = null)
    {
        return __db($key, $replacements, $locale);
    }
}

if (!function_exists('trans_choice_db')) {
    /**
     * Database-based choice translation function
     */
    function trans_choice_db($key, $number, $replacements = [], $locale = null)
    {
        $translator = app(TranslationService::class);
        $translation = $translator->get($key, $replacements, $locale);
        
        // Simple pluralization logic - can be enhanced
        if ($number == 1) {
            return $translation;
        }
        
        // Look for plural form
        $pluralKey = $key . '_plural';
        $plural = $translator->get($pluralKey, $replacements, $locale);
        
        return $plural !== $pluralKey ? $plural : $translation;
    }
}

if (!function_exists('set_translation')) {
    /**
     * Set translation in database
     */
    function set_translation($key, $value, $locale = null, $group = null)
    {
        $translator = app(TranslationService::class);
        return $translator->set($key, $value, $locale, $group);
    }
}

if (!function_exists('has_translation')) {
    /**
     * Check if translation exists
     */
    function has_translation($key, $locale = null)
    {
        $translator = app(TranslationService::class);
        return $translator->has($key, $locale);
    }
}

if (!function_exists('get_available_locales')) {
    /**
     * Get available locales
     */
    function get_available_locales()
    {
        $translator = app(TranslationService::class);
        return $translator->getAvailableLocales();
    }
}
