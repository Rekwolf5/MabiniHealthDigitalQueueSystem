<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Prescription extends Model
{
    protected $fillable = [
        'queue_id', 'prescribed_by', 'medicine_id', 'medicine_name',
        'dosage', 'frequency', 'duration', 'quantity_prescribed',
        'instructions', 'indication', 'status', 'quantity_dispensed',
        'dispensed_by', 'dispensed_at', 'pharmacy_notes',
        'requires_follow_up', 'follow_up_date'
    ];

    protected $casts = [
        'dispensed_at' => 'datetime',
        'follow_up_date' => 'date',
        'requires_follow_up' => 'boolean'
    ];

    /**
     * Get the queue this prescription belongs to
     */
    public function queue()
    {
        return $this->belongsTo(FrontDeskQueue::class);
    }

    /**
     * Get the doctor who prescribed this
     */
    public function prescribedBy()
    {
        return $this->belongsTo(User::class, 'prescribed_by');
    }

    /**
     * Get the medicine
     */
    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    /**
     * Get the pharmacy staff who dispensed this
     */
    public function dispensedBy()
    {
        return $this->belongsTo(User::class, 'dispensed_by');
    }

    /**
     * Mark as dispensed
     */
    public function markAsDispensed($quantity, $staff_id, $notes = null)
    {
        $this->update([
            'status' => 'dispensed',
            'quantity_dispensed' => $quantity,
            'dispensed_by' => $staff_id,
            'dispensed_at' => Carbon::now(),
            'pharmacy_notes' => $notes
        ]);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-500',
            'dispensed' => 'bg-green-500',
            'cancelled' => 'bg-red-500',
            default => 'bg-gray-500'
        };
    }

    /**
     * Get formatted prescription details
     */
    public function getFormattedPrescriptionAttribute()
    {
        return "{$this->medicine_name} {$this->dosage} - {$this->frequency} for {$this->duration}";
    }

    /**
     * Check if partially dispensed
     */
    public function isPartiallyDispensed()
    {
        return $this->quantity_dispensed > 0 && $this->quantity_dispensed < $this->quantity_prescribed;
    }
}
