<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CreateImprovementTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'survey:create-improvement-tables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the survey improvement tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating survey improvement tables...');

        try {
            // Create survey_improvement_categories table if it doesn't exist
            if (!Schema::hasTable('survey_improvement_categories')) {
                Schema::create('survey_improvement_categories', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('header_id')->constrained('survey_response_headers')->onDelete('cascade');
                    $table->string('category_name');
                    $table->boolean('is_other')->default(false);
                    $table->text('other_comments')->nullable();
                    $table->timestamps();
                });
                $this->info('Created survey_improvement_categories table');
            } else {
                $this->info('survey_improvement_categories table already exists');
            }

            // Create survey_improvement_details table if it doesn't exist
            if (!Schema::hasTable('survey_improvement_details')) {
                Schema::create('survey_improvement_details', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('category_id')->constrained('survey_improvement_categories')->onDelete('cascade');
                    $table->text('detail_text');
                    $table->timestamps();
                });
                $this->info('Created survey_improvement_details table');
            } else {
                $this->info('survey_improvement_details table already exists');
            }

            // Create survey_improvement_areas table if it doesn't exist (for backward compatibility)
            if (!Schema::hasTable('survey_improvement_areas')) {
                Schema::create('survey_improvement_areas', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('header_id')->constrained('survey_response_headers')->onDelete('cascade');
                    $table->string('area_category');
                    $table->text('area_details')->nullable();
                    $table->boolean('is_other')->default(false);
                    $table->text('other_comments')->nullable();
                    $table->timestamps();
                });
                $this->info('Created survey_improvement_areas table');
            } else {
                $this->info('survey_improvement_areas table already exists');
            }

            $this->info('All tables created successfully');
            return 0;
        } catch (\Exception $e) {
            $this->error('Error creating tables: ' . $e->getMessage());
            return 1;
        }
    }
}
