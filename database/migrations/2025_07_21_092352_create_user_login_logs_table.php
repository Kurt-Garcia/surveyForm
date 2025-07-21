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
        Schema::create('user_login_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_type'); // 'admin', 'user', 'developer'
            $table->unsignedBigInteger('user_id');
            $table->string('user_name');
            $table->string('user_email');
            $table->string('action'); // 'login', 'logout'
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('action_time');
            $table->timestamps();
            
            $table->index(['user_type', 'user_id']);
            $table->index('action_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_login_logs');
    }
};
