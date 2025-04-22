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
        Schema::table('surveys', function (Blueprint $table) {
            $table->integer('total_questions')->nullable()->after('is_active');
        });

        // Update existing surveys with their question counts
        $surveys = DB::table('surveys')->get();
        foreach ($surveys as $survey) {
            $questionCount = DB::table('survey_questions')
                ->where('survey_id', $survey->id)
                ->count();
            
            DB::table('surveys')
                ->where('id', $survey->id)
                ->update(['total_questions' => $questionCount]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->dropColumn('total_questions');
        });
    }
};
