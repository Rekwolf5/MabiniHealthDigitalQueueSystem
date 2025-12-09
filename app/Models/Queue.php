<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    use HasFactory;

    protected $table = 'queue';

    protected $fillable = [
        'patient_id',
        'assigned_doctor_id',
        'queue_number',
        'qr_code',
        'verification_token',
        'priority',
        'priority_reason',
        'is_cutoff_priority',
        'cutoff_priority_expires',
        'patient_category',
        'status',
        'requested_date',
        'requested_at',
        'reviewed_at',
        'reviewed_by',
        'approval_status',
        'staff_notes',
        'pwd_id',
        'senior_id',
        'service_type',
        'notes',
        'arrived_at',
        'started_at',
        'completed_at',
        'served_at',
        'doctor_accepted_at',
        'rejection_reason',
        'rejected_by',
        'rejected_at',
    ];

    protected $casts = [
        'arrived_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'served_at' => 'datetime',
        'doctor_accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
        'requested_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'requested_date' => 'date',
        'cutoff_priority_expires' => 'date',
        'is_cutoff_priority' => 'boolean',
    ];

    /**
     * Valid state transitions
     * Each status can only transition to specific next statuses
     */
    protected static $validTransitions = [
        'Pending' => ['Waiting', 'Consulting', 'Skipped', 'Cancelled'],  // Patient requests start as pending
        'Waiting' => ['Consulting', 'Skipped', 'Unattended', 'Cancelled', 'No Show'],
        'Consulting' => ['Completed', 'Waiting', 'Cancelled'], // Can go back to waiting if needed
        'Completed' => [], // Final state
        'Skipped' => ['Waiting', 'No Show', 'Cancelled'], // Can be reactivated or marked as no show
        'Unattended' => [], // Final state
        'No Show' => [], // Final state
        'Cancelled' => [], // Final state
    ];

    /**
     * Boot method to add model events
     */
    protected static function boot()
    {
        parent::boot();

        // Validate state transitions before updating
        static::updating(function ($queue) {
            if ($queue->isDirty('status')) {
                $oldStatus = $queue->getOriginal('status');
                $newStatus = $queue->status;
                
                // Normalize statuses for comparison
                $oldStatusNormalized = ucfirst(strtolower($oldStatus));
                $newStatusNormalized = ucfirst(strtolower($newStatus));
                
                if (!$queue->canTransitionTo($newStatusNormalized, $oldStatus)) {
                    throw new \InvalidArgumentException(
                        "Invalid status transition from '{$oldStatus}' to '{$newStatus}'. " .
                        "Allowed transitions: " . implode(', ', self::$validTransitions[$oldStatusNormalized] ?? [])
                    );
                }

                // Auto-set timestamps based on status change
                if ($newStatusNormalized === 'Consulting' && !$queue->started_at) {
                    $queue->started_at = now();
                }
                
                if ($newStatusNormalized === 'Completed' && !$queue->completed_at) {
                    $queue->completed_at = now();
                }
            }
        });
    }

    /**
     * Check if a status transition is valid
     */
    public function canTransitionTo(string $newStatus, ?string $fromStatus = null): bool
    {
        $currentStatus = $fromStatus ?? $this->status;
        
        // Normalize status to capitalized case for comparison
        $currentStatus = ucfirst(strtolower($currentStatus));
        $newStatus = ucfirst(strtolower($newStatus));
        
        // If status is not changing, it's valid
        if ($currentStatus === $newStatus) {
            return true;
        }
        
        // Check if transition is allowed
        $allowedTransitions = self::$validTransitions[$currentStatus] ?? [];
        return in_array($newStatus, $allowedTransitions);
    }

    /**
     * Get allowed next statuses for current queue
     */
    public function getAllowedNextStatuses(): array
    {
        // Normalize current status to match validTransitions keys
        $currentStatus = ucfirst(strtolower($this->status));
        return self::$validTransitions[$currentStatus] ?? [];
    }

    /**
     * Safely transition to a new status
     */
    public function transitionTo(string $newStatus): bool
    {
        if (!$this->canTransitionTo($newStatus)) {
            return false;
        }
        
        $this->status = $newStatus;
        return $this->save();
    }

    // Patient relationship removed - using FrontDeskQueue instead

    public function assignedDoctor()
    {
        return $this->belongsTo(User::class, 'assigned_doctor_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function consultation()
    {
        return $this->hasOne(Consultation::class);
    }

    public function medicinesDispensed()
    {
        return $this->hasMany(MedicineDispensed::class);
    }

    public function getWaitTimeAttribute()
    {
        $status = ucfirst(strtolower($this->status));
        
        if ($status === 'Waiting') {
            return now()->diffInMinutes($this->arrived_at);
        } elseif ($status === 'Consulting' && $this->started_at) {
            return $this->started_at->diffInMinutes($this->arrived_at);
        } elseif ($status === 'Completed' && $this->started_at) {
            return $this->started_at->diffInMinutes($this->arrived_at);
        }
        return 0;
    }
}
