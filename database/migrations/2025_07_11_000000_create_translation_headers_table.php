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
        Schema::create('translation_headers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Language name (e.g., 'English', 'Cebuano', 'Tagalog')
            $table->string('locale', 5)->unique(); // Language code (e.g., 'en', 'ceb', 'tl')
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Add index for performance
            $table->index('locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translation_headers');
    }
};