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
        // First, remove the sbu_id column from surveys table if it exists
        if (Schema::hasColumn('surveys', 'sbu_id')) {
            Schema::table('surveys', function (Blueprint $table) {
                $table->dropForeign(['sbu_id']);
                $table->dropColumn('sbu_id');
            });
        }

        // Create the pivot table for many-to-many relationship
        Schema::create('survey_sbu', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('survey_id');
            $table->unsignedBigInteger('sbu_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('survey_id')->references('id')->on('surveys')->onDelete('cascade');
            $table->foreign('sbu_id')->references('id')->on('sbus')->onDelete('cascade');

            // Prevent duplicate entries
            $table->unique(['survey_id', 'sbu_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_sbu');
        
        // Restore the old sbu_id column
        Schema::table('surveys', function (Blueprint $table) {
            $table->unsignedBigInteger('sbu_id')->nullable()->after('logo');
            $table->foreign('sbu_id')->references('id')->on('sbus')->onDelete('set null');
        });
    }
};
