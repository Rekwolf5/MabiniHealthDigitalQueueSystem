<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First, remove any duplicate contacts (keep the oldest record)
        DB::statement('
            DELETE t1 FROM patients t1
            INNER JOIN patients t2 
            WHERE t1.id > t2.id 
            AND t1.contact = t2.contact
        ');

        // Make contact unique only if it's not already unique
        $indexes = collect(DB::select("SHOW INDEX FROM patients WHERE Column_name = 'contact'"));
        if (!$indexes->where('Key_name', '!=', 'PRIMARY')->count()) {
            Schema::table('patients', function (Blueprint $table) {
                $table->unique('contact');
            });
        }

        // Add email field for staff to optionally add (for account linking) if not exists
        if (!Schema::hasColumn('patients', 'email')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->string('email')->nullable()->after('contact');
                $table->unique('email');
            });
        }
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropUnique(['contact']);
            $table->dropUnique(['email']);
            $table->dropColumn('email');
        });
    }
};
