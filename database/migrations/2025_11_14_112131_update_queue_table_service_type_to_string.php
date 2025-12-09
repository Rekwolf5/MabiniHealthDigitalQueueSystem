<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change service_type from enum to string to support all service types
        DB::statement('ALTER TABLE queue MODIFY COLUMN service_type VARCHAR(255)');
        
        // Also update priority enum to match form values (Priority, Regular)
        DB::statement("ALTER TABLE queue MODIFY COLUMN priority ENUM('Priority', 'Regular') DEFAULT 'Regular'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE queue MODIFY COLUMN service_type ENUM('Consultation', 'Check-up', 'Vaccination', 'Emergency')");
        DB::statement("ALTER TABLE queue MODIFY COLUMN priority ENUM('Normal', 'Urgent', 'Emergency') DEFAULT 'Normal'");
    }
};
