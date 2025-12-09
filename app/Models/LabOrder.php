<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LabOrder extends Model
{
    protected $fillable = [
        'queue_id', 'ordered_by', 'test_name', 'test_code',
        'clinical_indication', 'special_instructions', 'priority',
        'specimen_type', 'status', 'collected_by', 'collected_at',
        'processed_by', 'processed_at', 'completed_at', 'results',
        'result_values', 'interpretation', 'reference_ranges',
        'result_status', 'result_file_path', 'requires_follow_up',
        'follow_up_recommendations', 'reviewed_by', 'reviewed_at'
    ];

    protected $casts = [
        'result_values' => 'array',
        'collected_at' => 'datetime',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'requires_follow_up' => 'boolean'
    ];

    /**
     * Get the queue this lab order belongs to
     */
    public function queue()
    {
        return $this->belongsTo(FrontDeskQueue::class);
    }

    /**
     * Get the doctor who ordered this test
     */
    public function orderedBy()
    {
        return $this->belongsTo(User::class, 'ordered_by');
    }

    /**
     * Get the lab staff who collected specimen
     */
    public function collectedBy()
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    /**
     * Get the lab tech who processed the test
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Get the doctor who reviewed results
     */
    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Mark specimen as collected
     */
    public function markAsCollected($staff_id)
    {
        $this->update([
            'status' => 'collected',
            'collected_by' => $staff_id,
            'collected_at' => Carbon::now()
        ]);
    }

    /**
     * Start processing
     */
    public function startProcessing($staff_id)
    {
        $this->update([
            'status' => 'processing',
            'processed_by' => $staff_id,
            'processed_at' => Carbon::now()
        ]);
    }

    /**
     * Complete with results
     */
    public function completeWithResults($results, $result_values = null, $interpretation = null, $result_status = 'normal')
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => Carbon::now(),
            'results' => $results,
            'result_values' => $result_values,
            'interpretation' => $interpretation,
            'result_status' => $result_status
        ]);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-500',
            'collected' => 'bg-blue-500',
            'processing' => 'bg-purple-500',
            'completed' => 'bg-green-500',
            'cancelled' => 'bg-red-500',
            default => 'bg-gray-500'
        };
    }

    /**
     * Get result status badge class
     */
    public function getResultStatusBadgeClass()
    {
        return match($this->result_status) {
            'normal' => 'bg-green-500',
            'abnormal' => 'bg-yellow-500',
            'critical' => 'bg-red-500',
            default => 'bg-gray-500'
        };
    }

    /**
     * Check if results are ready
     */
    public function hasResults()
    {
        return $this->status === 'completed' && !empty($this->results);
    }
}
