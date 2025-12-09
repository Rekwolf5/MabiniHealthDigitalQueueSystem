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
        Schema::table('front_desk_queues', function (Blueprint $table) {
            // Basic medical information for triage
            $table->integer('age')->nullable()->after('contact_number');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('age');
            $table->text('chief_complaint')->nullable()->after('gender');
            $table->text('symptoms')->nullable()->after('chief_complaint');
            $table->text('allergies')->nullable()->after('symptoms');
            $table->enum('urgency_level', ['routine', 'urgent', 'emergency'])->default('routine')->after('priority');
            
            // Workflow tracking
            $table->enum('workflow_stage', ['registration', 'vitals', 'consultation', 'treatment', 'discharge'])->default('registration')->after('status');
            $table->timestamp('vitals_taken_at')->nullable()->after('called_at');
            $table->timestamp('consultation_started_at')->nullable()->after('vitals_taken_at');
            $table->unsignedBigInteger('assigned_staff_id')->nullable()->after('service_id'); // Current assigned staff
            
            $table->foreign('assigned_staff_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('front_desk_queues', function (Blueprint $table) {
            $table->dropForeign(['assigned_staff_id']);
            $table->dropColumn([
                'age', 'gender', 'chief_complaint', 'symptoms', 'allergies', 
                'urgency_level', 'workflow_stage', 'vitals_taken_at', 
                'consultation_started_at', 'assigned_staff_id'
            ]);
        });
    }
};
