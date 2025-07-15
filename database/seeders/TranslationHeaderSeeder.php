<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TranslationHeader;

class TranslationHeaderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $headers = [
            [
                'name' => 'English',
                'locale' => 'en',
                'is_active' => true
            ],
            [
                'name' => 'Tagalog',
                'locale' => 'tl',
                'is_active' => true
            ],
            [
                'name' => 'Cebuano',
                'locale' => 'ceb',
                'is_active' => true
            ]
        ];

        foreach ($headers as $header) {
            TranslationHeader::updateOrCreate(
                ['locale' => $header['locale']],
                $header
            );
        }
    }
}