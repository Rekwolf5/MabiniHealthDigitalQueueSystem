<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Queue;
use App\Models\QueueCounter;
use App\Models\QueueArchive;
use Carbon\Carbon;
class CleanUpOldQueues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-up-old-queues';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark old queues as unattended or no show at midnight, clean up old counters, and archive old completed queues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $yesterday = Carbon::yesterday();

        // Mark "waiting" patients as unattended
        $waitingCount = Queue::whereDate('created_at', '<', Carbon::today())
            ->where('status', 'waiting')
            ->update(['status' => 'unattended']);

        // Mark "skipped" patients as no show
        $skippedCount = Queue::whereDate('created_at', '<', Carbon::today())
            ->where('status', 'skipped')
            ->update(['status' => 'no show']);

        // Clean up old queue counters (keeps last 7 days)
        QueueCounter::resetDaily();

        // Archive old completed queues (older than 30 days)
        $archivedCount = QueueArchive::archiveOldQueues(30);

        // Console feedback
        $this->info("✓ $waitingCount waiting patients marked as 'unattended'.");
        $this->info("✓ $skippedCount skipped patients marked as 'no show'.");
        $this->info("✓ Queue counters cleaned up. New day starts with fresh numbering.");
        $this->info("✓ $archivedCount old queues archived and removed from active queue.");
        
        return Command::SUCCESS;
    }
}
