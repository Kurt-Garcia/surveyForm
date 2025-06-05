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
        // Create the pivot table for admin-site many-to-many relationship only if it doesn't exist
        if (!Schema::hasTable('admin_site_pivot')) {
            Schema::create('admin_site_pivot', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('admin_id');
                $table->unsignedBigInteger('site_id');
                $table->timestamps();

                // Foreign key constraints
                $table->foreign('admin_id')->references('id')->on('admin_users')->onDelete('cascade');
                $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');

                // Unique constraint to prevent duplicate admin-site associations
                $table->unique(['admin_id', 'site_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_site_pivot');
    }
};
