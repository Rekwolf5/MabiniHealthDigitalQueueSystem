<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Patient;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if patient_id column already exists
        if (Schema::hasColumn('patients', 'patient_id')) {
            // Column already exists, just make sure it has a unique constraint
            Schema::table('patients', function (Blueprint $table) {
                if (!\DB::select("SHOW INDEX FROM patients WHERE Column_name = 'patient_id' AND Key_name != 'PRIMARY'")) {
                    $table->unique('patient_id');
                }
            });
        } else {
            // First, add the column without unique constraint
            Schema::table('patients', function (Blueprint $table) {
                $table->string('patient_id')->nullable()->after('id');
            });

            // Generate patient IDs for all existing patients
            $patients = \DB::table('patients')->get();
            $year = date('Y');
            
            foreach ($patients as $index => $patient) {
                $number = str_pad($patient->id, 4, '0', STR_PAD_LEFT);
                \DB::table('patients')
                    ->where('id', $patient->id)
                    ->update(['patient_id' => "P-{$year}-{$number}"]);
            }

            // Now add the unique constraint
            Schema::table('patients', function (Blueprint $table) {
                $table->string('patient_id')->nullable(false)->unique()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('patient_id');
        });
    }
};
