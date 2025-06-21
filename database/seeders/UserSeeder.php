<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
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
            'email' => 'admin@gmail.com',
            'role' => 'admin',
            'password' => Hash::make('12345678'),
        ]);

        // Create approver user
        User::create([
            'name' => 'Approver',
            'email' => 'approver1@gmail.com',
            'role' => 'approver',
            'password' => Hash::make('12345678'),
        ]);
    }
} 