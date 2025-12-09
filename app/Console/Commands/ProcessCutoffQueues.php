<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Queue;
use App\Models\SystemSetting;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\QueueCutoffNotification;

class ProcessCutoffQueues extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'queue:process-cutoff {--force : Force process cutoff regardless of time}';

    /**
     * The console command description.
     */
    protected $description = 'Mark unattended queues when health center reaches cutoff time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Get cutoff time from system settings (default 5:00 PM)
            $cutoffTime = SystemSetting::where('key', 'queue_cutoff_time')->value('value') ?? '17:00';
            $now = Carbon::now();
            $cutoffDateTime = Carbon::today()->setTimeFromTimeString($cutoffTime);

            // Check if we should process cutoff
            if (!$this->option('force') && $now->lt($cutoffDateTime)) {
                $this->info("Not yet cutoff time. Current: {$now->format('H:i')}, Cutoff: {$cutoffTime}");
                return Command::SUCCESS;
            }

            // Get today's queues that are still waiting or skipped
            $affectedQueues = Queue::whereDate('created_at', Carbon::today())
                ->whereIn('status', ['Waiting', 'Skipped', 'Pending'])
                ->get();

            if ($affectedQueues->isEmpty()) {
                $this->info('No queues to process for cutoff.');
                return Command::SUCCESS;
            }

            $count = 0;

            foreach ($affectedQueues as $queue) {
                // Mark as Unattended
                $queue->update(['status' => 'Unattended']);

                // Send notification to patient
                try {
                    if ($queue->patient && $queue->patient->patientAccount) {
                        $queue->patient->patientAccount->notify(
                            new QueueCutoffNotification($queue)
                        );
                    }
                } catch (\Exception $e) {
                    Log::warning("Failed to send cutoff notification for queue {$queue->queue_number}: " . $e->getMessage());
                }

                // Log activity
                try {
                    ActivityLog::create([
                        'user_id' => $queue->patient_id,
                        'user_type' => 'patient',
                        'action' => 'queue_cutoff',
                        'description' => "Queue {$queue->queue_number} marked as Unattended due to cutoff time",
                        'ip_address' => '127.0.0.1',
                        'user_agent' => 'System Command',
                    ]);
                } catch (\Exception $e) {
                    Log::warning("Failed to log cutoff activity: " . $e->getMessage());
                }

                $count++;
            }

            $this->info("✓ Processed cutoff: {$count} queues marked as 'Unattended'");
            $this->info("✓ Cutoff time: {$cutoffTime}");
            $this->info("✓ Patients have been notified to return tomorrow.");

            // Log system-level activity
            Log::info("Queue cutoff processed: {$count} queues marked as Unattended at " . $now->format('Y-m-d H:i:s'));

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Error processing cutoff: " . $e->getMessage());
            Log::error("Queue cutoff processing failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
