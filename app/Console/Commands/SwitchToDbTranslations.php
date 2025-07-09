<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SwitchToDbTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:switch-to-db {--revert : Revert back to file-based translations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Switch views to use database-driven translations instead of file-based ones';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('revert')) {
            $this->revertToFileTranslations();
        } else {
            $this->switchToDbTranslations();
        }
    }

    /**
     * Switch to database translations
     */
    private function switchToDbTranslations()
    {
        $this->info('Switching to database-driven translations...');
        
        $viewsPath = resource_path('views');
        $files = File::allFiles($viewsPath);
        
        $replacements = [
            "__('survey." => "__db('",
            "__('auth." => "__db('",
            "__('validation." => "__db('",
            "__('messages." => "__db('",
        ];
        
        $fileCount = 0;
        $totalReplacements = 0;
        
        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $content = File::get($file->getRealPath());
                $originalContent = $content;
                
                foreach ($replacements as $from => $to) {
                    $count = substr_count($content, $from);
                    $content = str_replace($from, $to, $content);
                    $totalReplacements += $count;
                }
                
                if ($content !== $originalContent) {
                    File::put($file->getRealPath(), $content);
                    $fileCount++;
                }
            }
        }
        
        $this->info("Successfully updated {$fileCount} files with {$totalReplacements} replacements.");
        $this->info('Views now use database-driven translations!');
    }

    /**
     * Revert to file-based translations
     */
    private function revertToFileTranslations()
    {
        $this->info('Reverting to file-based translations...');
        
        $viewsPath = resource_path('views');
        $files = File::allFiles($viewsPath);
        
        $replacements = [
            "__db('account_name'" => "__('survey.account_name'",
            "__db('account_type'" => "__('survey.account_type'",
            "__db('date'" => "__('survey.date'",
            "__db('satisfaction_level'" => "__('survey.satisfaction_level'",
            "__db('required'" => "__('survey.required'",
            "__db('optional'" => "__('survey.optional'",
            "__db('recommendation'" => "__('survey.recommendation'",
            "__db('recommendation_question'" => "__('survey.recommendation_question'",
            "__db('select_rating'" => "__('survey.select_rating'",
            "__db('improvement_areas'" => "__('survey.improvement_areas'",
            "__db('select_all_apply'" => "__('survey.select_all_apply'",
            "__db('product_quality'" => "__('survey.product_quality'",
            "__db('delivery_logistics'" => "__('survey.delivery_logistics'",
            "__db('customer_service'" => "__('survey.customer_service'",
            "__db('timeliness'" => "__('survey.timeliness'",
            "__db('returns_handling'" => "__('survey.returns_handling'",
            "__db('others'" => "__('survey.others'",
            "__db('others_placeholder'" => "__('survey.others_placeholder'",
            "__db('submit_survey'" => "__('survey.submit_survey'",
            "__db('thank_you'" => "__('survey.thank_you'",
            "__db('thank_you_message'" => "__('survey.thank_you_message'",
            "__db('feedback_helps'" => "__('survey.feedback_helps'",
            "__db('view_response'" => "__('survey.view_response'",
        ];
        
        $fileCount = 0;
        $totalReplacements = 0;
        
        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $content = File::get($file->getRealPath());
                $originalContent = $content;
                
                foreach ($replacements as $from => $to) {
                    $count = substr_count($content, $from);
                    $content = str_replace($from, $to, $content);
                    $totalReplacements += $count;
                }
                
                if ($content !== $originalContent) {
                    File::put($file->getRealPath(), $content);
                    $fileCount++;
                }
            }
        }
        
        $this->info("Successfully reverted {$fileCount} files with {$totalReplacements} replacements.");
        $this->info('Views now use file-based translations!');
    }
}
