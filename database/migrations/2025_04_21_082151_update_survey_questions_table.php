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
            if (Schema::hasColumn('survey_questions', 'required')) {
                $table->dropColumn('required');
            }
            if (Schema::hasColumn('survey_questions', 'options')) {
                $table->dropColumn('options');
            }
            if (Schema::hasColumn('survey_questions', 'order')) {
                $table->dropColumn('order');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('survey_questions', function (Blueprint $table) {
            if (!Schema::hasColumn('survey_questions', 'required')) {
                $table->boolean('required')->default(false);
            }
            if (!Schema::hasColumn('survey_questions', 'options')) {
                $table->json('options')->nullable();
            }
            if (!Schema::hasColumn('survey_questions', 'order')) {
                $table->integer('order')->nullable();
            }
        });
    }
};
