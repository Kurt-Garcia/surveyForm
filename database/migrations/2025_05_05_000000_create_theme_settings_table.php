<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('theme_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(false);
            $table->string('primary_color')->default('#0093E9');
            $table->string('secondary_color')->default('#80D0C7');
            $table->string('accent_color')->default('#FF5E62');
            $table->string('background_color')->default('#f8f9fa');
            $table->string('text_color')->default('#333333');
            $table->string('heading_font')->default('Inter');
            $table->string('body_font')->default('Inter');
            $table->timestamps();
        });

        // Insert default theme
        DB::table('theme_settings')->insert([
            'name' => 'Default',
            'is_active' => true,
            'primary_color' => '#0078BD',
            'secondary_color' => '#005799',
            'accent_color' => '#0091ff', 
            'background_color' => '#f8f9fa',
            'text_color' => '#333333',
            'heading_font' => 'Inter',
            'body_font' => 'Inter',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('theme_settings');
    }
};