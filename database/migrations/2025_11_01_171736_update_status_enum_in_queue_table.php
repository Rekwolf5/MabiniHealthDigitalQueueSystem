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
         DB::statement("ALTER TABLE queue 
            MODIFY status ENUM('waiting', 'consulting', 'skipped', 'no show', 'cancelled', 'completed', 'unattended') 
            NOT NULL DEFAULT 'waiting'");

        Schema::table('queue', function (Blueprint $table) {
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         DB::statement("ALTER TABLE queue 
            MODIFY status ENUM('waiting', 'consulting', 'completed', 'unattended') 
            NOT NULL DEFAULT 'waiting'");

        Schema::table('queue', function (Blueprint $table) {
            //
        });
    }
};
