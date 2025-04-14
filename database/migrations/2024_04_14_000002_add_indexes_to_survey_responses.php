<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('survey_responses', function (Blueprint $table) {
            // Add indexes for commonly searched columns
            $table->index('account_name');
            $table->index('account_type');
            $table->index('date');
            
            // Add composite index for common query combinations
            $table->index(['survey_id', 'date']);
            $table->index(['account_name', 'account_type']);
        });
    }

    public function down()
    {
        Schema::table('survey_responses', function (Blueprint $table) {
            $table->dropIndex(['account_name']);
            $table->dropIndex(['account_type']);
            $table->dropIndex(['date']);
            $table->dropIndex(['survey_id', 'date']);
            $table->dropIndex(['account_name', 'account_type']);
        });
    }
};