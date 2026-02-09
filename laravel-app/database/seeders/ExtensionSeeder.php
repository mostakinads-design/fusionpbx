<?php

namespace Database\Seeders;

use App\Models\Extension;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExtensionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->count() === 0) {
            $this->command->warn('No users found. Please run UserSeeder first.');
            return;
        }

        // Create extensions for each user
        $extensions = [
            ['extension_number' => '1001', 'status' => 'active', 'description' => 'Admin Extension'],
            ['extension_number' => '1002', 'status' => 'active', 'description' => 'Sales Department'],
            ['extension_number' => '1003', 'status' => 'active', 'description' => 'Support Department'],
            ['extension_number' => '1004', 'status' => 'inactive', 'description' => 'Conference Room'],
            ['extension_number' => '1005', 'status' => 'active', 'description' => 'Development Team'],
            ['extension_number' => '1006', 'status' => 'suspended', 'description' => 'Temporary Extension'],
            ['extension_number' => '1007', 'status' => 'active', 'description' => 'Marketing Department'],
            ['extension_number' => '1008', 'status' => 'active', 'description' => 'HR Department'],
        ];

        foreach ($extensions as $index => $extensionData) {
            $user = $users->get($index % $users->count());
            
            Extension::create([
                'extension_number' => $extensionData['extension_number'],
                'user_id' => $user->id,
                'status' => $extensionData['status'],
                'description' => $extensionData['description'],
            ]);
        }
    }
}
