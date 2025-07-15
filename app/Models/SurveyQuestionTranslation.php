<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyQuestionTranslation extends Model
{
    protected $fillable = [
        'survey_question_id',
        'translation_header_id',
        'text'
    ];

    /**
     * Get the survey question that owns this translation
     */
    public function surveyQuestion(): BelongsTo
    {
        return $this->belongsTo(SurveyQuestion::class);
    }

    /**
     * Get the translation header for this translation
     */
    public function translationHeader(): BelongsTo
    {
        return $this->belongsTo(TranslationHeader::class);
    }

    /**
     * Get translation by question and locale
     */
    public static function getByQuestionAndLocale($questionId, $locale)
    {
        return self::whereHas('translationHeader', function ($query) use ($locale) {
            $query->where('locale', $locale);
        })
        ->where('survey_question_id', $questionId)
        ->first();
    }
}