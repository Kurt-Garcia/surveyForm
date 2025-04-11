<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('survey_forms', function (Blueprint $table) {
            $table->id();
            $table->string('account_name');
            $table->string('account_type');
            $table->date('date');
            $table->json('responses');
            $table->integer('recommendation');
            $table->text('comments');
            $table->foreignId('survey_id')->constrained();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('survey_forms');
    }
};
