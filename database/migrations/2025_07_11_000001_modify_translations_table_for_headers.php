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
        // First, seed the translation_headers table with existing locales
        $existingLocales = DB::table('translation_detail')
            ->select('locale')
            ->distinct()
            ->get();

        foreach ($existingLocales as $localeData) {
            $locale = $localeData->locale;
            $name = $this->getLanguageName($locale);
            
            DB::table('translation_headers')->insertOrIgnore([
                'name' => $name,
                'locale' => $locale,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Now modify the translation_detail table
        Schema::table('translation_detail', function (Blueprint $table) {
            // Drop existing indexes that include locale
            $table->dropUnique(['key', 'locale']);
            $table->dropIndex(['locale']);
            
            // Add translation_header_id column
            $table->unsignedBigInteger('translation_header_id')->after('key');
        });

        // Update existing translations with translation_header_id
        $translations = DB::table('translation_detail')->get();
        foreach ($translations as $translation) {
            $headerId = DB::table('translation_headers')
                ->where('locale', $translation->locale)
                ->value('id');
                
            DB::table('translation_detail')
                ->where('id', $translation->id)
                ->update(['translation_header_id' => $headerId]);
        }

        // Now add constraints and remove locale column
        Schema::table('translation_detail', function (Blueprint $table) {
            // Add foreign key constraint
            $table->foreign('translation_header_id')
                  ->references('id')
                  ->on('translation_headers')
                  ->onDelete('cascade');
            
            // Drop the locale column
            $table->dropColumn('locale');
            
            // Add new unique constraint
            $table->unique(['key', 'translation_header_id']);
            
            // Add index for performance
            $table->index('translation_header_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('translation_detail', function (Blueprint $table) {
            // Drop new constraints and indexes
            $table->dropForeign(['translation_header_id']);
            $table->dropUnique(['key', 'translation_header_id']);
            $table->dropIndex(['translation_header_id']);
            
            // Add back locale column
            $table->string('locale', 5)->after('key');
        });

        // Restore locale data from translation_headers
        $translations = DB::table('translation_detail')
            ->join('translation_headers', 'translation_detail.translation_header_id', '=', 'translation_headers.id')
            ->select('translation_detail.id', 'translation_headers.locale')
            ->get();

        foreach ($translations as $translation) {
            DB::table('translation_detail')
                ->where('id', $translation->id)
                ->update(['locale' => $translation->locale]);
        }

        Schema::table('translation_detail', function (Blueprint $table) {
            // Drop translation_header_id column
            $table->dropColumn('translation_header_id');
            
            // Restore original constraints
            $table->unique(['key', 'locale']);
            $table->index('locale');
        });
    }

    /**
     * Get language name from locale code
     */
    private function getLanguageName($locale)
    {
        $languageMap = [
            'en' => 'English',
            'tl' => 'Tagalog',
            'ceb' => 'Cebuano'
        ];

        return $languageMap[$locale] ?? ucfirst($locale);
    }
};