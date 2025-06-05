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
        
        // Remove the single relationship columns from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['sbu_id']);
            $table->dropForeign(['site_id']);
            $table->dropColumn(['sbu_id', 'site_id']);
        });
    }

    public function down()
    {
        // Re-add the single relationship columns
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('sbu_id')->nullable()->after('contact_number');
            $table->foreignId('site_id')->nullable()->after('sbu_id');
            
            $table->foreign('sbu_id')->references('id')->on('sbus')->onDelete('set null');
            $table->foreign('site_id')->references('id')->on('sites')->onDelete('set null');
        });
    }
    
    private function migrateExistingSingleRelationships()
    {
        // Get all users with single SBU/Site relationships
        $users = DB::table('users')
            ->whereNotNull('sbu_id')
            ->orWhereNotNull('site_id')
            ->get();
            
        foreach ($users as $user) {
            // Migrate SBU relationship to pivot table if not already exists
            if ($user->sbu_id) {
                $existingSbu = DB::table('user_sbu')
                    ->where('user_id', $user->id)
                    ->where('sbu_id', $user->sbu_id)
                    ->first();
                    
                if (!$existingSbu) {
                    DB::table('user_sbu')->insert([
                        'user_id' => $user->id,
                        'sbu_id' => $user->sbu_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            
            // Migrate Site relationship to pivot table if not already exists
            if ($user->site_id) {
                $existingSite = DB::table('user_site')
                    ->where('user_id', $user->id)
                    ->where('site_id', $user->site_id)
                    ->first();
                    
                if (!$existingSite) {
                    DB::table('user_site')->insert([
                        'user_id' => $user->id,
                        'site_id' => $user->site_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
};
