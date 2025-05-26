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
        Schema::table('surveys', function (Blueprint $table) {
            // First remove the old columns if they exist
            if (Schema::hasColumn('surveys', 'sbu')) {
                $table->dropColumn('sbu');
            }
            if (Schema::hasColumn('surveys', 'deployment_sites')) {
                $table->dropColumn('deployment_sites');
            }
            
            // Then add the foreign key
            $table->unsignedBigInteger('sbu_id')->nullable()->after('logo');
            $table->foreign('sbu_id')->references('id')->on('sbus')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->dropForeign(['sbu_id']);
            $table->dropColumn('sbu_id');
            $table->string('sbu')->nullable();
            $table->json('deployment_sites')->nullable();
        });
    }
};
