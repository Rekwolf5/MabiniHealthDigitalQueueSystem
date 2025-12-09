<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineDispensed extends Model
{
    use HasFactory;

    protected $table = 'medicine_dispensed';

    protected $fillable = [
        'consultation_id',
        'queue_id',
        'medicine_id',
        'batch_id',
        'quantity',
        'instructions',
        'dispensed_by',
        'dispensed_at',
        'status',
    ];

    protected $casts = [
        'dispensed_at' => 'datetime',
    ];

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    public function queue()
    {
        return $this->belongsTo(Queue::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function batch()
    {
        return $this->belongsTo(MedicineBatch::class, 'batch_id');
    }

    public function dispensedBy()
    {
        return $this->belongsTo(User::class, 'dispensed_by');
    }

    public function dispensedByUser()
    {
        return $this->belongsTo(User::class, 'dispensed_by');
    }
}
