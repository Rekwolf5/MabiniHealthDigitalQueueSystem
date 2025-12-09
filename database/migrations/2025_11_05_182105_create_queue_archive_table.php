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
        Schema::create('queue_archive', function (Blueprint $table) {
            $table->id();
            // Original queue data
            $table->unsignedBigInteger('original_queue_id'); // Original queue ID before archiving
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->string('queue_number');
            $table->enum('priority', ['Normal', 'Urgent', 'Emergency']);
            $table->enum('status', ['Waiting', 'Consulting', 'Completed', 'Skipped', 'Unattended', 'No Show']);
            $table->enum('service_type', ['Consultation', 'Check-up', 'Vaccination', 'Emergency']);
            $table->text('notes')->nullable();
            $table->string('qr_code', 64)->nullable();
            $table->string('verification_token', 32)->nullable();
            
            // Timestamps from original queue
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('queue_created_at')->nullable(); // Original created_at from queue
            $table->timestamp('queue_updated_at')->nullable(); // Original updated_at from queue
            
            // Archive metadata
            $table->timestamp('archived_at')->nullable(); // When it was archived
            $table->string('archived_reason')->default('Auto-archived after 30 days'); // Why it was archived
            $table->timestamps(); // created_at and updated_at for archive record itself
            
            // Indexes for faster queries
            $table->index('original_queue_id');
            $table->index('queue_number');
            $table->index('archived_at');
            $table->index(['patient_id', 'archived_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queue_archive');
    }
};
