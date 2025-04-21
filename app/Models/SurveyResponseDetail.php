<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyResponseDetail extends Model
{
    protected $table = 'survey_response_details';
    
    protected $fillable = [
        'header_id',
        'question_id',
        'response'
    ];

    public function header(): BelongsTo
    {
        return $this->belongsTo(SurveyResponseHeader::class, 'header_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(SurveyQuestion::class);
    }
}