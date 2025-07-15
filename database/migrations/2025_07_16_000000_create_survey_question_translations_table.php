<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('survey_question_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_question_id')->constrained('survey_questions')->onDelete('cascade');
            $table->foreignId('translation_header_id')->constrained('translation_headers')->onDelete('cascade');
            $table->text('text');
            $table->timestamps();
            
            // Ensure unique combination of question and language
            $table->unique(['survey_question_id', 'translation_header_id'], 'unique_question_translation');
            
            // Add indexes for performance
            $table->index('survey_question_id');
            $table->index('translation_header_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_question_translations');
    }
};