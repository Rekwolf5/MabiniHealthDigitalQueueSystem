<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'General Practitioner',
                'description' => 'General medical consultation and check-up',
                'capacity_per_hour' => 8,
                'operating_hours' => [
                    'monday' => ['start' => '08:00', 'end' => '17:00'],
                    'tuesday' => ['start' => '08:00', 'end' => '17:00'],
                    'wednesday' => ['start' => '08:00', 'end' => '17:00'],
                    'thursday' => ['start' => '08:00', 'end' => '17:00'],
                    'friday' => ['start' => '08:00', 'end' => '17:00'],
                    'saturday' => ['start' => '08:00', 'end' => '12:00'],
                ],
                'is_active' => true
            ],
            [
                'name' => 'Dental Service',
                'description' => 'Dental check-up, cleaning, and procedures',
                'capacity_per_hour' => 4,
                'operating_hours' => [
                    'monday' => ['start' => '08:00', 'end' => '17:00'],
                    'tuesday' => ['start' => '08:00', 'end' => '17:00'],
                    'wednesday' => ['start' => '08:00', 'end' => '17:00'],
                    'thursday' => ['start' => '08:00', 'end' => '17:00'],
                    'friday' => ['start' => '08:00', 'end' => '17:00'],
                ],
                'is_active' => true
            ],
            [
                'name' => 'Laboratory Service',
                'description' => 'Blood tests, urine tests, and other laboratory procedures',
                'capacity_per_hour' => 12,
                'operating_hours' => [
                    'monday' => ['start' => '07:00', 'end' => '16:00'],
                    'tuesday' => ['start' => '07:00', 'end' => '16:00'],
                    'wednesday' => ['start' => '07:00', 'end' => '16:00'],
                    'thursday' => ['start' => '07:00', 'end' => '16:00'],
                    'friday' => ['start' => '07:00', 'end' => '16:00'],
                    'saturday' => ['start' => '07:00', 'end' => '11:00'],
                ],
                'is_active' => true
            ],
            [
                'name' => 'Pharmacy',
                'description' => 'Medication dispensing and pharmaceutical consultation',
                'capacity_per_hour' => 15,
                'operating_hours' => [
                    'monday' => ['start' => '08:00', 'end' => '17:00'],
                    'tuesday' => ['start' => '08:00', 'end' => '17:00'],
                    'wednesday' => ['start' => '08:00', 'end' => '17:00'],
                    'thursday' => ['start' => '08:00', 'end' => '17:00'],
                    'friday' => ['start' => '08:00', 'end' => '17:00'],
                    'saturday' => ['start' => '08:00', 'end' => '12:00'],
                ],
                'is_active' => true
            ],
            [
                'name' => 'Maternal & Child Health',
                'description' => 'Prenatal care, child immunization, and maternal health services',
                'capacity_per_hour' => 6,
                'operating_hours' => [
                    'monday' => ['start' => '08:00', 'end' => '17:00'],
                    'tuesday' => ['start' => '08:00', 'end' => '17:00'],
                    'wednesday' => ['start' => '08:00', 'end' => '17:00'],
                    'thursday' => ['start' => '08:00', 'end' => '17:00'],
                    'friday' => ['start' => '08:00', 'end' => '17:00'],
                ],
                'is_active' => true
            ]
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
