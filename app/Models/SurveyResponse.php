<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyResponse extends Model
{
    protected $table = 'survey_responses';
    
    protected $fillable = [
        'account_name',
        'account_type',
        'date',
        'survey_id',
        'question_id',
        'response',
        'recommendation',
        'comments'
    ];

    protected $casts = [
        'responses' => 'array',
        'date' => 'date'
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }
}