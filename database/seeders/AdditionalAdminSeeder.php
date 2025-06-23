<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Sbu;
use App\Models\Site;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class AdditionalAdminSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        
        // Get all SBUs and Sites
        $sbus = Sbu::all();
        $sites = Site::all();
        
        if ($sbus->isEmpty() || $sites->isEmpty()) {
            $this->command->error('Please ensure SBUs and Sites exist before running this seeder');
            return;
        }
        
        $this->command->info('Creating additional admin users with specific roles...');
        
        // Super Admin - has access to all SBUs and sites
        $superAdmin = Admin::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@company.com',
            'contact_number' => '09999999999',
            'password' => Hash::make('superadmin123'),
            'status' => 1,
        ]);
        
        // Attach all SBUs and sites to super admin
        $superAdmin->sbus()->attach($sbus->pluck('id'));
        $superAdmin->sites()->attach($sites->pluck('id'));
        
        // Regional Managers - each manages specific regions
        $this->createRegionalManagers($faker, $sbus, $sites);
        
        // Department Heads - each manages specific departments across multiple sites
        $this->createDepartmentHeads($faker, $sbus, $sites);
        
        // Site Managers - each manages specific sites
        $this->createSiteManagers($faker, $sbus, $sites);
        
        // Survey Specialists - focused on survey management
        $this->createSurveySpecialists($faker, $sbus, $sites);
        
        $this->command->info('Additional admin seeding completed!');
    }
    
    private function createRegionalManagers($faker, $sbus, $sites)
    {
        $regions = [
            ['name' => 'Luzon Regional Manager', 'email' => 'luzon.manager@company.com'],
            ['name' => 'Visayas Regional Manager', 'email' => 'visayas.manager@company.com'],
            ['name' => 'Mindanao Regional Manager', 'email' => 'mindanao.manager@company.com'],
        ];
        
        foreach ($regions as $region) {
            $admin = Admin::create([
                'name' => $region['name'],
                'email' => $region['email'],
                'contact_number' => $faker->numerify('09#########'),
                'password' => Hash::make('regional123'),
                'status' => 1,
            ]);
            
            // Assign all SBUs but only specific regional sites
            $admin->sbus()->attach($sbus->pluck('id'));
            
            // Assign sites based on region (simplified logic)
            if (stripos($region['name'], 'luzon') !== false) {
                $regionalSites = $sites->filter(function($site) {
                    return stripos($site->name, 'Bignay') !== false || 
                           stripos($site->name, 'Punturin') !== false;
                });
            } elseif (stripos($region['name'], 'visayas') !== false) {
                $regionalSites = $sites->filter(function($site) {
                    return stripos($site->name, 'Cebu') !== false || 
                           stripos($site->name, 'Bohol') !== false ||
                           stripos($site->name, 'Leyte') !== false ||
                           stripos($site->name, 'Tacloban') !== false ||
                           stripos($site->name, 'Ormoc') !== false ||
                           stripos($site->name, 'Sogod') !== false;
                });
            } else { // Mindanao
                $regionalSites = $sites->filter(function($site) {
                    return stripos($site->name, 'Gensan') !== false || 
                           stripos($site->name, 'CDO') !== false ||
                           stripos($site->name, 'Davao') !== false ||
                           stripos($site->name, 'Koronadal') !== false ||
                           stripos($site->name, 'Valencia') !== false ||
                           stripos($site->name, 'Iligan') !== false;
                });
            }
            
            $admin->sites()->attach($regionalSites->pluck('id'));
        }
    }
    
    private function createDepartmentHeads($faker, $sbus, $sites)
    {
        $departments = [
            ['name' => 'HR Department Head', 'email' => 'hr.head@company.com'],
            ['name' => 'Operations Department Head', 'email' => 'operations.head@company.com'],
            ['name' => 'Quality Assurance Head', 'email' => 'qa.head@company.com'],
            ['name' => 'Training Department Head', 'email' => 'training.head@company.com'],
            ['name' => 'Customer Service Head', 'email' => 'cs.head@company.com'],
        ];
        
        foreach ($departments as $dept) {
            $admin = Admin::create([
                'name' => $dept['name'],
                'email' => $dept['email'],
                'contact_number' => $faker->numerify('09#########'),
                'password' => Hash::make('dept123'),
                'status' => 1,
            ]);
            
            // Department heads have access to both SBUs
            $admin->sbus()->attach($sbus->pluck('id'));
            
            // Assign to main sites across all regions
            $mainSites = $sites->where('is_main', true);
            $admin->sites()->attach($mainSites->pluck('id'));
        }
    }
    
    private function createSiteManagers($faker, $sbus, $sites)
    {
        // Create managers for main sites
        $mainSites = $sites->where('is_main', true);
        
        foreach ($mainSites as $site) {
            $siteName = str_replace([' - main', ' -main'], '', $site->name);
            $emailName = strtolower(str_replace([' ', '-'], '.', $siteName));
            
            $admin = Admin::create([
                'name' => $siteName . ' Site Manager',
                'email' => $emailName . '.manager@company.com',
                'contact_number' => $faker->numerify('09#########'),
                'password' => Hash::make('site123'),
                'status' => 1,
            ]);
            
            // Assign to the SBU of this site
            $admin->sbus()->attach($site->sbu_id);
            
            // Assign to this site and its sub-sites
            $siteAndSubSites = $sites->where('sbu_id', $site->sbu_id)
                                   ->filter(function($s) use ($site) {
                                       return $s->id === $site->id || !$s->is_main;
                                   });
            
            $admin->sites()->attach($siteAndSubSites->pluck('id'));
        }
    }
    
    private function createSurveySpecialists($faker, $sbus, $sites)
    {
        $specialists = [
            ['name' => 'Employee Survey Specialist', 'email' => 'employee.surveys@company.com'],
            ['name' => 'Customer Survey Specialist', 'email' => 'customer.surveys@company.com'],
            ['name' => 'Training Survey Specialist', 'email' => 'training.surveys@company.com'],
            ['name' => 'Safety Survey Specialist', 'email' => 'safety.surveys@company.com'],
        ];
        
        foreach ($specialists as $specialist) {
            $admin = Admin::create([
                'name' => $specialist['name'],
                'email' => $specialist['email'],
                'contact_number' => $faker->numerify('09#########'),
                'password' => Hash::make('survey123'),
                'status' => 1,
            ]);
            
            // Survey specialists have access to all SBUs and sites for comprehensive data collection
            $admin->sbus()->attach($sbus->pluck('id'));
            $admin->sites()->attach($sites->pluck('id'));
        }
    }
}
