<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // First, add new columns for foreign keys
        Schema::table('admin_users', function (Blueprint $table) {
            $table->foreignId('sbu_id')->nullable()->after('sbu');
            $table->foreignId('site_id')->nullable()->after('site');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('sbu_id')->nullable()->after('sbu');
            $table->foreignId('site_id')->nullable()->after('site');
        });

        // Migrate existing data
        $this->migrateExistingData('admin_users');
        $this->migrateExistingData('users');

        // Add foreign key constraints
        Schema::table('admin_users', function (Blueprint $table) {
            $table->foreign('sbu_id')->references('id')->on('sbus')->onDelete('set null');
            $table->foreign('site_id')->references('id')->on('sites')->onDelete('set null');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('sbu_id')->references('id')->on('sbus')->onDelete('set null');
            $table->foreign('site_id')->references('id')->on('sites')->onDelete('set null');
        });

        // Drop old string columns
        Schema::table('admin_users', function (Blueprint $table) {
            $table->dropColumn(['sbu', 'site']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['sbu', 'site']);
        });
    }

    private function migrateExistingData($table)
    {
        $records = DB::table($table)->whereNotNull('sbu')->get();

        foreach ($records as $record) {
            // Find the SBU ID
            $sbuId = DB::table('sbus')->where('name', $record->sbu)->value('id');
            
            if ($sbuId) {
                // Find the site ID based on the site name and SBU ID
                $siteName = str_replace($record->sbu . ' - ', '', $record->site);
                $siteName = str_replace($record->sbu . ' ', '', $siteName);
                
                $siteId = DB::table('sites')
                    ->where('sbu_id', $sbuId)
                    ->where(function ($query) use ($record, $siteName) {
                        $query->where('name', $record->site)
                              ->orWhere('name', $siteName);
                    })
                    ->value('id');

                // Update the record with the foreign keys
                DB::table($table)
                    ->where('id', $record->id)
                    ->update([
                        'sbu_id' => $sbuId,
                        'site_id' => $siteId
                    ]);
            }
        }
    }

    public function down()
    {
        // First add back the string columns
        Schema::table('admin_users', function (Blueprint $table) {
            $table->string('sbu')->nullable()->after('name');
            $table->string('site')->nullable()->after('sbu');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('sbu')->nullable()->after('name');
            $table->string('site')->nullable()->after('sbu');
        });

        // Restore the data
        $this->restoreData('admin_users');
        $this->restoreData('users');

        // Drop the foreign key constraints and columns
        Schema::table('admin_users', function (Blueprint $table) {
            $table->dropForeign(['sbu_id']);
            $table->dropForeign(['site_id']);
            $table->dropColumn(['sbu_id', 'site_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['sbu_id']);
            $table->dropForeign(['site_id']);
            $table->dropColumn(['sbu_id', 'site_id']);
        });
    }

    private function restoreData($table)
    {
        $records = DB::table($table)->whereNotNull('sbu_id')->get();

        foreach ($records as $record) {
            $sbu = DB::table('sbus')->where('id', $record->sbu_id)->value('name');
            $site = DB::table('sites')->where('id', $record->site_id)->value('name');

            if ($sbu) {
                DB::table($table)
                    ->where('id', $record->id)
                    ->update([
                        'sbu' => $sbu,
                        'site' => $site
                    ]);
            }
        }
    }
};