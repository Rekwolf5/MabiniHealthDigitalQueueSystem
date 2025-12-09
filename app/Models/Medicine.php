<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'dosage',
        'type',
        'stock',
        'unit_price',
        'reorder_level',
        'expiry_date',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'stock' => 'integer',
        'unit_price' => 'decimal:2',
        'reorder_level' => 'integer',
    ];

    // Default attributes
    protected $attributes = [
        'reorder_level' => 15,
    ];

    /**
     * Relationship: Medicine has many batches
     */
    public function batches()
    {
        return $this->hasMany(MedicineBatch::class);
    }

    /**
     * Get active batches (not depleted, not expired)
     */
    public function activeBatches()
    {
        return $this->hasMany(MedicineBatch::class)
                    ->where('quantity', '>', 0)
                    ->where('expiry_date', '>=', now())
                    ->orderBy('expiry_date', 'asc'); // FEFO
    }

    /**
     * Get total stock from all active batches
     * This will be the new way to calculate stock
     */
    public function getTotalStockFromBatchesAttribute()
    {
        return $this->batches()->sum('quantity');
    }

    /**
     * Get earliest expiry date from active batches
     */
    public function getEarliestExpiryDateAttribute()
    {
        return $this->activeBatches()->min('expiry_date');
    }

    /**
     * Check if any batch is expired
     */
    public function getHasExpiredBatchesAttribute()
    {
        return $this->batches()
                    ->where('expiry_date', '<', now())
                    ->where('quantity', '>', 0)
                    ->exists();
    }

    /**
     * Check if any batch is expiring soon
     */
    public function getHasExpiringSoonBatchesAttribute()
    {
        $thirtyDaysFromNow = now()->addDays(30);
        return $this->batches()
                    ->where('quantity', '>', 0)
                    ->where('expiry_date', '>=', now())
                    ->where('expiry_date', '<=', $thirtyDaysFromNow)
                    ->exists();
    }

    // Accessor to use 'quantity' as alias for 'stock' (for compatibility)
    public function getQuantityAttribute()
    {
        return $this->stock;
    }

    // Three-tier status system with batch awareness
    public function getStatusAttribute()
    {
        // Use batch-based stock if batches exist, otherwise use legacy stock field
        $totalStock = $this->batches()->exists() 
            ? $this->total_stock_from_batches 
            : $this->stock;
        
        // Priority 1: Check if out of stock
        if ($totalStock == 0) {
            return 'Out of Stock';
        }
        
        // Priority 2: Check if any batch is expired
        if ($this->has_expired_batches) {
            return 'Has Expired Batches';
        }
        
        // Priority 3: Check if expiring soon (check batches first, then legacy field)
        if ($this->batches()->exists()) {
            if ($this->has_expiring_soon_batches) {
                return 'Expiring Soon';
            }
        } elseif ($this->expiry_date) {
            $expiryDate = \Carbon\Carbon::parse($this->expiry_date);
            $daysUntilExpiry = now()->diffInDays($expiryDate, false);
            
            if ($daysUntilExpiry >= 0 && $daysUntilExpiry <= 30) {
                return 'Expiring Soon';
            }
        }
        
        // Priority 4: Check stock levels (three-tier)
        if ($totalStock <= 5) {
            return 'Critical'; // Red alert - 0-5 units
        } elseif ($totalStock <= ($this->reorder_level ?? 15)) {
            return 'Low Stock'; // Yellow warning - 6-15 units
        }
        
        return 'In Stock'; // Green - adequate supply
    }

    public function getIsExpiredAttribute()
    {
        return $this->expiry_date && \Carbon\Carbon::parse($this->expiry_date)->isPast();
    }

    public function getExpiresInDaysAttribute()
    {
        if (!$this->expiry_date) {
            return null;
        }
        
        return now()->diffInDays($this->expiry_date, false);
    }
    
    // Helper to get status color
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'Out of Stock' => 'danger',
            'Critical' => 'danger',
            'Low Stock' => 'warning',
            'Expired' => 'danger',
            'Has Expired Batches' => 'danger',
            'Expiring Soon' => 'warning',
            'In Stock' => 'success',
            default => 'secondary',
        };
    }
}
