<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MedicineBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'medicine_id',
        'batch_number',
        'quantity',
        'expiry_date',
        'received_date',
        'supplier',
        'notes',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'received_date' => 'date',
        'quantity' => 'integer',
    ];

    /**
     * Relationship: Batch belongs to a medicine
     */
    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    /**
     * Check if batch is expired
     */
    public function getIsExpiredAttribute()
    {
        return $this->expiry_date && Carbon::parse($this->expiry_date)->isPast();
    }

    /**
     * Get days until expiry
     */
    public function getExpiresInDaysAttribute()
    {
        if (!$this->expiry_date) {
            return null;
        }
        
        return now()->diffInDays($this->expiry_date, false);
    }

    /**
     * Check if batch is expiring soon (within 30 days)
     */
    public function getIsExpiringSoonAttribute()
    {
        if (!$this->expiry_date || $this->is_expired) {
            return false;
        }
        
        $daysUntilExpiry = $this->expires_in_days;
        return $daysUntilExpiry >= 0 && $daysUntilExpiry <= 30;
    }

    /**
     * Get batch status (Expired, Expiring Soon, Good)
     */
    public function getStatusAttribute()
    {
        if ($this->quantity <= 0) {
            return 'Depleted';
        }
        
        if ($this->is_expired) {
            return 'Expired';
        }
        
        if ($this->is_expiring_soon) {
            return 'Expiring Soon';
        }
        
        return 'Good';
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'Depleted' => 'secondary',
            'Expired' => 'danger',
            'Expiring Soon' => 'warning',
            'Good' => 'success',
            default => 'secondary',
        };
    }

    /**
     * Scope: Get active batches (not depleted, not expired)
     */
    public function scopeActive($query)
    {
        return $query->where('quantity', '>', 0)
                    ->where('expiry_date', '>=', now());
    }

    /**
     * Scope: Order by expiry date (FEFO - First Expire First Out)
     */
    public function scopeFEFO($query)
    {
        return $query->orderBy('expiry_date', 'asc');
    }
}
