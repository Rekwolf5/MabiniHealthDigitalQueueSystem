<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('queue', function (Blueprint $table) {
            $table->string('priority_reason')->nullable()->after('priority');
            $table->boolean('is_cutoff_priority')->default(false)->after('priority_reason');
            $table->date('cutoff_priority_expires')->nullable()->after('is_cutoff_priority');
        });
    }

    public function down(): void
    {
        Schema::table('queue', function (Blueprint $table) {
            $table->dropColumn(['priority_reason', 'is_cutoff_priority', 'cutoff_priority_expires']);
        });
    }
};
