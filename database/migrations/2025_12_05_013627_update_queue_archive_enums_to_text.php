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
        Schema::table('queue_archive', function (Blueprint $table) {
            // Change enum columns to string to match front_desk_queues values
            DB::statement('ALTER TABLE queue_archive MODIFY priority VARCHAR(255)');
            DB::statement('ALTER TABLE queue_archive MODIFY status VARCHAR(255)');
            DB::statement('ALTER TABLE queue_archive DROP COLUMN service_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('queue_archive', function (Blueprint $table) {
            // Restore original enum columns
            DB::statement("ALTER TABLE queue_archive MODIFY priority ENUM('Normal', 'Urgent', 'Emergency')");
            DB::statement("ALTER TABLE queue_archive MODIFY status ENUM('Waiting', 'Consulting', 'Completed', 'Skipped', 'Unattended', 'No Show')");
            $table->enum('service_type', ['Consultation', 'Check-up', 'Vaccination', 'Emergency'])->after('status');
        });
    }
};
