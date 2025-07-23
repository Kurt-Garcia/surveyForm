<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SurveyQuestion extends Model
{
    use LogsActivity;
    
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
            // Always update question count silently to avoid duplicate activity logging
            // since the SurveyQuestion creation is already logged
            $question->survey->updateQuestionCount(true);
        });

        static::deleted(function ($question) {
            // Always update question count silently to avoid duplicate activity logging
            // since the SurveyQuestion deletion is already logged
            $question->survey->updateQuestionCount(true);
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

    /**
     * Get the question number/position within the survey
     */
    public function getQuestionNumber()
    {
        $questions = $this->survey->questions()->orderBy('id')->get();
        $position = 1;
        foreach ($questions as $question) {
            if ($question->id === $this->id) {
                return $position;
            }
            $position++;
        }
        return $position;
    }

    /**
     * Configure activity logging options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['text', 'type', 'required'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(function (string $eventName) {
                if ($eventName === 'created') {
                    return "Question Added to {$this->survey->title}";
                } elseif ($eventName === 'deleted') {
                    $questionNumber = $this->getQuestionNumber();
                    return "Question {$questionNumber} has been deleted in {$this->survey->title}";
                } elseif ($eventName === 'updated') {
                    $questionNumber = $this->getQuestionNumber();
                    return "Question {$questionNumber} has been updated in {$this->survey->title}";
                } else {
                    return "Question {$eventName}";
                }
            });
    }
}