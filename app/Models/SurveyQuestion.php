<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyQuestion extends Model
{
    protected $fillable = [
        'text',
        'type',
        'survey_id',
        'description',
        'required'
    ];

    protected $casts = [
        'required' => 'boolean'
    ];

    protected static function booted()
    {
        static::created(function ($question) {
            $question->survey->updateQuestionCount();
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
}