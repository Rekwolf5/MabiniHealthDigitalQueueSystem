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
        // Update existing priority values if any archives exist
        DB::statement("UPDATE queue_archive SET priority = 'Regular' WHERE priority = 'Normal'");
        
        // Modify the enum to include new priority types
        DB::statement("ALTER TABLE queue_archive MODIFY COLUMN priority ENUM('Regular', 'Normal', 'Urgent', 'Emergency', 'PWD', 'Pregnant', 'Senior') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert priority values
        DB::statement("UPDATE queue_archive SET priority = 'Normal' WHERE priority = 'Regular'");
        
        // Revert enum to original values
        DB::statement("ALTER TABLE queue_archive MODIFY COLUMN priority ENUM('Normal', 'Urgent', 'Emergency') NOT NULL");
    }
};
