<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */    public function up(): void
    {
        // Create the pivot table for admin-SBU many-to-many relationship only if it doesn't exist
        if (!Schema::hasTable('admin_sbu')) {
            Schema::create('admin_sbu', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('admin_id');
                $table->unsignedBigInteger('sbu_id');
                $table->timestamps();

                // Foreign key constraints
                $table->foreign('admin_id')->references('id')->on('admin_users')->onDelete('cascade');
                $table->foreign('sbu_id')->references('id')->on('sbus')->onDelete('cascade');

                // Prevent duplicate entries
                $table->unique(['admin_id', 'sbu_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_sbu');
    }
};
