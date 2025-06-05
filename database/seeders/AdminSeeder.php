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
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'contact_number' => '09123456789',
            'password' => Hash::make('admin123'),
        ]);

        // Get the first SBU and Site to assign to the admin
        $firstSbu = Sbu::first();
        $firstSite = Site::first();

        if ($firstSbu) {
            // Attach the admin to the first SBU
            $admin->sbus()->attach($firstSbu->id);
        }

        if ($firstSite) {
            // Attach the admin to the first site
            $admin->sites()->attach($firstSite->id);
        }

        // Optionally, you can attach multiple SBUs and sites
        // For example, attach to all SBUs:
        // $admin->sbus()->attach(Sbu::pluck('id')->toArray());
        
        // Or attach to all sites of a specific SBU:
        // if ($firstSbu) {
        //     $sbuSites = Site::where('sbu_id', $firstSbu->id)->pluck('id')->toArray();
        //     $admin->sites()->attach($sbuSites);
        // }
    }
}