<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class QueueCounter extends Model
{
    protected $fillable = [
        'date',
        'service_type',
        'priority_lane',
        'counter',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the next queue number for a given service and priority
     */
    public static function getNextNumber($serviceType, $priorityLane)
    {
        $today = Carbon::today();

        // Get or create counter for today
        $counter = self::firstOrCreate(
            [
                'date' => $today,
                'service_type' => $serviceType,
                'priority_lane' => $priorityLane,
            ],
            [
                'counter' => 0,
            ]
        );

        // Increment counter
        $counter->increment('counter');

        return $counter->counter;
    }

    /**
     * Reset all counters (called at midnight)
     */
    public static function resetDaily()
    {
        // Delete old counters (older than 7 days)
        self::where('date', '<', Carbon::today()->subDays(7))->delete();
        
        return true;
    }
}
