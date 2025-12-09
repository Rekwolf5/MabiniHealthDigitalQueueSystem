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
            // Add served_at column after completed_at if it doesn't exist
            if (!Schema::hasColumn('queue', 'served_at')) {
                $table->timestamp('served_at')->nullable()->after('completed_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('queue', function (Blueprint $table) {
            if (Schema::hasColumn('queue', 'served_at')) {
                $table->dropColumn('served_at');
            }
        });
    }
};
