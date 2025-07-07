<?php

namespace App\Services;

use App\Models\SurveyImprovementCategory;
use App\Models\SurveyImprovementDetail;
use App\Models\SurveyImprovementArea;

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
        
        // For backward compatibility, also create a record in the old table
        SurveyImprovementArea::create([
            'header_id' => $headerId,
            'area_category' => $areaCategory,
            'area_details' => $details ? implode('; ', $details) : null,
            'is_other' => $isOther,
            'other_comments' => $otherComments
        ]);
    }
    
    /**
     * Helper method to map improvement details to their respective categories
     * 
     * @param array $details Array of improvement detail texts
     * @return array Associative array mapping category names to arrays of detail texts
     */
    public static function mapDetailsToCategories(array $details): array
    {
        $detailsByCategory = [];
        
        // Define mapping of detail IDs to categories
        $detailToCategory = [
            'product_availability' => 'product_quality',
            'product_expiration' => 'product_quality',
            'product_damage' => 'product_quality',
            'delivery_time' => 'delivery_logistics',
            'missing_items' => 'delivery_logistics',
            'response_time' => 'customer_service',
            'clear_communication' => 'customer_service',
            'schedule_adherence' => 'timeliness',
            'return_process' => 'returns_handling',
            'bo_coordination' => 'returns_handling'
        ];
        
        foreach ($details as $detail) {
            $category = null;
            
            // First try to match by ID
            foreach ($detailToCategory as $detailId => $categoryValue) {
                if (strpos($detail, $detailId) !== false) {
                    $category = $categoryValue;
                    break;
                }
            }
            
            // If no match by ID, try content-based matching
            if (!$category) {
                if (strpos($detail, 'product') !== false || strpos($detail, 'expiration') !== false || strpos($detail, 'stock') !== false) {
                    $category = 'product_quality';
                } elseif (strpos($detail, 'delivery') !== false || strpos($detail, 'missing') !== false) {
                    $category = 'delivery_logistics';
                } elseif (strpos($detail, 'service') !== false || strpos($detail, 'sales') !== false || 
                        strpos($detail, 'communication') !== false || strpos($detail, 'concern') !== false || 
                        strpos($detail, 'respond') !== false) {
                    $category = 'customer_service';
                } elseif (strpos($detail, 'time') !== false || strpos($detail, 'schedule') !== false) {
                    $category = 'timeliness';
                } elseif (strpos($detail, 'return') !== false || strpos($detail, 'BO') !== false || 
                        strpos($detail, 'coordination') !== false || strpos($detail, 'pick') !== false) {
                    $category = 'returns_handling';
                }
            }
            
            if ($category) {
                if (!isset($detailsByCategory[$category])) {
                    $detailsByCategory[$category] = [];
                }
                $detailsByCategory[$category][] = $detail;
            }
        }
        
        return $detailsByCategory;
    }
}
