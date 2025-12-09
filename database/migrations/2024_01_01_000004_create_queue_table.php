<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->string('queue_number');
            $table->enum('priority', ['Normal', 'Urgent', 'Emergency'])->default('Normal');
            $table->enum('status', ['Waiting', 'Consulting', 'Completed', 'Skipped'])->default('Waiting');
            $table->enum('service_type', ['Consultation', 'Check-up', 'Vaccination', 'Emergency']);
            $table->text('notes')->nullable();
            $table->timestamp('arrived_at');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue');
    }
};
