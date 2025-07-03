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
            $table->foreignId('user_site_id')->nullable()->after('admin_id')->constrained('sites')->onDelete('set null');
            $table->index('user_site_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('survey_response_headers', function (Blueprint $table) {
            $table->dropForeign(['user_site_id']);
            $table->dropIndex(['user_site_id']);
            $table->dropColumn('user_site_id');
        });
    }
};
