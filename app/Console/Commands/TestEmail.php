<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email sending functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info('Testing email to: ' . $email);
        
        try {
            Mail::raw('This is a test email from Mabini Health Center Queue System.', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Test Email - Mabini Health Center');
            });
            
            $this->info('✓ Email sent successfully!');
            $this->info('Check your inbox (and spam folder) at: ' . $email);
            return 0;
        } catch (\Exception $e) {
            $this->error('✗ Failed to send email: ' . $e->getMessage());
            return 1;
        }
    }
}
