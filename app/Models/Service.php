<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Service extends Model
{
    protected $fillable = [
        'name',
        'description',
        'capacity_per_hour',
        'operating_hours',
        'is_active',
        'available_today',
        'unavailable_reason',
        'daily_patient_limit',
        'current_patient_count',
        'start_time',
        'end_time',
        'settings_updated_date',
        'updated_by_staff_id'
    ];

    protected $casts = [
        'operating_hours' => 'array',
        'is_active' => 'boolean',
        'available_today' => 'boolean',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'settings_updated_date' => 'date',
    ];

    /**
     * Get front desk queues for this service
     */
    public function frontDeskQueues()
    {
        return $this->hasMany(FrontDeskQueue::class);
    }

    /**
     * Get all staff members assigned to this service
     */
    public function staff()
    {
        return $this->hasMany(User::class, 'service_id')
                    ->where('role', 'staff');
    }

    /**
     * Get current waiting queue for this service
     */
    public function currentQueue()
    {
        return $this->frontDeskQueues()
            ->whereIn('status', ['waiting', 'called', 'in_progress'])
            ->orderBy('priority', 'desc')
            ->orderBy('arrived_at');
    }

    /**
     * Calculate current capacity usage
     */
    public function getCurrentCapacity()
    {
        $now = Carbon::now();
        $hourStart = $now->startOfHour();
        
        return $this->frontDeskQueues()
            ->where('arrived_at', '>=', $hourStart)
            ->whereIn('status', ['waiting', 'called', 'in_progress', 'completed'])
            ->count();
    }

    /**
     * Check if service is available for new patients
     */
    public function isAvailable()
    {
        if (!$this->is_active) {
            return false;
        }

        return $this->getCurrentCapacity() < $this->capacity_per_hour;
    }

    /**
     * Get estimated wait time in minutes
     */
    /**
     * Get the staff member who last updated service settings
     */
    public function updatedByStaff()
    {
        return $this->belongsTo(User::class, 'updated_by_staff_id');
    }

    /**
     * Check if service is available for new patients (enhanced version)
     */
    public function isAvailableForNewPatients()
    {
        if (!$this->is_active || !$this->available_today) {
            return false;
        }

        // Check if within operating hours
        $currentTime = now()->format('H:i:s');
        if ($this->start_time && $this->end_time) {
            if ($currentTime < $this->start_time->format('H:i:s') || $currentTime > $this->end_time->format('H:i:s')) {
                return false;
            }
        }

        // Check daily patient limit
        if ($this->daily_patient_limit && $this->current_patient_count >= $this->daily_patient_limit) {
            return false;
        }

        // Check hourly capacity (legacy support)
        if ($this->capacity_per_hour && $this->getCurrentCapacity() >= $this->capacity_per_hour) {
            return false;
        }

        return true;
    }

    /**
     * Get comprehensive availability status
     */
    public function getAvailabilityStatus()
    {
        if (!$this->is_active) {
            return ['status' => 'inactive', 'message' => 'Service is inactive', 'color' => 'gray'];
        }

        if (!$this->available_today) {
            return ['status' => 'unavailable', 'message' => $this->unavailable_reason ?: 'Service unavailable today', 'color' => 'red'];
        }

        $currentTime = now()->format('H:i:s');
        if ($this->start_time && $currentTime < $this->start_time->format('H:i:s')) {
            return ['status' => 'not_started', 'message' => 'Service starts at ' . $this->start_time->format('g:i A'), 'color' => 'yellow'];
        }

        if ($this->end_time && $currentTime > $this->end_time->format('H:i:s')) {
            return ['status' => 'closed', 'message' => 'Service closed for today', 'color' => 'red'];
        }

        if ($this->daily_patient_limit && $this->current_patient_count >= $this->daily_patient_limit) {
            return ['status' => 'full', 'message' => 'Daily limit reached (' . $this->daily_patient_limit . ' patients)', 'color' => 'red'];
        }

        $remaining = $this->daily_patient_limit ? ($this->daily_patient_limit - $this->current_patient_count) : '∞';
        return ['status' => 'available', 'message' => 'Available - ' . $remaining . ' slots remaining', 'color' => 'green'];
    }

    /**
     * Increment patient count when new patient is added
     */
    public function incrementPatientCount()
    {
        $this->increment('current_patient_count');
    }

    /**
     * Decrement patient count when patient is removed/cancelled
     */
    public function decrementPatientCount()
    {
        if ($this->current_patient_count > 0) {
            $this->decrement('current_patient_count');
        }
    }

    /**
     * Reset daily counters (called by daily job or manually)
     */
    public function resetDailyCounters()
    {
        $this->update([
            'current_patient_count' => 0,
            'available_today' => true, // Reset to available unless manually set
        ]);
    }

    /**
     * Update service availability settings
     */
    public function updateAvailabilitySettings(array $settings, $staffId)
    {
        $settings['settings_updated_date'] = now()->toDateString();
        $settings['updated_by_staff_id'] = $staffId;
        
        $this->update($settings);
    }

    /**
     * Get today's queue statistics
     */
    public function getTodayStats()
    {
        $today = now()->startOfDay();
        
        return [
            'total_patients' => $this->frontDeskQueues()->whereDate('arrived_at', $today)->count(),
            'completed' => $this->frontDeskQueues()->whereDate('arrived_at', $today)->where('status', 'completed')->count(),
            'waiting' => $this->frontDeskQueues()->whereDate('arrived_at', $today)->whereIn('status', ['waiting', 'called', 'in_progress'])->count(),
            'cancelled' => $this->frontDeskQueues()->whereDate('arrived_at', $today)->where('status', 'cancelled')->count(),
        ];
    }

    /**
     * Calculate real-time average service time based on today's completed queues
     */
    public function getAverageServiceTime()
    {
        $today = now()->startOfDay();
        
        $completedQueues = $this->frontDeskQueues()
            ->whereDate('arrived_at', $today)
            ->where('status', 'completed')
            ->whereNotNull('called_at')
            ->whereNotNull('completed_at')
            ->get();
        
        if ($completedQueues->isEmpty()) {
            // Fallback to system setting or default 15 minutes
            return SystemSetting::get('avg_consultation_time', 15);
        }
        
        $totalMinutes = 0;
        $count = 0;
        
        foreach ($completedQueues as $queue) {
            $startTime = Carbon::parse($queue->called_at);
            $endTime = Carbon::parse($queue->completed_at);
            $totalMinutes += $startTime->diffInMinutes($endTime);
            $count++;
        }
        
        return $count > 0 ? round($totalMinutes / $count) : 15;
    }

    /**
     * Calculate dynamic capacity based on real-time data
     */
    public function calculateDynamicCapacity()
    {
        $now = Carbon::now();
        
        // Get operating hours from system settings
        $openingTime = Carbon::parse(SystemSetting::get('opening_time', '08:00'));
        $closingTime = Carbon::parse(SystemSetting::get('closing_time', '17:00'));
        $cutoffTime = Carbon::parse(SystemSetting::get('queue_cutoff_time', '16:00'));
        
        // If service has specific times, use those
        if ($this->start_time) {
            $openingTime = Carbon::parse($this->start_time);
        }
        if ($this->end_time) {
            $closingTime = Carbon::parse($this->end_time);
        }
        
        // Calculate remaining time until cutoff
        $remainingMinutes = $now->diffInMinutes($cutoffTime, false);
        
        // If past cutoff or closed, no capacity
        if ($remainingMinutes <= 0 || $now->greaterThan($closingTime)) {
            return [
                'available_slots' => 0,
                'reason' => 'Queue cutoff time has passed',
                'avg_service_time' => $this->getAverageServiceTime(),
                'current_waiting' => $this->getCurrentWaitingCount(),
            ];
        }
        
        // Get average service time from today's data
        $avgServiceTime = $this->getAverageServiceTime();
        
        // Get current waiting + in-progress count
        $currentWaiting = $this->getCurrentWaitingCount();
        
        // Calculate time needed for current queue
        $timeForCurrentQueue = $currentWaiting * $avgServiceTime;
        
        // Calculate remaining time after serving current queue
        $availableTime = max(0, $remainingMinutes - $timeForCurrentQueue);
        
        // Calculate how many more patients can be served
        $additionalSlots = floor($availableTime / $avgServiceTime);
        
        // Apply daily limit if set
        if ($this->daily_patient_limit) {
            $todayCount = $this->frontDeskQueues()
                ->whereDate('arrived_at', now()->startOfDay())
                ->whereIn('status', ['waiting', 'called', 'in_progress', 'completed'])
                ->count();
            
            $limitRemaining = max(0, $this->daily_patient_limit - $todayCount);
            $additionalSlots = min($additionalSlots, $limitRemaining);
        }
        
        return [
            'available_slots' => max(0, $additionalSlots),
            'avg_service_time' => $avgServiceTime,
            'current_waiting' => $currentWaiting,
            'estimated_wait_time' => $timeForCurrentQueue,
            'cutoff_time' => $cutoffTime->format('H:i'),
            'remaining_minutes' => max(0, $remainingMinutes),
            'daily_limit_remaining' => $this->daily_patient_limit ? ($this->daily_patient_limit - ($todayCount ?? 0)) : null,
        ];
    }

    /**
     * Get current waiting count (waiting + called + in_progress)
     */
    public function getCurrentWaitingCount()
    {
        return $this->frontDeskQueues()
            ->whereDate('arrived_at', now()->startOfDay())
            ->whereIn('status', ['waiting', 'called', 'in_progress'])
            ->count();
    }

    /**
     * Check if service can accept new patients
     */
    public function canAcceptPatients()
    {
        if (!$this->is_active || !$this->available_today) {
            return false;
        }
        
        $capacity = $this->calculateDynamicCapacity();
        return $capacity['available_slots'] > 0;
    }

    /**
     * Get estimated wait time for new patient
     */
    public function getEstimatedWaitTime()
    {
        $capacity = $this->calculateDynamicCapacity();
        return $capacity['estimated_wait_time'] ?? 0;
    }
}

