<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyImprovementArea extends Model
{
    protected $table = 'survey_improvement_areas';
    
    protected $fillable = [
        'header_id',
        'area_category',
        'area_details',
        'is_other',
        'other_comments'
    ];

    protected $casts = [
        'is_other' => 'boolean',
    ];

    public function header(): BelongsTo
    {
        return $this->belongsTo(SurveyResponseHeader::class, 'header_id');
    }
}
