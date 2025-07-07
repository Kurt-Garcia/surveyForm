<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveyImprovementCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey_improvement_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('header_id')->constrained('survey_response_headers')->onDelete('cascade');
            $table->string('category_name');
            $table->boolean('is_other')->default(false);
            $table->text('other_comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('survey_improvement_categories');
    }
}
