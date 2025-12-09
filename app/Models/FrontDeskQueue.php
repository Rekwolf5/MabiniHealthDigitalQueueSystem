<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FrontDeskQueue extends Model
{
    protected $fillable = [
        'queue_number',
        'patient_name',
        'contact_number',
        'age',
        'gender',
        'chief_complaint',
        'symptoms',
        'allergies',
        'service_id',
        'priority',
        'urgency_level',
        'status',
        'workflow_stage',
        'arrived_at',
        'called_at',
        'vitals_taken_at',
        'consultation_started_at',
        'completed_at',
        'assigned_staff_id',
        'notes'
    ];

    protected $casts = [
        'arrived_at' => 'datetime',
        'called_at' => 'datetime',
        'vitals_taken_at' => 'datetime',
        'consultation_started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    /**
     * Boot function to auto-generate queue number
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($queue) {
            if (!$queue->queue_number) {
                $queue->queue_number = self::generateQueueNumber($queue->service_id, $queue->priority);
            }
        });
    }

    /**
     * Generate unique queue number for today with database locking to prevent race conditions
     */
    public static function generateQueueNumber($serviceId = null, $priority = 'normal')
    {
        return DB::transaction(function () use ($serviceId, $priority) {
            $today = Carbon::today();
            
            // Get service abbreviation
            $serviceAbbrev = 'GEN'; // Default
            if ($serviceId) {
                $service = \App\Models\Service::find($serviceId);
                if ($service) {
                    $serviceAbbrev = match(strtolower($service->name)) {
                        'general practitioner' => 'GP',
                        'dental service' => 'DEN',
                        'laboratory service' => 'LAB',
                        'pharmacy' => 'PHR',
                        'maternal & child health' => 'MCH',
                        default => 'GEN'
                    };
                }
            }
            
            // Add priority prefix
            $priorityPrefix = match($priority) {
                'emergency' => 'E-',
                'senior' => 'S-',
                'pwd' => 'P-',
                default => ''
            };
            
            $basePrefix = $priorityPrefix . $serviceAbbrev . '-';
            
            // Find last queue number for this service and priority today WITH LOCK
            // Check BOTH active queue AND archive to prevent number reuse
            $lastQueue = self::whereDate('arrived_at', $today)
                ->where('queue_number', 'like', $basePrefix . '%')
                ->orderBy('queue_number', 'desc')
                ->lockForUpdate()  // CRITICAL: Prevents race conditions
                ->first();
            
            // Also check archive table for today's numbers
            $lastArchivedQueue = \App\Models\QueueArchive::whereDate('arrived_at', $today)
                ->where('queue_number', 'like', $basePrefix . '%')
                ->orderBy('queue_number', 'desc')
                ->lockForUpdate()
                ->first();
                
            // Get the highest number from either active or archived queues
            $lastNumber = 0;
            
            if ($lastQueue) {
                $parts = explode('-', $lastQueue->queue_number);
                $lastNumber = max($lastNumber, (int) end($parts));
            }
            
            if ($lastArchivedQueue) {
                $parts = explode('-', $lastArchivedQueue->queue_number);
                $lastNumber = max($lastNumber, (int) end($parts));
            }
            
            // Generate next number and verify uniqueness
            $attempts = 0;
            do {
                $newNumber = str_pad($lastNumber + 1 + $attempts, 3, '0', STR_PAD_LEFT);
                $queueNumber = $basePrefix . $newNumber;
                
                // Check if this number already exists (safety check)
                $existsInActive = self::where('queue_number', $queueNumber)->exists();
                $existsInArchive = \App\Models\QueueArchive::where('queue_number', $queueNumber)->exists();
                
                if (!$existsInActive && !$existsInArchive) {
                    break; // Found unique number
                }
                
                $attempts++;
            } while ($attempts < 50);
            
            return $queueNumber;
        });
    }

    /**
     * Get the service this queue belongs to
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Scope for waiting patients
     */
    public function scopeWaiting($query)
    {
        return $query->where('status', 'waiting');
    }

    /**
     * Scope for today's queue
     */
    public function scopeToday($query)
    {
        return $query->whereDate('arrived_at', Carbon::today());
    }

    /**
     * Get priority badge class
     */
    public function getPriorityBadgeClass()
    {
        return match($this->priority) {
            'emergency' => 'bg-red-500',
            'senior' => 'bg-purple-500',
            'pwd' => 'bg-blue-500',
            default => 'bg-gray-500'
        };
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'waiting' => 'bg-yellow-500',
            'called' => 'bg-blue-500',
            'in_progress' => 'bg-purple-500',
            'completed' => 'bg-green-500',
            'cancelled' => 'bg-red-500',
            default => 'bg-gray-500'
        };
    }

    /**
     * Mark as called
     */
    public function markAsCalled()
    {
        $this->update([
            'status' => 'called',
            'called_at' => Carbon::now()
        ]);
    }

    /**
     * Mark as completed
     */
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'workflow_stage' => 'discharge',
            'completed_at' => Carbon::now()
        ]);
    }

    /**
     * Get the assigned staff member
     */
    public function assignedStaff()
    {
        return $this->belongsTo(User::class, 'assigned_staff_id');
    }

    /**
     * Get the medical record for this queue
     */
    public function medicalRecord()
    {
        return $this->hasOne(PatientMedicalRecord::class, 'queue_id');
    }

    /**
     * Get all service encounters for this queue
     */
    public function serviceEncounters()
    {
        return $this->hasMany(ServiceEncounter::class, 'queue_id')->orderBy('created_at');
    }

    /**
     * Get all prescriptions for this queue
     */
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'queue_id');
    }

    /**
     * Get all lab orders for this queue
     */
    public function labOrders()
    {
        return $this->hasMany(LabOrder::class, 'queue_id');
    }

    /**
     * Get workflow stage display name
     */
    public function getWorkflowStageDisplayAttribute()
    {
        return match($this->workflow_stage) {
            'registration' => 'Registration Complete',
            'vitals' => 'Vital Signs Check',
            'consultation' => 'Doctor Consultation',
            'treatment' => 'Treatment/Procedure',
            'discharge' => 'Ready for Discharge',
            default => ucfirst($this->workflow_stage)
        };
    }

    /**
     * Get urgency level badge class
     */
    public function getUrgencyBadgeClass()
    {
        return match($this->urgency_level) {
            'emergency' => 'bg-red-500',
            'urgent' => 'bg-orange-500',
            'routine' => 'bg-green-500',
            default => 'bg-gray-500'
        };
    }

    /**
     * Advance to next workflow stage
     */
    public function advanceWorkflowStage($new_stage, $staff_id = null)
    {
        $updates = ['workflow_stage' => $new_stage];
        
        if ($staff_id) {
            $updates['assigned_staff_id'] = $staff_id;
        }

        // Update specific timestamps based on stage
        switch ($new_stage) {
            case 'vitals':
                $updates['vitals_taken_at'] = Carbon::now();
                break;
            case 'consultation':
                $updates['consultation_started_at'] = Carbon::now();
                break;
        }

        $this->update($updates);
    }
}
