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
        Schema::create('survey_forms', function (Blueprint $table) {
            $table->id();
            $table->string('accountName');
            $table->string('accountType');
            $table->date('date');
            $table->integer('Q1');  // Question 1
            $table->integer('Q2');  // Question 2
            $table->integer('Q3');  // Question 3
            $table->integer('Q4');  // Question 4
            $table->integer('Q5');  // Question 5
            $table->integer('Q6');  // Question 6
            $table->integer('surveyRating');
            $table->text('comments');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_forms');
    }
};
