<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Create survey_response_headers table
        Schema::create('survey_response_headers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('surveys')->onDelete('cascade');
            $table->foreignId('admin_id')->nullable()->constrained('admin_users')->onDelete('set null');
            $table->string('account_name', 100);
            $table->string('account_type', 50);
            $table->date('date');
            $table->tinyInteger('recommendation')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();

            // Add indexes for performance
            $table->index('account_name');
            $table->index('account_type');
            $table->index('date');
            $table->index(['survey_id', 'date']);
            $table->index(['account_name', 'account_type']);
        });

        // Create survey_response_details table
        Schema::create('survey_response_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('header_id')->constrained('survey_response_headers')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('survey_questions')->onDelete('cascade');
            $table->string('response', 500);
            $table->timestamps();

            // Add index for performance
            $table->index('question_id');
        });

        // Migrate existing data
        DB::statement('
            INSERT INTO survey_response_headers (
                survey_id, admin_id, account_name, account_type, date, 
                recommendation, comments, created_at, updated_at
            )
            SELECT DISTINCT 
                survey_id, admin_id, account_name, account_type, date,
                recommendation, comments, MIN(created_at), MIN(updated_at)
            FROM survey_responses
            GROUP BY survey_id, admin_id, account_name, account_type, date, recommendation, comments
        ');

        DB::statement('
            INSERT INTO survey_response_details (
                header_id, question_id, response, created_at, updated_at
            )
            SELECT 
                h.id, sr.question_id, sr.response, sr.created_at, sr.updated_at
            FROM survey_responses sr
            JOIN survey_response_headers h ON 
                h.survey_id = sr.survey_id AND
                h.account_name = sr.account_name AND
                h.date = sr.date
        ');

        // Drop old table after migration
        Schema::dropIfExists('survey_responses');
    }

    public function down()
    {
        // Recreate the original survey_responses table
        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('surveys')->onDelete('cascade');
            $table->foreignId('admin_id')->nullable()->constrained('admin_users')->onDelete('set null');
            $table->foreignId('question_id')->constrained('survey_questions')->onDelete('cascade');
            $table->string('account_name', 100);
            $table->string('account_type', 50);
            $table->date('date');
            $table->string('response', 500);
            $table->tinyInteger('recommendation')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();
        });

        // Migrate data back if needed
        DB::statement('
            INSERT INTO survey_responses (
                survey_id, admin_id, question_id, account_name, account_type,
                date, response, recommendation, comments, created_at, updated_at
            )
            SELECT 
                h.survey_id, h.admin_id, d.question_id, h.account_name, h.account_type,
                h.date, d.response, h.recommendation, h.comments, d.created_at, d.updated_at
            FROM survey_response_headers h
            JOIN survey_response_details d ON d.header_id = h.id
        ');

        Schema::dropIfExists('survey_response_details');
        Schema::dropIfExists('survey_response_headers');
    }
};