<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveyImprovementCategoriesAndDetailsTablesComplete extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Only create tables if they don't exist yet
        if (!Schema::hasTable('survey_improvement_categories')) {
            Schema::create('survey_improvement_categories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('header_id')->constrained('survey_response_headers')->onDelete('cascade');
                $table->string('category_name');
                $table->boolean('is_other')->default(false);
                $table->text('other_comments')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('survey_improvement_details')) {
            Schema::create('survey_improvement_details', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained('survey_improvement_categories')->onDelete('cascade');
                $table->text('detail_text');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('survey_improvement_details');
        Schema::dropIfExists('survey_improvement_categories');
    }
}
