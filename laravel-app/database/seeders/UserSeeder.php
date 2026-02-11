<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            'name' => 'Admin User',
            'email' => 'admin@fusionpbx.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Create regular users
        User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@fusionpbx.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane.smith@fusionpbx.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        User::create([
            'name' => 'Bob Johnson',
            'email' => 'bob.johnson@fusionpbx.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        User::create([
            'name' => 'Alice Williams',
            'email' => 'alice.williams@fusionpbx.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);
    }
}
