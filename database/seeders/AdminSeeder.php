<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\Sbu;
use App\Models\Site;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Create the admin user
        $admin = Admin::create([
            'is_seeder' => true,
            'name' => 'FDC Admin',
            'email' => 'fdcadmin@gmail.com',
            'contact_number' => '09123456789',
            'password' => Hash::make('admin123'),
        ]);

        // Get FDC and FUI SBUs to assign to the admin
        $fdcSbu = Sbu::where('name', 'FDC')->first();
        $fuiSbu = Sbu::where('name', 'FUI')->first();

        // Attach admin to both FDC and FUI SBUs
        if ($fdcSbu) {
            $admin->sbus()->attach($fdcSbu->id);
        }

        if ($fuiSbu) {
            $admin->sbus()->attach($fuiSbu->id);
        }

        // Attach admin to all sites
        $allSites = Site::all();
        if ($allSites->isNotEmpty()) {
            $admin->sites()->attach($allSites->pluck('id')->toArray());
        }
    }
}