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
        Schema::table('services', function (Blueprint $table) {
            // Daily availability settings
            $table->boolean('available_today')->default(true)->after('is_active');
            $table->string('unavailable_reason')->nullable()->after('available_today');
            
            // Capacity management
            $table->integer('daily_patient_limit')->nullable()->after('unavailable_reason');
            $table->integer('current_patient_count')->default(0)->after('daily_patient_limit');
            
            // Operating hours
            $table->time('start_time')->default('08:00:00')->after('current_patient_count');
            $table->time('end_time')->default('17:00:00')->after('start_time');
            
            // Last updated tracking
            $table->date('settings_updated_date')->nullable()->after('end_time');
            $table->unsignedBigInteger('updated_by_staff_id')->nullable()->after('settings_updated_date');
            
            $table->foreign('updated_by_staff_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['updated_by_staff_id']);
            $table->dropColumn([
                'available_today',
                'unavailable_reason', 
                'daily_patient_limit',
                'current_patient_count',
                'start_time',
                'end_time',
                'settings_updated_date',
                'updated_by_staff_id'
            ]);
        });
    }
};
