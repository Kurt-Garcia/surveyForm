<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateUserStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update existing users to have status = 1 (active) where status is null
        DB::table('users')
            ->whereNull('status')
            ->update(['status' => 1]);

        // Update existing admin users to have status = 1 (active) where status is null
        DB::table('admin_users')
            ->whereNull('status')
            ->update(['status' => 1]);

        $this->command->info('Updated existing users and admin users with default status = 1 (active)');
    }
}
