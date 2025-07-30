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
        Schema::table('theme_settings', function (Blueprint $table) {
            // Drop the existing unique constraint on name
            $table->dropUnique(['name']);
            
            // Add a composite unique constraint on name and admin_id
            // This allows different admins to have themes with the same name
            $table->unique(['name', 'admin_id'], 'theme_settings_name_admin_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('theme_settings', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('theme_settings_name_admin_unique');
            
            // Restore the original unique constraint on name only
            $table->unique('name');
        });
    }
};