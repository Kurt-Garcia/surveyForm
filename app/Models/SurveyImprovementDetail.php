<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyImprovementDetail extends Model
{
    protected $table = 'survey_improvement_details';
    
    protected $fillable = [
        'category_id',
        'detail_text'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(SurveyImprovementCategory::class, 'category_id');
    }
}
