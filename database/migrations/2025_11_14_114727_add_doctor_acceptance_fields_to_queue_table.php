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
            $table->timestamp('doctor_accepted_at')->nullable()->after('assigned_doctor_id');
            $table->text('rejection_reason')->nullable()->after('doctor_accepted_at');
            $table->unsignedBigInteger('rejected_by')->nullable()->after('rejection_reason');
            $table->timestamp('rejected_at')->nullable()->after('rejected_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('queue', function (Blueprint $table) {
            $table->dropColumn(['doctor_accepted_at', 'rejection_reason', 'rejected_by', 'rejected_at']);
        });
    }
};
