<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('surveys')) {
            Schema::create('surveys', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->foreignId('admin_id')->constrained('admin_users')->onDelete('cascade');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('survey_questions')) {
            Schema::create('survey_questions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('survey_id')->constrained('surveys')->onDelete('cascade');
                $table->string('text');
                $table->string('type');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('survey_questions');
        Schema::dropIfExists('surveys');
    }
};