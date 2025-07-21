<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class SurveyQuestion extends Model
{
    protected $fillable = [
        'text',
        'type',
        'survey_id',
        'required'
    ];

    protected $casts = [
        'required' => 'boolean'
    ];

    protected static function booted()
    {
        static::created(function ($question) {
            // Check if we're in a database transaction (likely during survey creation)
            // If so, update question count silently to avoid activity logging
            $inTransaction = DB::transactionLevel() > 0;
            $question->survey->updateQuestionCount($inTransaction);
        });

        static::deleted(function ($question) {
            $question->survey->updateQuestionCount();
        });
    }

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(SurveyResponseDetail::class, 'question_id');
    }

    /**
     * Get translations for this question
     */
    public function translations(): HasMany
    {
        return $this->hasMany(SurveyQuestionTranslation::class);
    }

    /**
     * Get question text based on language locale
     */
    public function getTextByLanguage($locale = 'en')
    {
        // Return English text for default or 'en' locale
        if ($locale === 'en' || $locale === 'english') {
            return $this->text;
        }

        // Look for translation in the specified locale
        $translation = $this->translations()
            ->whereHas('translationHeader', function ($query) use ($locale) {
                $query->where('locale', $locale)->where('is_active', true);
            })
            ->first();

        return $translation ? $translation->text : $this->text;
    }

    /**
     * Get all translations with their locales
     */
    public function getTranslationsWithLocales()
    {
        return $this->translations()->with('translationHeader')->get()->mapWithKeys(function ($translation) {
            return [$translation->translationHeader->locale => $translation->text];
        });
    }

    /**
     * Set translation for a specific locale
     */
    public function setTranslation($locale, $text)
    {
        $translationHeader = TranslationHeader::where('locale', $locale)->first();
        
        if (!$translationHeader) {
            throw new \Exception("Translation header for locale '{$locale}' not found.");
        }

        return $this->translations()->updateOrCreate(
            ['translation_header_id' => $translationHeader->id],
            ['text' => $text]
        );
    }

    /**
     * Legacy method for backward compatibility
     */
    public function getTextTagalogAttribute()
    {
        return $this->getTextByLanguage('tl');
    }

    /**
     * Legacy method for backward compatibility
     */
    public function getTextCebuanoAttribute()
    {
        return $this->getTextByLanguage('ceb');
    }
}