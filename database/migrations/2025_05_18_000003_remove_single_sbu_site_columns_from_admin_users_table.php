<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Before removing the columns, migrate any existing single relationships 
        // to the many-to-many pivot tables if they haven't been migrated yet
        $this->migrateExistingSingleRelationships();
        
        // Remove the single relationship columns from admin_users table
        Schema::table('admin_users', function (Blueprint $table) {
            $table->dropForeign(['sbu_id']);
            $table->dropForeign(['site_id']);
            $table->dropColumn(['sbu_id', 'site_id']);
        });
    }

    public function down()
    {
        // Re-add the single relationship columns
        Schema::table('admin_users', function (Blueprint $table) {
            $table->foreignId('sbu_id')->nullable()->after('contact_number');
            $table->foreignId('site_id')->nullable()->after('sbu_id');
            
            $table->foreign('sbu_id')->references('id')->on('sbus')->onDelete('set null');
            $table->foreign('site_id')->references('id')->on('sites')->onDelete('set null');
        });
    }
    
    private function migrateExistingSingleRelationships()
    {
        // Get all admin users with single SBU/Site relationships
        $adminUsers = DB::table('admin_users')
            ->whereNotNull('sbu_id')
            ->orWhereNotNull('site_id')
            ->get();
            
        foreach ($adminUsers as $admin) {
            // Migrate SBU relationship to pivot table if not already exists
            if ($admin->sbu_id) {
                $existingSbu = DB::table('admin_sbu')
                    ->where('admin_id', $admin->id)
                    ->where('sbu_id', $admin->sbu_id)
                    ->first();
                    
                if (!$existingSbu) {
                    DB::table('admin_sbu')->insert([
                        'admin_id' => $admin->id,
                        'sbu_id' => $admin->sbu_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            
            // Migrate Site relationship to pivot table if not already exists
            if ($admin->site_id) {
                $existingSite = DB::table('admin_site')
                    ->where('admin_id', $admin->id)
                    ->where('site_id', $admin->site_id)
                    ->first();
                    
                if (!$existingSite) {
                    DB::table('admin_site')->insert([
                        'admin_id' => $admin->id,
                        'site_id' => $admin->site_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
};
