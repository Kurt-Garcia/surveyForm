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
        // Create or update the admin user
        $admin = Admin::updateOrCreate(
            ['email' => 'fdcadmin@gmail.com'],
            [
                'superadmin' => true,
                'name' => 'FDC Admin',
                'contact_number' => '09123456789',
                'password' => Hash::make('admin123'),
            ]
        );

        // Get FDC and FUI SBUs to assign to the admin
        $fdcSbu = Sbu::where('name', 'FDC')->first();
        $fuiSbu = Sbu::where('name', 'FUI')->first();

        // Attach admin to both FDC and FUI SBUs (sync to avoid duplicates)
        $sbuIds = [];
        if ($fdcSbu) {
            $sbuIds[] = $fdcSbu->id;
        }
        if ($fuiSbu) {
            $sbuIds[] = $fuiSbu->id;
        }
        if (!empty($sbuIds)) {
            $admin->sbus()->sync($sbuIds);
        }

        // Attach admin to all sites (sync to avoid duplicates)
        $allSites = Site::all();
        if ($allSites->isNotEmpty()) {
            $admin->sites()->sync($allSites->pluck('id')->toArray());
        }
    }
}