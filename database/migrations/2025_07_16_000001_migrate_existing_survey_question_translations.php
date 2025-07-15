<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\SurveyQuestion;
use App\Models\TranslationHeader;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get translation headers for Tagalog and Cebuano
        $tagalogHeader = TranslationHeader::where('locale', 'tl')->first();
        $cebuanoHeader = TranslationHeader::where('locale', 'ceb')->first();
        
        if (!$tagalogHeader || !$cebuanoHeader) {
            // Create missing translation headers if they don't exist
            if (!$tagalogHeader) {
                $tagalogHeader = TranslationHeader::create([
                    'name' => 'Tagalog',
                    'locale' => 'tl',
                    'is_active' => true
                ]);
            }
            
            if (!$cebuanoHeader) {
                $cebuanoHeader = TranslationHeader::create([
                    'name' => 'Cebuano',
                    'locale' => 'ceb',
                    'is_active' => true
                ]);
            }
        }
        
        // Migrate existing data
        $questions = SurveyQuestion::whereNotNull('text_tagalog')
            ->orWhereNotNull('text_cebuano')
            ->get();
            
        foreach ($questions as $question) {
            // Migrate Tagalog translation
            if (!empty($question->text_tagalog)) {
                DB::table('survey_question_translations')->insert([
                    'survey_question_id' => $question->id,
                    'translation_header_id' => $tagalogHeader->id,
                    'text' => $question->text_tagalog,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            // Migrate Cebuano translation
            if (!empty($question->text_cebuano)) {
                DB::table('survey_question_translations')->insert([
                    'survey_question_id' => $question->id,
                    'translation_header_id' => $cebuanoHeader->id,
                    'text' => $question->text_cebuano,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear the survey_question_translations table
        DB::table('survey_question_translations')->truncate();
    }
};