<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Add patient_category column to track the specific category
     * (PWD, Pregnant, Senior, Regular) while priority is simplified to just Priority/Regular
     */
    public function up(): void
    {
        Schema::table('queue', function (Blueprint $table) {
            $table->enum('patient_category', ['PWD', 'Pregnant', 'Senior', 'Regular'])
                  ->default('Regular')
                  ->after('priority');
        });
        
        Schema::table('queue_archive', function (Blueprint $table) {
            $table->enum('patient_category', ['PWD', 'Pregnant', 'Senior', 'Regular'])
                  ->default('Regular')
                  ->after('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('queue', function (Blueprint $table) {
            $table->dropColumn('patient_category');
        });
        
        Schema::table('queue_archive', function (Blueprint $table) {
            $table->dropColumn('patient_category');
        });
    }
};
