<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:reset-passwords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset admin and staff passwords to default';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Resetting passwords for admin and staff accounts...');
        $this->newLine();
        
        // Reset admin password
        $admin = User::where('email', 'admin@mabini.com')->first();
        if ($admin) {
            $admin->password = Hash::make('Admin@2025');
            $admin->save();
            $this->info('✅ Admin password reset');
        } else {
            $this->warn('⚠️  Admin account not found');
        }
        
        // Reset staff password
        $staff = User::where('email', 'staff@mabini.com')->first();
        if ($staff) {
            $staff->password = Hash::make('Staff@2025');
            $staff->save();
            $this->info('✅ Staff password reset');
        } else {
            $this->warn('⚠️  Staff account not found');
        }
        
        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('🔐 UPDATED LOGIN CREDENTIALS:');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('ADMIN:');
        $this->info('  Email: admin@mabini.com');
        $this->info('  Password: Admin@2025');
        $this->newLine();
        $this->info('STAFF:');
        $this->info('  Email: staff@mabini.com');
        $this->info('  Password: Staff@2025');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();
        $this->info('You can now login at: http://localhost:8000/login');
        
        return 0;
    }
}

