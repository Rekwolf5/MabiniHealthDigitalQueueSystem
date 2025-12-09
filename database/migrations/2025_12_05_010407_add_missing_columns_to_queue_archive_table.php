<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('queue_archive', function (Blueprint $table) {
            // Add missing columns from front_desk_queues table
            $table->unsignedBigInteger('service_id')->nullable()->after('patient_id');
            $table->unsignedBigInteger('assigned_staff_id')->nullable()->after('completed_at');
            $table->string('patient_name')->nullable()->after('queue_number');
            $table->string('contact_number')->nullable()->after('patient_name');
            $table->integer('age')->nullable()->after('contact_number');
            $table->text('chief_complaint')->nullable()->after('age');
            $table->text('allergies')->nullable()->after('chief_complaint');
            $table->string('urgency_level')->nullable()->after('status');
            $table->string('workflow_stage')->nullable()->after('urgency_level');
            $table->timestamp('called_at')->nullable()->after('arrived_at');
            $table->unsignedBigInteger('archived_by')->nullable()->after('archived_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('queue_archive', function (Blueprint $table) {
            $table->dropColumn([
                'service_id',
                'assigned_staff_id',
                'patient_name',
                'contact_number',
                'age',
                'chief_complaint',
                'allergies',
                'urgency_level',
                'workflow_stage',
                'called_at',
                'archived_by'
            ]);
        });
    }
};
