<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@ktb.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Create test user
        User::create([
            'name' => 'Test User',
            'email' => 'user@ktb.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Create sample users
        User::create([
            'name' => 'Andi Wijaya',
            'email' => 'andi@ktb.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }
}
