<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MigrateSurveyImprovementData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Get all existing improvement areas
        $oldData = DB::table('survey_improvement_areas')->get();
        
        foreach ($oldData as $row) {
            // Create a category record
            $categoryId = DB::table('survey_improvement_categories')->insertGetId([
                'header_id' => $row->header_id,
                'category_name' => $row->area_category,
                'is_other' => $row->is_other,
                'other_comments' => $row->other_comments,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at
            ]);
            
            // If there are area details, split and insert them
            if (!empty($row->area_details)) {
                $details = explode("\n", $row->area_details);
                
                foreach ($details as $detail) {
                    if (trim($detail) !== '') {
                        DB::table('survey_improvement_details')->insert([
                            'category_id' => $categoryId,
                            'detail_text' => trim($detail),
                            'created_at' => $row->created_at,
                            'updated_at' => $row->updated_at
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // The original table should still exist, so no specific down action is needed
    }
}
