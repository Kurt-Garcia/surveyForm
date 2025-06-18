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
        $statusValues = DB::table('users')->select('id', 'status')->get();
        
        Schema::table('users', function (Blueprint $table) {
            // Drop the existing status column
            $table->dropColumn('status');
        });
        
        Schema::table('users', function (Blueprint $table) {
            // Add status column after email_verified_at
            $table->tinyInteger('status')->default(1)->after('email_verified_at');
        });
        
        // Restore the status values
        foreach ($statusValues as $user) {
            DB::table('users')
                ->where('id', $user->id)
                ->update(['status' => $user->status]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Store existing status values
        $statusValues = DB::table('users')->select('id', 'status')->get();
        
        Schema::table('users', function (Blueprint $table) {
            // Drop the status column
            $table->dropColumn('status');
        });
        
        Schema::table('users', function (Blueprint $table) {
            // Add status column at the end (original position)
            $table->tinyInteger('status')->default(1);
        });
        
        // Restore the status values
        foreach ($statusValues as $user) {
            DB::table('users')
                ->where('id', $user->id)
                ->update(['status' => $user->status]);
        }
    }
};
