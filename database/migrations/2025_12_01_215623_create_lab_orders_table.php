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
        Schema::create('lab_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('queue_id');
            $table->unsignedBigInteger('ordered_by'); // Doctor who ordered
            
            // Lab test details
            $table->string('test_name'); // e.g., "Complete Blood Count", "Urinalysis"
            $table->string('test_code')->nullable(); // Lab internal code
            $table->text('clinical_indication'); // Why test was ordered
            $table->text('special_instructions')->nullable(); // Fasting required, etc.
            $table->enum('priority', ['routine', 'urgent', 'stat'])->default('routine');
            $table->enum('specimen_type', ['blood', 'urine', 'stool', 'sputum', 'other'])->nullable();
            
            // Lab processing
            $table->enum('status', ['pending', 'collected', 'processing', 'completed', 'cancelled'])->default('pending');
            $table->unsignedBigInteger('collected_by')->nullable(); // Lab staff who collected specimen
            $table->timestamp('collected_at')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable(); // Lab tech who processed
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            // Results
            $table->text('results')->nullable(); // Lab results text
            $table->json('result_values')->nullable(); // Structured result data
            $table->text('interpretation')->nullable(); // Lab interpretation
            $table->text('reference_ranges')->nullable(); // Normal values
            $table->enum('result_status', ['normal', 'abnormal', 'critical'])->nullable();
            $table->string('result_file_path')->nullable(); // Path to uploaded result file
            
            // Follow-up
            $table->boolean('requires_follow_up')->default(false);
            $table->text('follow_up_recommendations')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable(); // Doctor who reviewed results
            $table->timestamp('reviewed_at')->nullable();
            
            $table->foreign('queue_id')->references('id')->on('front_desk_queues')->onDelete('cascade');
            $table->foreign('ordered_by')->references('id')->on('users');
            $table->foreign('collected_by')->references('id')->on('users');
            $table->foreign('processed_by')->references('id')->on('users');
            $table->foreign('reviewed_by')->references('id')->on('users');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_orders');
    }
};
