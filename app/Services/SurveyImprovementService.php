<?php

namespace App\Services;

use App\Models\SurveyImprovementCategory;
use App\Models\SurveyImprovementDetail;

class SurveyImprovementService
{
    /**
     * Create improvement category and details
     * 
     * @param int $headerId The survey response header ID
     * @param string $areaCategory The category name
     * @param array|null $details The details for this category
     * @param bool $isOther Whether this is the "other" category
     * @param string|null $otherComments Comments for the "other" category
     * @return void
     */
    public static function createImprovementAreaWithDetails(
        int $headerId, 
        string $areaCategory, 
        ?array $details = null, 
        bool $isOther = false, 
        ?string $otherComments = null
    ): void {
        // Create the new category record
        $category = SurveyImprovementCategory::create([
            'header_id' => $headerId,
            'category_name' => $areaCategory,
            'is_other' => $isOther,
            'other_comments' => $otherComments
        ]);
        
        // Create detail records
        if ($details) {
            foreach ($details as $detail) {
                SurveyImprovementDetail::create([
                    'category_id' => $category->id,
                    'detail_text' => $detail
                ]);
            }
        }
    }
}
