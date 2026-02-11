<?php

namespace Database\Seeders;

use App\Models\CallLog;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CallLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = ['completed', 'missed', 'failed', 'busy'];
        $callTypes = ['inbound', 'outbound', 'internal'];
        
        $callerIds = [
            '+1234567890',
            '+1987654321',
            '+1555123456',
            '+1555987654',
            '1001',
            '1002',
            '1003',
        ];

        $destinations = [
            '1001',
            '1002',
            '1003',
            '1004',
            '+1555000111',
            '+1555000222',
            '+1555000333',
        ];

        // Create 50 sample call logs
        for ($i = 0; $i < 50; $i++) {
            CallLog::create([
                'caller_id' => $callerIds[array_rand($callerIds)],
                'destination' => $destinations[array_rand($destinations)],
                'duration' => rand(10, 3600),
                'status' => $statuses[array_rand($statuses)],
                'call_type' => $callTypes[array_rand($callTypes)],
                'call_date' => Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59)),
            ]);
        }
    }
}
