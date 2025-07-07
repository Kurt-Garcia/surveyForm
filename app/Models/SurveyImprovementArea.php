<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @deprecated This model is being phased out in favor of SurveyImprovementCategory and SurveyImprovementDetail.
 * It is kept for backward compatibility and will be removed in a future update.
 */
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
    
    /**
     * Get the equivalent category object from the new structure.
     */
    public function getCategory()
    {
        return SurveyImprovementCategory::where('header_id', $this->header_id)
            ->where('category_name', $this->area_category)
            ->first();
    }
    
    /**
     * Get the details for this improvement area from the new structure.
     */
    public function getDetails()
    {
        $category = $this->getCategory();
        
        if ($category) {
            return $category->details;
        }
        
        return collect();
    }
}
