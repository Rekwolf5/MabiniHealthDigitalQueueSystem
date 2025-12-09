<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            // Rename 'price' to 'unit_price' for clarity
            $table->renameColumn('price', 'unit_price');
            
            // Add reorder_level column (default to 15 units as per model)
            $table->integer('reorder_level')->default(15)->after('stock');
        });
    }

    public function down(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            // Reverse the changes
            $table->renameColumn('unit_price', 'price');
            $table->dropColumn('reorder_level');
        });
    }
};
