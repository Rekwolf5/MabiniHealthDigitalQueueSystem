<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use Illuminate\Support\Str;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "🏥 Creating sample data for Health Center System...\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

        // Create sample patients
        $patients = [
            [
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'age' => 35,
                'gender' => 'Male',
                'contact' => '09171234567',
                'address' => '123 Main St, Mabini, Batangas',
                'date_of_birth' => '1989-01-15',
            ],
            [
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'age' => 28,
                'gender' => 'Female',
                'contact' => '09181234567',
                'address' => '456 Rizal Ave, Mabini, Batangas',
                'date_of_birth' => '1996-06-20',
            ],
            [
                'first_name' => 'Pedro',
                'last_name' => 'Garcia',
                'age' => 45,
                'gender' => 'Male',
                'contact' => '09191234567',
                'address' => '789 Bonifacio St, Mabini, Batangas',
                'date_of_birth' => '1979-03-10',
            ],
        ];

        foreach ($patients as $patientData) {
            Patient::create($patientData);
        }
        echo "✅ Created " . count($patients) . " sample patients\n";

        // Create sample medicines with batches
        $medicines = [
            [
                'name' => 'Paracetamol 500mg',
                'description' => 'Pain reliever and fever reducer',
                'type' => 'Tablet',
                'dosage' => '500mg',
                'stock' => 500,
                'unit_price' => 2.50,
                'reorder_level' => 50,
                'expiry_date' => '2026-12-31', // Legacy field
                'batches' => [
                    ['batch_number' => 'PARA-2024-001', 'quantity' => 200, 'expiry_date' => '2025-12-31'],
                    ['batch_number' => 'PARA-2024-002', 'quantity' => 300, 'expiry_date' => '2026-06-30'],
                ]
            ],
            [
                'name' => 'Amoxicillin 500mg',
                'description' => 'Antibiotic for bacterial infections',
                'type' => 'Capsule',
                'dosage' => '500mg',
                'stock' => 300,
                'unit_price' => 8.00,
                'reorder_level' => 30,
                'expiry_date' => '2026-12-31',
                'batches' => [
                    ['batch_number' => 'AMOX-2024-001', 'quantity' => 150, 'expiry_date' => '2025-11-30'],
                    ['batch_number' => 'AMOX-2024-002', 'quantity' => 150, 'expiry_date' => '2026-03-31'],
                ]
            ],
            [
                'name' => 'Biogesic 500mg',
                'description' => 'Paracetamol for pain and fever',
                'type' => 'Tablet',
                'dosage' => '500mg',
                'stock' => 400,
                'unit_price' => 3.00,
                'reorder_level' => 40,
                'expiry_date' => '2026-12-31',
                'batches' => [
                    ['batch_number' => 'BIO-2024-001', 'quantity' => 200, 'expiry_date' => '2025-10-31'],
                    ['batch_number' => 'BIO-2024-002', 'quantity' => 200, 'expiry_date' => '2026-04-30'],
                ]
            ],
            [
                'name' => 'Cetirizine 10mg',
                'description' => 'Antihistamine for allergies',
                'type' => 'Tablet',
                'dosage' => '10mg',
                'stock' => 250,
                'unit_price' => 5.00,
                'reorder_level' => 25,
                'expiry_date' => '2026-12-31',
                'batches' => [
                    ['batch_number' => 'CETI-2024-001', 'quantity' => 250, 'expiry_date' => '2026-08-31'],
                ]
            ],
            [
                'name' => 'Ibuprofen 400mg',
                'description' => 'Anti-inflammatory and pain reliever',
                'type' => 'Tablet',
                'dosage' => '400mg',
                'stock' => 350,
                'unit_price' => 4.50,
                'reorder_level' => 35,
                'expiry_date' => '2026-12-31',
                'batches' => [
                    ['batch_number' => 'IBU-2024-001', 'quantity' => 350, 'expiry_date' => '2026-02-28'],
                ]
            ],
        ];

        foreach ($medicines as $medicineData) {
            $batches = $medicineData['batches'];
            unset($medicineData['batches']);
            
            $medicine = Medicine::create($medicineData);
            
            // Create batches for this medicine
            foreach ($batches as $batchData) {
                MedicineBatch::create([
                    'medicine_id' => $medicine->id,
                    'batch_number' => $batchData['batch_number'],
                    'quantity' => $batchData['quantity'],
                    'expiry_date' => $batchData['expiry_date'],
                ]);
            }
        }
        echo "✅ Created " . count($medicines) . " medicines with batches\n";

        echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "✨ Sample data seeding completed!\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        echo "📋 Next steps:\n";
        echo "1. Login as staff: staff@mabini.com / Staff@2025\n";
        echo "2. Go to Queue Management\n";
        echo "3. Add patient to queue\n";
        echo "4. Call patient\n";
        echo "5. Mark as served with prescribed medicines\n";
        echo "6. Go to Pharmacy to see pending prescription\n\n";
    }
}
