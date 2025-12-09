<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'queue_id',
        'doctor_id',
        'chief_complaint',
        'blood_pressure',
        'temperature',
        'pulse_rate',
        'weight',
        'height',
        'diagnosis',
        'symptoms',
        'physical_examination',
        'treatment',
        'prescription',
        'prescribed_medicines',
        'prescription_dispensed',
        'notes',
        'follow_up_date',
        'doctor_notes'
    ];

    protected $casts = [
        'follow_up_date' => 'date',
        'prescription_dispensed' => 'boolean',
        'prescribed_medicines' => 'array',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function queue()
    {
        return $this->belongsTo(Queue::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function medicinesDispensed()
    {
        return $this->hasMany(MedicineDispensed::class);
    }
}

