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
        Schema::create('patient_medical_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('queue_id'); // Link to front_desk_queue
            $table->string('patient_name');
            $table->string('contact_number')->nullable();
            $table->integer('age')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->text('address')->nullable();
            $table->date('date_of_birth')->nullable();
            
            // Medical Information
            $table->text('chief_complaint')->nullable(); // Main reason for visit
            $table->text('present_illness')->nullable(); // History of present illness
            $table->text('past_medical_history')->nullable(); // Previous illnesses
            $table->text('allergies')->nullable(); // Known allergies
            $table->text('current_medications')->nullable(); // Current meds
            $table->text('social_history')->nullable(); // Smoking, drinking, etc.
            $table->text('family_history')->nullable(); // Family medical history
            
            // Vital Signs (JSON format for flexibility)
            $table->json('vital_signs')->nullable(); // BP, temp, pulse, resp, weight, height
            
            // Assessment and Plan
            $table->text('assessment')->nullable(); // Doctor's diagnosis
            $table->text('plan')->nullable(); // Treatment plan
            $table->text('notes')->nullable(); // Additional notes
            
            // Service tracking
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('created_by'); // Staff who created record
            $table->unsignedBigInteger('updated_by')->nullable(); // Last staff to update
            
            $table->foreign('queue_id')->references('id')->on('front_desk_queues')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_medical_records');
    }
};
