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
        
        // The old table 'survey_improvement_areas' has been dropped, so we no longer save data to it
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
        
        // Define explicit mapping of detail IDs to categories - this is more reliable
        $detailToCategory = [
            // Product Quality details
            'product_availability' => 'product_quality',
            'product_expiration' => 'product_quality',
            'product_damage' => 'product_quality',
            
            // Delivery & Logistics details
            'delivery_time' => 'delivery_logistics',
            'missing_items' => 'delivery_logistics',
            
            // Customer Service details
            'response_time' => 'customer_service',
            'clear_communication' => 'customer_service',
            
            // Timeliness details
            'schedule_adherence' => 'timeliness',
            
            // Returns/BO Handling details
            'return_process' => 'returns_handling',
            'bo_coordination' => 'returns_handling'
        ];
        
        foreach ($details as $detail) {
            $category = null;
            
            // First, try to extract the category from the data-category attribute
            if (preg_match('/data-category="([^"]+)"/', $detail, $matches)) {
                $category = $matches[1];
            }
            
            // If that didn't work, try to extract from the ID
            if (!$category) {
                foreach ($detailToCategory as $detailId => $categoryValue) {
                    // Look for the detail ID exactly in the string
                    if (preg_match('/\bid="' . preg_quote($detailId, '/') . '"/', $detail) || 
                        preg_match('/\b' . preg_quote($detailId, '/') . '\b/', $detail)) {
                        $category = $categoryValue;
                        break;
                    }
                }
            }
            
            // If we still couldn't determine the category, use a fallback approach
            // with stricter matching to avoid misattribution
            if (!$category) {
                // Try to determine category from the detail text content
                if (preg_match('/products|availability|stock|expiration|damaged|packaging|dents|leaks/i', $detail)) {
                    $category = 'product_quality';
                } elseif (preg_match('/deliveries|arrive on time|missing items|double-check orders/i', $detail)) {
                    $category = 'delivery_logistics';
                } elseif (preg_match('/concerns|follow-ups|responded|communication|interactions|polite|professional/i', $detail)) {
                    $category = 'customer_service';
                } elseif (preg_match('/schedule|disruptions|store operations|follow.*agreed/i', $detail)) {
                    $category = 'timeliness';
                } elseif (preg_match('/return process|quicker|convenient|coordination|bad order|picking up/i', $detail)) {
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
