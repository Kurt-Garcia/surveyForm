<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('survey_response_headers', function (Blueprint $table) {
            $table->timestamp('start_time')->nullable()->after('date');
            $table->timestamp('end_time')->nullable()->after('start_time');
        });
    }

    public function down()
    {
        Schema::table('survey_response_headers', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'end_time']);
        });
    }
};