<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Process queue cutoff at closing time (default 5:00 PM)
        // This marks remaining queues as "Unattended" when health center closes
        $schedule->command('queue:process-cutoff')->dailyAt('17:00');
        
        // Clean up old queues at midnight - marks yesterday's unfinished queues
        $schedule->command('app:clean-up-old-queues')->dailyAt('00:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
