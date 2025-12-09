<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Patient;
use App\Models\PatientAccount;
use App\Models\Medicine;
use App\Models\Queue;
use App\Models\MedicalRecord;
use App\Models\SystemSetting;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // For SQLite, we use PRAGMA instead of SET FOREIGN_KEY_CHECKS
        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        // Create admin user
        User::create([
            'name' => 'Dr. Admin',
            'email' => 'admin@mabini.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '09123456789',
            'status' => 'active',
        ]);

        // Create staff user
        User::create([
            'name' => 'Nurse Staff',
            'email' => 'staff@mabini.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'phone' => '09234567890',
            'status' => 'active',
        ]);

        // Create front desk user
        User::create([
            'name' => 'Front Desk Officer',
            'email' => 'frontdesk@mabini.com',
            'password' => Hash::make('frontdesk123'),
            'role' => 'front_desk',
            'phone' => '09345678901',
            'status' => 'active',
        ]);

        // Create sample patients
        $patients = [
            [
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'age' => 35,
                'gender' => 'female',
                'contact' => '09123456789',
                'address' => '123 Main St, Mabini, Batangas',
                'date_of_birth' => '1989-03-15',
                'emergency_contact' => '09987654321',
            ],
            [
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'age' => 42,
                'gender' => 'male',
                'contact' => '09234567890',
                'address' => '456 Oak Ave, Mabini, Batangas',
                'date_of_birth' => '1982-07-22',
                'emergency_contact' => '09876543210',
            ],
            [
                'first_name' => 'Ana',
                'last_name' => 'Garcia',
                'age' => 28,
                'gender' => 'female',
                'contact' => '09345678901',
                'address' => '789 Pine Rd, Mabini, Batangas',
                'date_of_birth' => '1996-11-08',
                'emergency_contact' => '09765432109',
            ],
        ];

        foreach ($patients as $patientData) {
            $patient = Patient::create($patientData);
            
            // Create patient account
            PatientAccount::create([
                'patient_id' => $patient->id,
                'email' => strtolower($patient->first_name) . '.' . strtolower($patient->last_name) . '@example.com',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]);
        }

        // Create sample medicines
        $medicines = [
            [
                'name' => 'Paracetamol',
                'dosage' => '500mg',
                'type' => 'Tablet',
                'stock' => 150,
                'expiry_date' => '2025-12-31',
                'description' => 'Pain reliever and fever reducer',
                'price' => 2.50,
            ],
            [
                'name' => 'Amoxicillin',
                'dosage' => '250mg',
                'type' => 'Capsule',
                'stock' => 25,
                'expiry_date' => '2025-06-30',
                'description' => 'Antibiotic for bacterial infections',
                'price' => 15.00,
            ],
            [
                'name' => 'Ibuprofen',
                'dosage' => '400mg',
                'type' => 'Tablet',
                'stock' => 80,
                'expiry_date' => '2025-09-15',
                'description' => 'Anti-inflammatory pain reliever',
                'price' => 3.75,
            ],
            [
                'name' => 'Cetirizine',
                'dosage' => '10mg',
                'type' => 'Tablet',
                'stock' => 5,
                'expiry_date' => '2025-03-20',
                'description' => 'Antihistamine for allergies',
                'price' => 1.25,
            ],
        ];

        foreach ($medicines as $medicineData) {
            Medicine::create($medicineData);
        }

        // Create sample queue entries
        $queueEntries = [
            [
                'patient_id' => 1,
                'queue_number' => 'Q001',
                'priority' => 'Normal',
                'status' => 'Waiting',
                'service_type' => 'Consultation',
                'notes' => 'Regular check-up',
                'arrived_at' => now()->subMinutes(30),
            ],
            [
                'patient_id' => 2,
                'queue_number' => 'Q002',
                'priority' => 'Urgent',
                'status' => 'Consulting',
                'service_type' => 'Emergency',
                'notes' => 'Chest pain complaint',
                'arrived_at' => now()->subMinutes(15),
                'started_at' => now()->subMinutes(5),
            ],
            [
                'patient_id' => 3,
                'queue_number' => 'Q003',
                'priority' => 'Normal',
                'status' => 'Completed',
                'service_type' => 'Vaccination',
                'notes' => 'COVID-19 booster shot',
                'arrived_at' => now()->subHours(2),
                'started_at' => now()->subHours(1)->subMinutes(30),
                'completed_at' => now()->subHours(1),
            ],
        ];

        foreach ($queueEntries as $queueData) {
            Queue::create($queueData);
        }

        // Create sample medical records
        $medicalRecords = [
            [
                'patient_id' => 1,
                'diagnosis' => 'Hypertension',
                'treatment' => 'Prescribed Amlodipine 5mg daily',
                'notes' => 'Patient advised to monitor blood pressure daily',
                'visit_date' => now()->subDays(7),
            ],
            [
                'patient_id' => 2,
                'diagnosis' => 'Upper Respiratory Infection',
                'treatment' => 'Prescribed Amoxicillin 500mg TID for 7 days',
                'notes' => 'Patient to return if symptoms persist',
                'visit_date' => now()->subDays(3),
            ],
            [
                'patient_id' => 3,
                'diagnosis' => 'Allergic Rhinitis',
                'treatment' => 'Prescribed Cetirizine 10mg daily',
                'notes' => 'Avoid known allergens, use air purifier',
                'visit_date' => now()->subDays(1),
            ],
        ];

        foreach ($medicalRecords as $recordData) {
            MedicalRecord::create($recordData);
        }

        // Create system settings
        $settings = [
            ['key' => 'clinic_name', 'value' => 'Mabini Health Center', 'type' => 'string', 'description' => 'Name of the health center'],
            ['key' => 'clinic_address', 'value' => 'Mabini, Batangas', 'type' => 'string', 'description' => 'Address of the health center'],
            ['key' => 'clinic_phone', 'value' => '043-123-4567', 'type' => 'string', 'description' => 'Contact number'],
            ['key' => 'max_queue_per_day', 'value' => '50', 'type' => 'number', 'description' => 'Maximum queue entries per day'],
            ['key' => 'enable_patient_registration', 'value' => '1', 'type' => 'boolean', 'description' => 'Allow patient self-registration'],
            ['key' => 'low_stock_threshold', 'value' => '25', 'type' => 'number', 'description' => 'Low stock alert threshold'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::create($setting);
        }

        // Re-enable foreign key checks
        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('🔐 Login Credentials:');
        $this->command->info('   Admin: admin@mabini.com / password');
        $this->command->info('   Staff: staff@mabini.com / password');
        $this->command->info('   Front Desk: frontdesk@mabini.com / frontdesk123');
        $this->command->info('   Patient: maria.santos@example.com / password');
        $this->command->info('👥 Sample Data: 3 patients, 4 medicines, 3 queue entries');
    }
}
