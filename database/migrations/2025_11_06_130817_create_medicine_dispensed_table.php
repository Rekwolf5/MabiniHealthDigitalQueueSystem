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
        Schema::create('medicine_dispensed', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('queue_id')->nullable()->constrained('queue')->onDelete('cascade');
            $table->foreignId('medicine_id')->constrained()->onDelete('cascade');
            $table->foreignId('batch_id')->nullable()->constrained('medicine_batches')->onDelete('set null');
            $table->integer('quantity');
            $table->text('instructions')->nullable();
            $table->foreignId('dispensed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('dispensed_at')->nullable();
            $table->enum('status', ['pending', 'dispensed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_dispensed');
    }
};
