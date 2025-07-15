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
        Schema::create('translation_detail', function (Blueprint $table) {
            $table->id();
            $table->string('key'); // Translation key (e.g., 'survey.account_name')
            $table->string('locale', 5); // Language code (e.g., 'en', 'tl', 'ceb')
            $table->text('value'); // Translation value
            $table->string('group')->nullable(); // Group/namespace (e.g., 'survey', 'auth')
            $table->timestamps();
            
            // Make sure key + locale combination is unique
            $table->unique(['key', 'locale']);
            
            // Add indexes for performance
            $table->index(['group', 'locale']);
            $table->index('locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translation_detail');
    }
};
