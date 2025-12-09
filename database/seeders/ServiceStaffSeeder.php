<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceStaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = \App\Models\Service::all();
        
        foreach ($services as $service) {
            // Create staff account for each service
            \App\Models\User::create([
                'name' => $service->name . ' Staff',
                'email' => strtolower(str_replace(' ', '', $service->name)) . '@mabini.com',
                'password' => bcrypt($service->name . '@2025'),
                'role' => 'staff',
                'user_type' => 'service_staff',
                'service_id' => $service->id,
                'email_verified_at' => now(),
            ]);
        }
        
        // Also update the existing admin user to be admin type
        $admin = \App\Models\User::where('email', 'admin@mabini.com')->first();
        if ($admin) {
            $admin->update([
                'user_type' => 'admin',
                'service_id' => null
            ]);
        }
    }
}
