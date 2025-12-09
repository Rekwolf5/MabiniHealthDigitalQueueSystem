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
        Schema::create('service_encounters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('queue_id');
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('staff_id'); // Staff handling this encounter
            
            // Encounter details
            $table->enum('encounter_type', ['vitals', 'consultation', 'treatment', 'lab_work', 'pharmacy', 'follow_up']);
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('duration_minutes')->nullable();
            
            // Medical data for this encounter
            $table->json('vital_signs')->nullable(); // If vitals encounter
            $table->text('findings')->nullable(); // What was found/observed
            $table->text('actions_taken')->nullable(); // What was done
            $table->text('recommendations')->nullable(); // Next steps
            $table->text('notes')->nullable(); // Additional notes
            
            // Referrals and next steps
            $table->unsignedBigInteger('referred_to_service')->nullable(); // If referring to another service
            $table->boolean('requires_follow_up')->default(false);
            $table->date('follow_up_date')->nullable();
            
            $table->foreign('queue_id')->references('id')->on('front_desk_queues')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services');
            $table->foreign('staff_id')->references('id')->on('users');
            $table->foreign('referred_to_service')->references('id')->on('services');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_encounters');
    }
};
