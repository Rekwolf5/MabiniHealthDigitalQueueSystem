<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin account
        User::create([
            'name' => 'Admin',
            'email' => 'admin@mabini.com',
            'password' => Hash::make('Admin@2025'),
            'role' => 'admin',
        ]);

        // Create default staff account
        User::create([
            'name' => 'Staff User',
            'email' => 'staff@mabini.com',
            'password' => Hash::make('Staff@2025'),
            'role' => 'staff',
        ]);

        echo "✅ Admin and Staff accounts created successfully!\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "🔐 ADMIN LOGIN:\n";
        echo "   Email: admin@mabini.com\n";
        echo "   Password: Admin@2025\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "👤 STAFF LOGIN:\n";
        echo "   Email: staff@mabini.com\n";
        echo "   Password: Staff@2025\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    }
}

