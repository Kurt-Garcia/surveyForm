<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Store existing status values
        $statusValues = DB::table('admin_users')->select('id', 'status')->get();
        
        Schema::table('admin_users', function (Blueprint $table) {
            // Drop the existing status column
            $table->dropColumn('status');
        });
        
        Schema::table('admin_users', function (Blueprint $table) {
            // Add status column after email
            $table->tinyInteger('status')->default(1)->after('contact_number');
        });
        
        // Restore the status values
        foreach ($statusValues as $admin) {
            DB::table('admin_users')
                ->where('id', $admin->id)
                ->update(['status' => $admin->status]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Store existing status values
        $statusValues = DB::table('admin_users')->select('id', 'status')->get();
        
        Schema::table('admin_users', function (Blueprint $table) {
            // Drop the status column
            $table->dropColumn('status');
        });
        
        Schema::table('admin_users', function (Blueprint $table) {
            // Add status column at the end (original position)
            $table->tinyInteger('status')->default(1);
        });
        
        // Restore the status values
        foreach ($statusValues as $admin) {
            DB::table('admin_users')
                ->where('id', $admin->id)
                ->update(['status' => $admin->status]);
        }
    }
};
