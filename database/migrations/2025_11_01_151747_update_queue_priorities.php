<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ Step 1: Ensure updates are applied before schema change
        DB::statement("UPDATE `queue` SET `priority` = 'PWD' WHERE `priority` = 'Emergency'");
        DB::statement("UPDATE `queue` SET `priority` = 'Pregnant' WHERE `priority` = 'Urgent'");
        DB::statement("UPDATE `queue` SET `priority` = 'Regular' WHERE `priority` = 'Normal'");

        // ✅ Step 2: Alter table AFTER all values are valid
        DB::statement("ALTER TABLE `queue` MODIFY `priority` ENUM('PWD','Pregnant','Senior','Regular') NOT NULL");
    }

    public function down(): void
    {
        // Rollback to the old ENUM if needed
        DB::statement("ALTER TABLE `queue` MODIFY `priority` ENUM('Emergency','Urgent','Normal') NOT NULL");

        // Optionally convert back
        DB::statement("UPDATE `queue` SET `priority` = 'Emergency' WHERE `priority` = 'PWD'");
        DB::statement("UPDATE `queue` SET `priority` = 'Urgent' WHERE `priority` = 'Pregnant'");
        DB::statement("UPDATE `queue` SET `priority` = 'Normal' WHERE `priority` = 'Regular'");
    }
};
