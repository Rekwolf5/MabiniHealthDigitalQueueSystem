<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'prefix',
        'age',
        'gender',
        'contact',
        'address',
        'date_of_birth',
        'emergency_contact',
        'patient_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * Get the queues for the patient.
     */
    public function queues(): HasMany
    {
        return $this->hasMany(Queue::class);
    }

    /**
     * Get the medical records for the patient.
     */
    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }

    /**
     * Get the full name attribute.
     */
    public function getFullNameAttribute(): string
    {
        $name = trim(($this->prefix ? $this->prefix . ' ' : '') . 
                    $this->first_name . ' ' . 
                    ($this->middle_name ? $this->middle_name . ' ' : '') . 
                    $this->last_name);
        return $name;
    }
}
