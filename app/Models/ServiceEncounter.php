<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ServiceEncounter extends Model
{
    protected $fillable = [
        'queue_id', 'service_id', 'staff_id', 'encounter_type', 'status',
        'started_at', 'completed_at', 'duration_minutes', 'vital_signs',
        'findings', 'actions_taken', 'recommendations', 'notes',
        'referred_to_service', 'requires_follow_up', 'follow_up_date'
    ];

    protected $casts = [
        'vital_signs' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'follow_up_date' => 'date',
        'requires_follow_up' => 'boolean'
    ];

    /**
     * Get the queue this encounter belongs to
     */
    public function queue()
    {
        return $this->belongsTo(FrontDeskQueue::class);
    }

    /**
     * Get the service this encounter is for
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the staff handling this encounter
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**
     * Get the service this patient was referred to
     */
    public function referredToService()
    {
        return $this->belongsTo(Service::class, 'referred_to_service');
    }

    /**
     * Start this encounter
     */
    public function start()
    {
        $this->update([
            'status' => 'in_progress',
            'started_at' => Carbon::now()
        ]);
    }

    /**
     * Complete this encounter
     */
    public function complete()
    {
        $completed_at = Carbon::now();
        $duration = $this->started_at ? $this->started_at->diffInMinutes($completed_at) : 0;
        
        $this->update([
            'status' => 'completed',
            'completed_at' => $completed_at,
            'duration_minutes' => $duration
        ]);
    }

    /**
     * Get encounter type display name
     */
    public function getEncounterTypeDisplayAttribute()
    {
        return match($this->encounter_type) {
            'vitals' => 'Vital Signs Check',
            'consultation' => 'Doctor Consultation',
            'treatment' => 'Treatment/Procedure',
            'lab_work' => 'Laboratory Work',
            'pharmacy' => 'Pharmacy Dispensing',
            'follow_up' => 'Follow-up Visit',
            default => ucfirst($this->encounter_type)
        };
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-500',
            'in_progress' => 'bg-blue-500',
            'completed' => 'bg-green-500',
            'cancelled' => 'bg-red-500',
            default => 'bg-gray-500'
        };
    }
}
