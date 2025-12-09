<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DoctorUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample doctors
        $doctors = [
            [
                'name' => 'Dr. Maria Santos',
                'email' => 'doctor1@mabini.com',
                'password' => Hash::make('Doctor@2025'),
                'role' => 'doctor',
            ],
            [
                'name' => 'Dr. Jose Rizal',
                'email' => 'doctor2@mabini.com',
                'password' => Hash::make('Doctor@2025'),
                'role' => 'doctor',
            ],
            [
                'name' => 'Dr. Juan Dela Cruz',
                'email' => 'doctor3@mabini.com',
                'password' => Hash::make('Doctor@2025'),
                'role' => 'doctor',
            ],
        ];

        foreach ($doctors as $doctor) {
            // Check if doctor already exists
            if (!User::where('email', $doctor['email'])->exists()) {
                User::create($doctor);
                echo "✅ Created doctor: {$doctor['name']} ({$doctor['email']})\n";
            } else {
                echo "⏭️  Doctor already exists: {$doctor['email']}\n";
            }
        }

        echo "\n==============================================\n";
        echo "    DOCTOR LOGIN CREDENTIALS\n";
        echo "==============================================\n\n";
        echo "Doctor 1:\n";
        echo "  Email: doctor1@mabini.com\n";
        echo "  Password: Doctor@2025\n\n";
        echo "Doctor 2:\n";
        echo "  Email: doctor2@mabini.com\n";
        echo "  Password: Doctor@2025\n\n";
        echo "Doctor 3:\n";
        echo "  Email: doctor3@mabini.com\n";
        echo "  Password: Doctor@2025\n\n";
        echo "==============================================\n";
    }
}
