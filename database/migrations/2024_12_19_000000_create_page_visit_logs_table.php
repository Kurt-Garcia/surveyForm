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
        Schema::create('page_visit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_type'); // 'admin', 'user', 'developer'
            $table->unsignedBigInteger('user_id');
            $table->string('user_name');
            $table->string('user_email');
            $table->string('page_url');
            $table->string('page_title')->nullable();
            $table->string('route_name')->nullable();
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            $table->integer('duration_seconds')->nullable(); // Duration in seconds
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('session_id');
            $table->json('additional_data')->nullable(); // For storing extra metadata
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['user_type', 'user_id']);
            $table->index('start_time');
            $table->index('route_name');
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_visit_logs');
    }
};