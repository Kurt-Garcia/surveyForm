<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Developer;
use Illuminate\Support\Facades\Hash;

class DeveloperSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Developer::create([
            'username' => 'FastDev',
            'name' => 'DevKurt',
            'email' => 'jobgkurtkainne@gmail.com',
            'password' => Hash::make('Admin123'),
            'status' => true,
        ]);
    }
}
