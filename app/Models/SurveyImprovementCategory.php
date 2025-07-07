<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurveyImprovementCategory extends Model
{
    protected $table = 'survey_improvement_categories';
    
    protected $fillable = [
        'header_id',
        'category_name',
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
    
    public function details(): HasMany
    {
        return $this->hasMany(SurveyImprovementDetail::class, 'category_id');
    }
}
