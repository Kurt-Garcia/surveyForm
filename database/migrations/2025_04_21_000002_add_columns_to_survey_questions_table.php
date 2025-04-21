<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('survey_questions', function (Blueprint $table) {
            $table->text('description')->nullable()->after('type');
            $table->boolean('required')->default(false)->after('description');
            $table->json('options')->nullable()->after('required');
            $table->integer('order')->nullable()->after('options');
        });
    }

    public function down()
    {
        Schema::table('survey_questions', function (Blueprint $table) {
            $table->dropColumn(['description', 'required', 'options', 'order']);
        });
    }
};