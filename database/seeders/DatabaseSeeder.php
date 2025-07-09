<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Sbu;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // First, create the SBUs and Sites
        $this->call([
            SbuAndSiteSeeder::class,
        ]);

        // Create admin users first (needed for foreign key constraint)
        $this->call([
            AdminSeeder::class,
        ]);

        // // Create the test user without sbu_id and site_id
        // $user = User::factory()->create([
        //     'name' => 'Juan De La Cruz',
        //     'email' => 'delacruzjuan@gmail.com',
        //     'contact_number' => '09123456789',
        //     'password' => bcrypt('admin123'),
        //     'created_by' => 1,
        // ]);

        // // Get the first SBU and its sites to assign to the user
        // $firstSbu = \App\Models\Sbu::first();
        // if ($firstSbu) {
        //     // Attach the user to the first SBU
        //     $user->sbus()->attach($firstSbu->id);
            
        //     // Attach the user to the first site of that SBU
        //     $firstSite = $firstSbu->sites()->first();
        //     if ($firstSite) {
        //         $user->sites()->attach($firstSite->id);
        //     }
        // }

        // Seed the Developer
        $this->call([
            DeveloperSeeder::class,
        ]);
        
        // Seed default translations
        $this->call([
            TranslationSeeder::class,
        ]);
        
        // // Create large dataset for testing
        // $this->call([
        //     LargeDataSeeder::class
        // ]);
        
        // // Create detailed surveys with comprehensive questions
        // $this->call([
        //     DetailedSurveySeeder::class
        // ]);
        
        // // Create sample survey responses
        // $this->call([
        //     SurveyResponseSeeder::class
        // ]);
    }
}
