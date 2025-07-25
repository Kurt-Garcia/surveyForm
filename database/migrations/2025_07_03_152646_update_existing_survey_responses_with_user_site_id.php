<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the table exists before proceeding
        if (!Schema::hasTable('survey_response_headers')) {
            return;
        }

        // Update existing survey responses to set user_site_id
        // For the specific case mentioned by the user: all responses should be associated with "MNC Cebu - main"
        
        $mncCebuSite = \App\Models\Site::where('name', 'like', '%MNC Cebu%')
            ->where('name', 'like', '%main%')
            ->first();
        
        if ($mncCebuSite) {
            // Update all existing responses to use MNC Cebu - main as the user site
            DB::table('survey_response_headers')
                ->whereNull('user_site_id')
                ->update(['user_site_id' => $mncCebuSite->id]);
        } else {
            // If MNC Cebu - main doesn't exist, try to find any site with "MNC Cebu" in the name
            $mncCebuSite = \App\Models\Site::where('name', 'like', '%MNC Cebu%')->first();
            if ($mncCebuSite) {
                DB::table('survey_response_headers')
                    ->whereNull('user_site_id')
                    ->update(['user_site_id' => $mncCebuSite->id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if the table exists before proceeding
        if (!Schema::hasTable('survey_response_headers')) {
            return;
        }

        // Set user_site_id back to null for all records
        DB::table('survey_response_headers')
            ->update(['user_site_id' => null]);
    }
};
