<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TranslationService;
use App\Models\Translation;

class ImportTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:import {--clear : Clear existing translations before import}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import translations from language files to database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting translation import...');
        
        // Clear existing translations if requested
        if ($this->option('clear')) {
            $this->info('Clearing existing translations...');
            Translation::truncate();
        }
        
        $translationService = app(TranslationService::class);
        
        // Import translations from files
        $success = $translationService->importFromFiles();
        
        if ($success) {
            $count = Translation::count();
            $this->info("Successfully imported {$count} translations!");
            
            // Show summary
            $locales = Translation::getAvailableLocales();
            $this->info('Available locales: ' . implode(', ', $locales));
            
            foreach ($locales as $locale) {
                $localeCount = Translation::where('locale', $locale)->count();
                $this->line("  {$locale}: {$localeCount} translations");
            }
        } else {
            $this->error('Failed to import translations. Language directory not found.');
        }
        
        // Clear cache
        $translationService->clearCache();
        $this->info('Translation cache cleared.');
    }
}
