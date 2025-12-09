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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('queue_id');
            $table->unsignedBigInteger('prescribed_by'); // Doctor who prescribed
            $table->unsignedBigInteger('medicine_id');
            
            // Prescription details
            $table->string('medicine_name'); // Store name for reference even if medicine deleted
            $table->string('dosage'); // e.g., "500mg"
            $table->string('frequency'); // e.g., "3 times daily", "Every 8 hours"
            $table->string('duration'); // e.g., "7 days", "Until finished"
            $table->integer('quantity_prescribed'); // Total pills/units prescribed
            $table->text('instructions')->nullable(); // "Take with food", "Before meals", etc.
            $table->text('indication')->nullable(); // What condition this treats
            
            // Pharmacy tracking
            $table->enum('status', ['pending', 'dispensed', 'cancelled'])->default('pending');
            $table->integer('quantity_dispensed')->default(0);
            $table->unsignedBigInteger('dispensed_by')->nullable(); // Pharmacy staff
            $table->timestamp('dispensed_at')->nullable();
            $table->text('pharmacy_notes')->nullable();
            
            // Follow-up
            $table->boolean('requires_follow_up')->default(false);
            $table->date('follow_up_date')->nullable();
            
            $table->foreign('queue_id')->references('id')->on('front_desk_queues')->onDelete('cascade');
            $table->foreign('prescribed_by')->references('id')->on('users');
            $table->foreign('medicine_id')->references('id')->on('medicines')->onDelete('cascade');
            $table->foreign('dispensed_by')->references('id')->on('users');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
