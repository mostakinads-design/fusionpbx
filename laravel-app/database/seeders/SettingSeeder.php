<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // System Settings
            [
                'key' => 'system.name',
                'value' => 'FusionPBX',
                'type' => 'string',
                'group' => 'system',
                'description' => 'System name',
            ],
            [
                'key' => 'system.timezone',
                'value' => 'America/New_York',
                'type' => 'string',
                'group' => 'system',
                'description' => 'Default timezone',
            ],
            [
                'key' => 'system.maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'system',
                'description' => 'Enable/disable maintenance mode',
            ],
            
            // Call Settings
            [
                'key' => 'call.max_duration',
                'value' => '3600',
                'type' => 'integer',
                'group' => 'call',
                'description' => 'Maximum call duration in seconds',
            ],
            [
                'key' => 'call.recording_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'call',
                'description' => 'Enable call recording',
            ],
            [
                'key' => 'call.auto_answer',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'call',
                'description' => 'Enable auto answer',
            ],
            
            // Notification Settings
            [
                'key' => 'notification.email_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'notification',
                'description' => 'Enable email notifications',
            ],
            [
                'key' => 'notification.sms_enabled',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'notification',
                'description' => 'Enable SMS notifications',
            ],
            [
                'key' => 'notification.admin_email',
                'value' => 'admin@fusionpbx.com',
                'type' => 'string',
                'group' => 'notification',
                'description' => 'Admin email address',
            ],
            
            // Security Settings
            [
                'key' => 'security.password_expiry_days',
                'value' => '90',
                'type' => 'integer',
                'group' => 'security',
                'description' => 'Password expiry in days',
            ],
            [
                'key' => 'security.max_login_attempts',
                'value' => '5',
                'type' => 'integer',
                'group' => 'security',
                'description' => 'Maximum login attempts before lockout',
            ],
            [
                'key' => 'security.session_timeout',
                'value' => '1800',
                'type' => 'integer',
                'group' => 'security',
                'description' => 'Session timeout in seconds',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
