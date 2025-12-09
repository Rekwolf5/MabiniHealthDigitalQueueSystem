<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QueueArchive extends Model
{
    protected $table = 'queue_archive';
    
    protected $fillable = [
        'original_queue_id',
        'patient_id',
        'service_id',
        'queue_number',
        'patient_name',
        'contact_number',
        'age',
        'chief_complaint',
        'allergies',
        'priority',
        'patient_category',
        'status',
        'service_type',
        'urgency_level',
        'workflow_stage',
        'notes',
        'qr_code',
        'verification_token',
        'arrived_at',
        'called_at',
        'started_at',
        'completed_at',
        'assigned_staff_id',
        'queue_created_at',
        'queue_updated_at',
        'archived_at',
        'archived_reason',
        'archived_by',
    ];

    protected $casts = [
        'arrived_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'queue_created_at' => 'datetime',
        'queue_updated_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    /**
     * Get the patient associated with this archived queue.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Archive old completed queues (older than specified days)
     * 
     * @param int $daysOld Number of days old to archive (default 30)
     * @return int Number of queues archived
     */
    public static function archiveOldQueues(int $daysOld = 30): int
    {
        $cutoffDate = now()->subDays($daysOld);
        
        // Get completed/skipped/unattended/no-show queues older than cutoff
        $oldQueues = Queue::whereIn('status', ['Completed', 'Skipped', 'Unattended', 'No Show'])
            ->where('created_at', '<', $cutoffDate)
            ->get();
        
        $archivedCount = 0;
        
        foreach ($oldQueues as $queue) {
            // Create archive record
            self::create([
                'original_queue_id' => $queue->id,
                'patient_id' => $queue->patient_id,
                'queue_number' => $queue->queue_number,
                'priority' => $queue->priority,
                'patient_category' => $queue->patient_category ?? 'Regular',
                'status' => $queue->status,
                'service_type' => $queue->service_type,
                'notes' => $queue->notes,
                'qr_code' => $queue->qr_code,
                'verification_token' => $queue->verification_token,
                'arrived_at' => $queue->arrived_at,
                'started_at' => $queue->started_at,
                'completed_at' => $queue->completed_at,
                'queue_created_at' => $queue->created_at,
                'queue_updated_at' => $queue->updated_at,
                'archived_at' => now(),
                'archived_reason' => "Auto-archived after {$daysOld} days",
            ]);
            
            // Delete the original queue
            $queue->delete();
            
            $archivedCount++;
        }
        
        return $archivedCount;
    }
}
