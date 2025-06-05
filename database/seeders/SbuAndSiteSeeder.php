<?php

namespace Database\Seeders;

use App\Models\Sbu;
use App\Models\Site;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SbuAndSiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create FDC SBU and its sites
        $fdc = Sbu::firstOrCreate(['name' => 'FDC']);
        
        // Only create sites if this SBU doesn't have any sites yet
        if ($fdc->sites()->count() === 0) {
            // Camanava Region
            Site::create(['sbu_id' => $fdc->id, 'name' => 'FDC Bignay - main', 'is_main' => true]);
            Site::create(['sbu_id' => $fdc->id, 'name' => 'FDC Punturin', 'is_main' => false]);
            
            // Bohol Region
            Site::create(['sbu_id' => $fdc->id, 'name' => 'FDC Tagbilaran - main', 'is_main' => true]);
            Site::create(['sbu_id' => $fdc->id, 'name' => 'FDC Ubay', 'is_main' => false]);
            
            // Leyte Region
            Site::create(['sbu_id' => $fdc->id, 'name' => 'FDC Tacloban - main', 'is_main' => true]);
            Site::create(['sbu_id' => $fdc->id, 'name' => 'FDC Ormoc', 'is_main' => false]);
            Site::create(['sbu_id' => $fdc->id, 'name' => 'FDC Sogod', 'is_main' => false]);
            
            // Samar Region
            Site::create(['sbu_id' => $fdc->id, 'name' => 'FDC Calbayog - main', 'is_main' => true]);
            Site::create(['sbu_id' => $fdc->id, 'name' => 'FDC Bogongan', 'is_main' => false]);
            Site::create(['sbu_id' => $fdc->id, 'name' => 'FDC Catarman', 'is_main' => false]);
            
            // Panay Region
            Site::create(['sbu_id' => $fdc->id, 'name' => 'FDC Roxas - main', 'is_main' => true]);
            Site::create(['sbu_id' => $fdc->id, 'name' => 'FDC Kalibo', 'is_main' => false]);
            
            // Mindanao Region
            Site::create(['sbu_id' => $fdc->id, 'name' => 'FDC Gensan - main', 'is_main' => true]);
            Site::create(['sbu_id' => $fdc->id, 'name' => 'FDC Koronadal', 'is_main' => false]);
            Site::create(['sbu_id' => $fdc->id, 'name' => 'FDC CDO - main', 'is_main' => true]);
            Site::create(['sbu_id' => $fdc->id, 'name' => 'FDC Valencia', 'is_main' => false]);
            Site::create(['sbu_id' => $fdc->id, 'name' => 'FDC Iligan', 'is_main' => false]);
            Site::create(['sbu_id' => $fdc->id, 'name' => 'FDC RX/RO', 'is_main' => false]);
            Site::create(['sbu_id' => $fdc->id, 'name' => 'FDC Cebu - main', 'is_main' => true]);
            Site::create(['sbu_id' => $fdc->id, 'name' => 'FDC Davao', 'is_main' => false]);
        }
        
        // Create FUI SBU and its sites
        $fui = Sbu::firstOrCreate(['name' => 'FUI']);
        
        // Only create sites if this SBU doesn't have any sites yet
        if ($fui->sites()->count() === 0) {
            // NAI Sites
            Site::create(['sbu_id' => $fui->id, 'name' => 'NAI Cebu - main', 'is_main' => true]);
            Site::create(['sbu_id' => $fui->id, 'name' => 'NAI Bohol', 'is_main' => false]);
            Site::create(['sbu_id' => $fui->id, 'name' => 'NAI Iloilo - main', 'is_main' => true]);
            Site::create(['sbu_id' => $fui->id, 'name' => 'NAI Roxas', 'is_main' => false]);
            Site::create(['sbu_id' => $fui->id, 'name' => 'NAI Bacolod - main', 'is_main' => true]);
            Site::create(['sbu_id' => $fui->id, 'name' => 'NAI Dumaguete', 'is_main' => false]);
            Site::create(['sbu_id' => $fui->id, 'name' => 'NAI Leyte - main', 'is_main' => true]);
            Site::create(['sbu_id' => $fui->id, 'name' => 'NAI Samar', 'is_main' => false]);
            Site::create(['sbu_id' => $fui->id, 'name' => 'NAI Borongan', 'is_main' => false]);
            
            // MNC Sites
            Site::create(['sbu_id' => $fui->id, 'name' => 'MNC Cebu - main', 'is_main' => true]);
            Site::create(['sbu_id' => $fui->id, 'name' => 'MNC Bohol', 'is_main' => false]);
            Site::create(['sbu_id' => $fui->id, 'name' => 'MNC Ozamiz - main', 'is_main' => true]);
            Site::create(['sbu_id' => $fui->id, 'name' => 'MNC Dipolog', 'is_main' => false]);
            
            // Shell Sites
            Site::create(['sbu_id' => $fui->id, 'name' => 'Shell Cebu - main', 'is_main' => true]);
            Site::create(['sbu_id' => $fui->id, 'name' => 'Shell Bohol', 'is_main' => false]);
            Site::create(['sbu_id' => $fui->id, 'name' => 'Shell Leyte - main', 'is_main' => true]);
            Site::create(['sbu_id' => $fui->id, 'name' => 'Shell Samar', 'is_main' => false]);
            Site::create(['sbu_id' => $fui->id, 'name' => 'Shell Negros - main', 'is_main' => true]);
            Site::create(['sbu_id' => $fui->id, 'name' => 'Shell Panay', 'is_main' => false]);
        }
    }
}
