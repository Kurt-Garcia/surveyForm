<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveyImprovementAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey_improvement_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('header_id')->constrained('survey_response_headers')->onDelete('cascade');
            $table->string('area_category');
            $table->text('area_details')->nullable();
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
        Schema::dropIfExists('survey_improvement_areas');
    }
}
