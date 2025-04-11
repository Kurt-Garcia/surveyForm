<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyResponse extends Model
{
    protected $table = 'survey_forms';
    
    protected $fillable = [
        'account_name',
        'account_type',
        'date',
        'responses',
        'recommendation',
        'comments',
        'survey_id'
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