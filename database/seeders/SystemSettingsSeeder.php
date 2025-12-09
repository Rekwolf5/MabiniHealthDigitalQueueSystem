<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\DB;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'queue_cutoff_time',
                'value' => '17:00',
                'type' => 'time',
                'description' => 'Time when health center stops accepting patients for the day (cutoff time). Remaining queues will be marked as Unattended.',
            ],
            [
                'key' => 'opening_time',
                'value' => '08:00',
                'type' => 'time',
                'description' => 'Health center opening time',
            ],
            [
                'key' => 'closing_time',
                'value' => '17:00',
                'type' => 'time',
                'description' => 'Health center closing time',
            ],
            [
                'key' => 'max_queue_per_day',
                'value' => '100',
                'type' => 'number',
                'description' => 'Maximum number of queue requests allowed per day',
            ],
            [
                'key' => 'enable_cutoff_notifications',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Send email/SMS notifications to patients when their queue is marked as unattended due to cutoff',
            ],
            [
                'key' => 'cutoff_priority_option',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Allow cut-off patients to request priority queue the next day (one-time courtesy)',
            ],
            [
                'key' => 'cutoff_warning_time',
                'value' => '16:00',
                'type' => 'time',
                'description' => 'Time to display warning to staff about approaching cutoff (1 hour before cutoff)',
            ],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('System settings seeded successfully!');
    }
}
