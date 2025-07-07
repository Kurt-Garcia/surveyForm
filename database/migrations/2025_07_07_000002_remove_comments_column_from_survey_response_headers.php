<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveCommentsColumnFromSurveyResponseHeaders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('survey_response_headers', function (Blueprint $table) {
            $table->dropColumn('comments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('survey_response_headers', function (Blueprint $table) {
            $table->text('comments')->nullable()->after('recommendation');
        });
    }
}
