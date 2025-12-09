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
        Schema::create('queue_counters', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('service_type');
            $table->string('priority_lane'); // P (Priority), R (Regular)
            $table->integer('counter')->default(0);
            $table->timestamps();

            // Unique constraint: one counter per date-service-priority combination
            $table->unique(['date', 'service_type', 'priority_lane']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queue_counters');
    }
};
