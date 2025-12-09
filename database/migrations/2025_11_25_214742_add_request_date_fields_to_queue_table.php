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
        Schema::table('queue', function (Blueprint $table) {
            // Only add columns that don't exist yet
            if (!Schema::hasColumn('queue', 'staff_notes')) {
                $table->text('staff_notes')->nullable(); // Staff notes for approval/rejection
            }
            if (!Schema::hasColumn('queue', 'pwd_id')) {
                $table->string('pwd_id')->nullable(); // PWD ID number
            }
            if (!Schema::hasColumn('queue', 'senior_id')) {
                $table->string('senior_id')->nullable(); // Senior Citizen ID number
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('queue', function (Blueprint $table) {
            $table->dropColumn([
                'staff_notes',
                'pwd_id',
                'senior_id'
            ]);
        });
    }
};
