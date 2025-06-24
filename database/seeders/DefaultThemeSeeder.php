<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ThemeSetting;

class DefaultThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if default themes already exist
        $defaultThemes = [
            [
                'name' => 'Default',
                'admin_id' => null, // Global theme
                'is_active' => true,
                'primary_color' => '#0078BD',
                'secondary_color' => '#005799',
                'accent_color' => '#0091ff',
                'background_color' => '#f8f9fa',
                'text_color' => '#333333',
                'heading_font' => 'Inter',
                'body_font' => 'Inter',
            ],
            [
                'name' => 'Dark Mode',
                'admin_id' => null, // Global theme
                'is_active' => false,
                'primary_color' => '#4e73df',
                'secondary_color' => '#6f42c1',
                'accent_color' => '#36b9cc',
                'background_color' => '#1a1a1a',
                'text_color' => '#ffffff',
                'heading_font' => 'Roboto',
                'body_font' => 'Roboto',
            ],
            [
                'name' => 'Corporate Blue',
                'admin_id' => null, // Global theme
                'is_active' => false,
                'primary_color' => '#2c5aa0',
                'secondary_color' => '#1a365d',
                'accent_color' => '#3182ce',
                'background_color' => '#f7fafc',
                'text_color' => '#2d3748',
                'heading_font' => 'Open Sans',
                'body_font' => 'Open Sans',
            ],
            [
                'name' => 'Modern Green',
                'admin_id' => null, // Global theme
                'is_active' => false,
                'primary_color' => '#38a169',
                'secondary_color' => '#2f855a',
                'accent_color' => '#48bb78',
                'background_color' => '#f0fff4',
                'text_color' => '#1a202c',
                'heading_font' => 'Montserrat',
                'body_font' => 'Lato',
            ],
            [
                'name' => 'Elegant Purple',
                'admin_id' => null, // Global theme
                'is_active' => false,
                'primary_color' => '#805ad5',
                'secondary_color' => '#553c9a',
                'accent_color' => '#9f7aea',
                'background_color' => '#faf5ff',
                'text_color' => '#2d3748',
                'heading_font' => 'Playfair Display',
                'body_font' => 'Source Sans Pro',
            ],
        ];

        foreach ($defaultThemes as $themeData) {
            // Check if theme already exists
            $existingTheme = ThemeSetting::where('name', $themeData['name'])
                                       ->whereNull('admin_id')
                                       ->first();

            if (!$existingTheme) {
                ThemeSetting::create($themeData);
                $this->command->info("Created default theme: {$themeData['name']}");
            } else {
                $this->command->info("Default theme already exists: {$themeData['name']}");
            }
        }
    }
}
