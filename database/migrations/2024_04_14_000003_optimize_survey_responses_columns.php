<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('survey_responses', function (Blueprint $table) {
            // Optimize string lengths based on actual needs
            $table->string('account_name', 100)->change();  // Reduced from default 255
            $table->string('account_type', 50)->change();   // Reduced from default 255
            
            // Change recommendation to tinyint as it only stores 1-10
            $table->tinyInteger('recommendation')->nullable()->change();
            
            // For response field, if it's always short answers, we can optimize it
            $table->string('response', 500)->change(); // Changed from text to varchar with reasonable limit
        });
    }

    public function down()
    {
        Schema::table('survey_responses', function (Blueprint $table) {
            $table->string('account_name')->change();
            $table->string('account_type')->change();
            $table->integer('recommendation')->nullable()->change();
            $table->text('response')->change();
        });
    }
};