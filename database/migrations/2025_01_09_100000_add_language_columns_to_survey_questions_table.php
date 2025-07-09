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
        Schema::table('survey_questions', function (Blueprint $table) {
            // Add language columns - text will be the default English text
            $table->text('text_tagalog')->nullable()->after('text');
            $table->text('text_cebuano')->nullable()->after('text_tagalog');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('survey_questions', function (Blueprint $table) {
            $table->dropColumn(['text_tagalog', 'text_cebuano']);
        });
    }
};
