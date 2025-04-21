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
        Schema::table('survey_response_headers', function (Blueprint $table) {
            $table->boolean('allow_resubmit')->default(false)->after('comments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('survey_response_headers', function (Blueprint $table) {
            $table->dropColumn('allow_resubmit');
        });
    }
};
