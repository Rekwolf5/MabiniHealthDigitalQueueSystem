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
            // Drop the foreign key constraint first
            $table->dropForeign(['patient_id']);
            
            // Modify patient_id to be nullable (for walk-in patients)
            $table->foreignId('patient_id')->nullable()->change()->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('queue_archive', function (Blueprint $table) {
            // Drop the foreign key
            $table->dropForeign(['patient_id']);
            
            // Make patient_id required again
            $table->foreignId('patient_id')->nullable(false)->change()->constrained()->onDelete('cascade');
        });
    }
};
