<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PatientMedicalRecord extends Model
{
    protected $fillable = [
        'queue_id', 'patient_name', 'contact_number', 'age', 'gender', 
        'address', 'date_of_birth', 'chief_complaint', 'present_illness',
        'past_medical_history', 'allergies', 'current_medications', 
        'social_history', 'family_history', 'vital_signs', 'assessment',
        'plan', 'notes', 'service_id', 'created_by', 'updated_by'
    ];

    protected $casts = [
        'vital_signs' => 'array',
        'date_of_birth' => 'date'
    ];

    /**
     * Get the queue this record belongs to
     */
    public function queue()
    {
        return $this->belongsTo(FrontDeskQueue::class);
    }

    /**
     * Get the service this record is for
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the staff who created this record
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the staff who last updated this record
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get formatted vital signs
     */
    public function getFormattedVitalSigns()
    {
        if (!$this->vital_signs) return null;
        
        $vitals = $this->vital_signs;
        return [
            'blood_pressure' => $vitals['systolic'] . '/' . $vitals['diastolic'] . ' mmHg',
            'temperature' => $vitals['temperature'] . '°C',
            'pulse' => $vitals['pulse'] . ' bpm',
            'respiratory_rate' => $vitals['respiratory_rate'] . ' /min',
            'weight' => $vitals['weight'] . ' kg',
            'height' => $vitals['height'] . ' cm',
            'bmi' => round($vitals['weight'] / (($vitals['height']/100) ** 2), 1)
        ];
    }

    /**
     * Check if vital signs are within normal ranges
     */
    public function hasAbnormalVitals()
    {
        if (!$this->vital_signs) return false;
        
        $vitals = $this->vital_signs;
        
        // Basic normal ranges (can be customized per age/gender)
        return (
            $vitals['systolic'] > 140 || $vitals['systolic'] < 90 ||
            $vitals['diastolic'] > 90 || $vitals['diastolic'] < 60 ||
            $vitals['temperature'] > 37.5 || $vitals['temperature'] < 36 ||
            $vitals['pulse'] > 100 || $vitals['pulse'] < 60 ||
            $vitals['respiratory_rate'] > 20 || $vitals['respiratory_rate'] < 12
        );
    }
}
