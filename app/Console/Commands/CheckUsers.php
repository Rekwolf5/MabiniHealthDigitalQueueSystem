<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CheckUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and create default admin/staff users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking users in database...');
        $this->newLine();
        
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->warn('No users found in database!');
            $this->info('Creating default admin and staff accounts...');
            
            // Create admin
            User::create([
                'name' => 'Admin',
                'email' => 'admin@mabini.com',
                'password' => Hash::make('Admin@2025'),
                'role' => 'admin',
            ]);
            
            // Create staff
            User::create([
                'name' => 'Staff User',
                'email' => 'staff@mabini.com',
                'password' => Hash::make('Staff@2025'),
                'role' => 'staff',
            ]);
            
            $this->info('✅ Default accounts created!');
            $this->newLine();
        }
        
        // Display all users
        $users = User::all();
        $this->table(
            ['ID', 'Name', 'Email', 'Role'],
            $users->map(function ($user) {
                return [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->role,
                ];
            })
        );
        
        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('🔐 LOGIN CREDENTIALS:');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('ADMIN:');
        $this->info('  Email: admin@mabini.com');
        $this->info('  Password: Admin@2025');
        $this->newLine();
        $this->info('STAFF:');
        $this->info('  Email: staff@mabini.com');
        $this->info('  Password: Staff@2025');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        return 0;
    }
}

