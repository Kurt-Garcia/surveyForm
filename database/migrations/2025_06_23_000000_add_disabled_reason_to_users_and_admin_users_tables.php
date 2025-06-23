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
        Schema::table('users', function (Blueprint $table) {
            $table->text('disabled_reason')->nullable()->after('status');
        });

        Schema::table('admin_users', function (Blueprint $table) {
            $table->text('disabled_reason')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('disabled_reason');
        });

        Schema::table('admin_users', function (Blueprint $table) {
            $table->dropColumn('disabled_reason');
        });
    }
};
