<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add assigned_doctor_id to queue table if it doesn't exist
        if (!Schema::hasColumn('queue', 'assigned_doctor_id')) {
            Schema::table('queue', function (Blueprint $table) {
                $table->unsignedBigInteger('assigned_doctor_id')->nullable()->after('patient_id');
                $table->foreign('assigned_doctor_id')->references('id')->on('users')->onDelete('set null');
            });
        }

        // Add doctor fields to consultations table
        Schema::table('consultations', function (Blueprint $table) {
            if (!Schema::hasColumn('consultations', 'doctor_id')) {
                $table->unsignedBigInteger('doctor_id')->nullable()->after('patient_id');
                $table->foreign('doctor_id')->references('id')->on('users')->onDelete('set null');
            }
            
            // Vital signs
            if (!Schema::hasColumn('consultations', 'chief_complaint')) {
                $table->string('chief_complaint')->nullable()->after('queue_id');
            }
            if (!Schema::hasColumn('consultations', 'blood_pressure')) {
                $table->string('blood_pressure')->nullable()->after('chief_complaint');
            }
            if (!Schema::hasColumn('consultations', 'temperature')) {
                $table->decimal('temperature', 4, 1)->nullable()->after('blood_pressure');
            }
            if (!Schema::hasColumn('consultations', 'pulse_rate')) {
                $table->integer('pulse_rate')->nullable()->after('temperature');
            }
            if (!Schema::hasColumn('consultations', 'weight')) {
                $table->decimal('weight', 5, 2)->nullable()->after('pulse_rate');
            }
            if (!Schema::hasColumn('consultations', 'height')) {
                $table->decimal('height', 5, 2)->nullable()->after('weight');
            }
            
            // Additional fields
            if (!Schema::hasColumn('consultations', 'physical_examination')) {
                $table->text('physical_examination')->nullable()->after('symptoms');
            }
            if (!Schema::hasColumn('consultations', 'doctor_notes')) {
                $table->text('doctor_notes')->nullable()->after('follow_up_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('queue', function (Blueprint $table) {
            $table->dropForeign(['assigned_doctor_id']);
            $table->dropColumn('assigned_doctor_id');
        });

        Schema::table('consultations', function (Blueprint $table) {
            $table->dropForeign(['doctor_id']);
            $table->dropColumn([
                'doctor_id',
                'chief_complaint',
                'blood_pressure',
                'temperature',
                'pulse_rate',
                'weight',
                'height',
                'physical_examination',
                'doctor_notes',
            ]);
        });
    }
};
