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
        // Create the pivot table for user-SBU many-to-many relationship only if it doesn't exist
        if (!Schema::hasTable('user_sbu')) {
            Schema::create('user_sbu', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('sbu_id');
                $table->timestamps();

                // Foreign key constraints
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('sbu_id')->references('id')->on('sbus')->onDelete('cascade');

                // Prevent duplicate entries
                $table->unique(['user_id', 'sbu_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sbu');
    }
};
