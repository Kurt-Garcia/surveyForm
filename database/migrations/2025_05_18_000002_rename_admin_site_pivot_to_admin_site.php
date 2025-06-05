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
        // Rename the admin_site_pivot table to admin_site only if admin_site_pivot exists and admin_site doesn't
        if (Schema::hasTable('admin_site_pivot') && !Schema::hasTable('admin_site')) {
            Schema::rename('admin_site_pivot', 'admin_site');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename it back to admin_site_pivot
        Schema::rename('admin_site', 'admin_site_pivot');
    }
};
