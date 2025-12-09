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
        // Change service_type from ENUM to VARCHAR to support more service types
        DB::statement("ALTER TABLE queue MODIFY COLUMN service_type VARCHAR(100)");
        
        // Update old service types to new ones
        DB::table('queue')->where('service_type', 'Vaccination')->update(['service_type' => 'Immunization Program']);
        DB::table('queue')->where('service_type', 'Check-up')->update(['service_type' => 'Consultation and Treatment']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to old structure if needed
        DB::statement("ALTER TABLE queue MODIFY COLUMN service_type VARCHAR(100)");
    }
};
