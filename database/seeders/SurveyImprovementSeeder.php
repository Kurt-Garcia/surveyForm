<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SurveyResponseHeader;
use App\Models\SurveyImprovementCategory;
use App\Models\SurveyImprovementDetail;
use Illuminate\Support\Facades\DB;

class SurveyImprovementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Sample improvement categories and details
        $categories = [
            'product_quality' => [
                'We hope products are always available. Some items are often out of stock.',
                'Please monitor product expiration dates more carefully. We sometimes receive items that are near expiry.',
                'Some products arrive with dents, leaks, or damaged packaging. Kindly ensure all items are in good condition.'
            ],
            'delivery_logistics' => [
                'We\'d appreciate it if deliveries consistently arrive on time, as promised.',
                'There have been a few instances of missing items in our deliveries. Please double-check orders for completeness.'
            ],
            'customer_service' => [
                'It would be helpful if our concerns or follow-ups were responded to more quickly.',
                'We appreciate clear communication. Kindly ensure that all interactions remain polite and professional.'
            ],
            'timeliness' => [
                'Please try to follow the agreed delivery or visit schedule to avoid disruptions in our store operations.'
            ],
            'returns_handling' => [
                'I hope the return process can be made quicker and more convenient.',
                'Please improve coordination when it comes to picking up bad order items.'
            ],
            'others' => []
        ];
        
        // Get all response headers (or a subset for testing)
        $headers = SurveyResponseHeader::take(20)->get();
        
        foreach ($headers as $header) {
            // Select 1-3 random categories for each header
            $numCategories = rand(1, 3);
            $selectedCategories = array_keys($categories);
            shuffle($selectedCategories);
            $selectedCategories = array_slice($selectedCategories, 0, $numCategories);
            
            foreach ($selectedCategories as $categoryName) {
                $isOther = ($categoryName === 'others');
                $otherComments = $isOther ? 'Sample other comments: Please improve your website.' : null;
                
                // Create category
                $category = SurveyImprovementCategory::create([
                    'header_id' => $header->id,
                    'category_name' => $categoryName,
                    'is_other' => $isOther,
                    'other_comments' => $otherComments
                ]);
                
                // For non-other categories, add some details
                if (!$isOther && isset($categories[$categoryName])) {
                    $detailsSet = $categories[$categoryName];
                    // Add 1-2 random details
                    $numDetails = min(count($detailsSet), rand(1, 2));
                    shuffle($detailsSet);
                    
                    for ($i = 0; $i < $numDetails; $i++) {
                        SurveyImprovementDetail::create([
                            'category_id' => $category->id,
                            'detail_text' => $detailsSet[$i]
                        ]);
                    }
                }
            }
        }
    }
}
