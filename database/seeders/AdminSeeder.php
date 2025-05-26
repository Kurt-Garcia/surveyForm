<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        Admin::create([
            'sbu_id' => '1',
            'site_id' => '1',
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'contact_number' => '09123456789',
            'password' => Hash::make('admin123'),
        ]);
    }
}