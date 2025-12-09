@extends('layouts.app')

@section('title', 'Consultation - ' . $queue->patient->full_name)

@section('content')
<style>
/* Container width fix to prevent sidebar overlap */
.consultation-form-container {
    max-width: 100%;
    width: 100%;
    margin: 0 auto;
    padding-right: 20px;
    padding-bottom: 60px;
    box-sizing: border-box;
    overflow-x: hidden;
}

/* Prevent any tooltip/title overlays */
input[placeholder]::-webkit-input-placeholder,
textarea[placeholder]::-webkit-input-placeholder {
    position: relative;
    z-index: -1;
}

/* Ensure all form elements are visible */
input, textarea, select {
    position: relative;
    z-index: 1 !important;
    max-width: 100%;
}

/* Remove problematic transitions */
.form-control:focus {
    outline: 2px solid #007bff;
    border-color: #007bff;
}

/* Clean card styling */
.consultation-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
    overflow: visible;
    max-width: 100%;
    box-sizing: border-box;
}

.consultation-card-header {
    padding: 1rem 1.5rem;
    border-bottom: 2px solid #e9ecef;
    background: #f8f9fa;
}

.consultation-card-body {
    padding: 1.5rem;
    overflow-x: hidden;
}

/* Form styling */
.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: block;
    color: #495057;
}

.form-control {
    border: 2px solid #ced4da;
    border-radius: 6px;
    padding: 0.75rem;
    font-size: 1rem;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

.badge-required {
    background: #dc3545;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    margin-left: 0.5rem;
}

.badge-optional {
    background: #6c757d;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    margin-left: 0.5rem;
}

/* Medicine row styling */
.medicine-row {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    background: #f8f9fa;
    overflow-x: hidden;
    box-sizing: border-box;
}

/* Responsive grid fix */
.row {
    margin-left: -0.75rem;
    margin-right: -0.75rem;
    display: flex;
    flex-wrap: wrap;
}

.row > * {
    padding-left: 0.75rem;
    padding-right: 0.75rem;
    box-sizing: border-box;
}

/* Button container responsive */
.button-container {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    justify-content: space-between;
    align-items: center;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .consultation-card-body {
        padding: 1rem;
    }
    
    .button-container {
        flex-direction: column;
        align-items: stretch;
    }
    
    .button-container > * {
        width: 100%;
    }
}
</style>

<div class="consultation-form-container">

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2><i class="fas fa-stethoscope"></i> Patient Consultation</h2>
            <p style="color: #6c757d; margin: 0;">
                <strong>Queue:</strong> {{ $queue->queue_number }} | 
                <strong>Service:</strong> {{ $queue->service_type ?? 'General Consultation' }}
            </p>
        </div>
        <a href="{{ route('doctor.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>

        <!-- Patient Info Card - Simple Visible Design -->
        <div class="dashboard-section" style="background: #4a5568; color: white; margin-bottom: 2rem; padding: 1.5rem;">
            <h3 style="color: white; margin-bottom: 1rem; font-size: 1.5rem;">
                <i class="fas fa-user-circle"></i> Patient Information
            </h3>
            
            <div class="row" style="margin-bottom: 1rem;">
                <div class="col-md-4">
                    <p style="margin-bottom: 0.5rem;">
                        <strong><i class="fas fa-user"></i> Full Name:</strong><br>
                        <span style="font-size: 1.1rem;">{{ $queue->patient->full_name }}</span>
                    </p>
                </div>
                <div class="col-md-4">
                    <p style="margin-bottom: 0.5rem;">
                        <strong><i class="fas fa-birthday-cake"></i> Age / Gender:</strong><br>
                        <span style="font-size: 1.1rem;">{{ $queue->patient->age ?? 'N/A' }} years / {{ ucfirst($queue->patient->gender ?? 'N/A') }}</span>
                    </p>
                </div>
                <div class="col-md-4">
                    <p style="margin-bottom: 0.5rem;">
                        <strong><i class="fas fa-phone"></i> Contact Number:</strong><br>
                        <span style="font-size: 1.1rem;">{{ $queue->patient->contact ?? 'N/A' }}</span>
                    </p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-8">
                    <p style="margin-bottom: 0.5rem;">
                        <strong><i class="fas fa-map-marker-alt"></i> Address:</strong><br>
                        <span style="font-size: 1.1rem;">{{ $queue->patient->address ?? 'N/A' }}</span>
                    </p>
                </div>
                <div class="col-md-4">
                    <p style="margin-bottom: 0.5rem;">
                        <strong><i class="fas fa-flag"></i> Priority Level:</strong><br>
                        <span class="badge" style="font-size: 1rem; {{ $queue->priority == 'Priority' ? 'background: #dc3545;' : 'background: #28a745;' }}">
                            {{ $queue->priority ?? 'Regular' }}
                        </span>
                    </p>
                </div>
            </div>
            
            @if($queue->patient->allergies)
            <div style="margin-top: 1rem; padding: 1rem; background: #dc3545; border-radius: 8px;">
                <p style="margin: 0;">
                    <strong><i class="fas fa-exclamation-triangle"></i> ALLERGIES ALERT:</strong><br>
                    {{ $queue->patient->allergies }}
                </p>
            </div>
            @endif
        </div>

        <!-- Consultation Form -->
        <form action="{{ route('doctor.consultation.save', $queue->id) }}" method="POST">
            @csrf

            <!-- Vital Signs Section -->
            <div class="consultation-card">
                <div class="consultation-card-header">
                    <h3 style="margin: 0; color: #dc3545;">
                        <i class="fas fa-heartbeat"></i> Vital Signs
                        <span class="badge-optional">Optional - Staff can pre-fill</span>
                    </h3>
                </div>
                
                <div class="consultation-card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><i class="fas fa-tint text-danger"></i> Blood Pressure</label>
                                <input type="text" name="blood_pressure" 
                                    value="{{ old('blood_pressure', $queue->consultation->blood_pressure ?? '') }}" 
                                    class="form-control"
                                    placeholder="120/80">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><i class="fas fa-thermometer-half text-warning"></i> Temperature (°C)</label>
                                <input type="number" step="0.1" name="temperature" 
                                    value="{{ old('temperature', $queue->consultation->temperature ?? '') }}" 
                                    class="form-control"
                                    placeholder="37.5">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><i class="fas fa-heartbeat text-danger"></i> Pulse Rate (bpm)</label>
                                <input type="number" name="pulse_rate" 
                                    value="{{ old('pulse_rate', $queue->consultation->pulse_rate ?? '') }}" 
                                    class="form-control"
                                    placeholder="72">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-weight text-primary"></i> Weight (kg)</label>
                                <input type="number" step="0.1" name="weight" 
                                    value="{{ old('weight', $queue->consultation->weight ?? '') }}" 
                                    class="form-control"
                                    placeholder="65.5">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-ruler-vertical text-info"></i> Height (cm)</label>
                                <input type="number" step="0.1" name="height" 
                                    value="{{ old('height', $queue->consultation->height ?? '') }}" 
                                    class="form-control"
                                    placeholder="165">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Clinical Assessment Section -->
            <div class="consultation-card">
                <div class="consultation-card-header">
                    <h3 style="margin: 0; color: #007bff;">
                        <i class="fas fa-clipboard-list"></i> Clinical Assessment
                        <span class="badge-required"><i class="fas fa-asterisk"></i> Required</span>
                    </h3>
                </div>
                
                <div class="consultation-card-body">
                    <div class="form-group">
                        <label>
                            <i class="fas fa-comment-medical text-primary"></i> Chief Complaint
                            <span class="badge-required"><i class="fas fa-asterisk"></i> REQUIRED</span>
                        </label>
                        <textarea name="chief_complaint" rows="2" required
                            class="form-control"
                            placeholder="Patient's main concern or reason for visit">{{ old('chief_complaint', $queue->consultation->chief_complaint ?? '') }}</textarea>
                        @error('chief_complaint')
                            <small class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-notes-medical text-success"></i> Symptoms
                            <span class="badge-optional">Optional</span>
                        </label>
                        <textarea name="symptoms" rows="3"
                            class="form-control"
                            placeholder="Detailed symptoms reported by the patient">{{ old('symptoms', $queue->consultation->symptoms ?? '') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-user-md text-info"></i> Physical Examination
                            <span class="badge-optional">Optional</span>
                        </label>
                        <textarea name="physical_examination" rows="3"
                            class="form-control"
                            placeholder="Findings from physical examination">{{ old('physical_examination', $queue->consultation->physical_examination ?? '') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-diagnoses text-danger"></i> Diagnosis
                            <span class="badge-required"><i class="fas fa-asterisk"></i> REQUIRED</span>
                        </label>
                        <textarea name="diagnosis" rows="2" required
                            class="form-control"
                            placeholder="Preliminary or final diagnosis">{{ old('diagnosis', $queue->consultation->diagnosis ?? '') }}</textarea>
                        @error('diagnosis')
                            <small class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-notes-medical text-warning"></i> Treatment Plan
                            <span class="badge-optional">Optional</span>
                        </label>
                        <textarea name="treatment_plan" rows="3"
                            class="form-control"
                            placeholder="Treatment plan and recommendations">{{ old('treatment_plan', $queue->consultation->treatment_plan ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prescription Section -->
        <div class="consultation-card">
            <div class="consultation-card-header">
                <h3 style="margin: 0; color: #28a745;">
                    <i class="fas fa-prescription"></i> Prescription
                    <span class="badge-optional">Optional - Leave empty for paper prescription</span>
                </h3>
            </div>
            
            <div class="consultation-card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> You can leave this empty if providing paper prescription to patient
                </div>
                
                <div id="medicines-container">
                    <!-- Medicine rows will be added here -->
                </div>

                <button type="button" onclick="addMedicine()" class="btn btn-success" style="margin-top: 1rem;">
                    <i class="fas fa-plus-circle"></i> Add Medicine
                </button>
            </div>
        </div>

        <!-- Additional Information Section -->
        <div class="consultation-card">
            <div class="consultation-card-header">
                <h3 style="margin: 0; color: #6f42c1;">
                    <i class="fas fa-clipboard-list"></i> Follow-up & Notes
                    <span class="badge-optional">Optional</span>
                </h3>
            </div>
            
            <div class="consultation-card-body">
                <div class="form-group">
                    <label><i class="fas fa-calendar-check text-info"></i> Follow-up Date <span class="badge-optional">Optional</span></label>
                    <input type="date" name="follow_up_date" 
                        value="{{ old('follow_up_date', $queue->consultation->follow_up_date ?? '') }}"
                        class="form-control">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-sticky-note text-warning"></i> Doctor's Notes <span class="badge-optional">Optional</span></label>
                    <textarea name="doctor_notes" rows="3"
                        class="form-control"
                        placeholder="Any additional notes or observations">{{ old('doctor_notes', $queue->consultation->doctor_notes ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="button-container" style="margin-top: 2rem;">
            <div style="display: flex; gap: 0.5rem;">
                <a href="{{ route('doctor.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancel
                </a>
                @if($queue->consultation)
                <a href="{{ route('doctor.prescription.print', $queue->consultation->id) }}" 
                   target="_blank"
                   class="btn btn-success">
                    <i class="fas fa-print"></i> Print Prescription
                </a>
                @endif
            </div>
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save"></i> Save Consultation & Mark Complete
            </button>
        </div>
    </form>
</div><!-- /.consultation-form-container -->

<script>
let medicineIndex = 0;

function addMedicine() {
    const container = document.getElementById('medicines-container');
    const medicineRow = document.createElement('div');
    medicineRow.className = 'medicine-row';
    medicineRow.style.cssText = 'border: 2px solid #28a745; padding: 1.5rem; border-radius: 8px; margin-bottom: 1rem; background: #f8f9fa; position: relative;';
    
    medicineRow.innerHTML = `
        <button type="button" onclick="removeMedicine(this)" class="btn btn-sm btn-danger" style="position: absolute; top: 10px; right: 10px;">
            <i class="fas fa-times"></i> Remove
        </button>
        
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label><i class="fas fa-pills text-success"></i> Medicine</label>
                    <select name="prescribed_medicines[${medicineIndex}][medicine_id]" class="form-control">
                        <option value="">Select Medicine</option>
                        @foreach(App\Models\Medicine::all() as $medicine)
                            <option value="{{ $medicine->id }}">{{ $medicine->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label><i class="fas fa-hashtag text-primary"></i> Quantity</label>
                    <input type="number" name="prescribed_medicines[${medicineIndex}][quantity]" min="1" class="form-control" placeholder="10">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label><i class="fas fa-prescription-bottle text-info"></i> Dosage</label>
                    <input type="text" name="prescribed_medicines[${medicineIndex}][dosage]" class="form-control" placeholder="500mg">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label><i class="fas fa-clock text-warning"></i> Frequency</label>
                    <input type="text" name="prescribed_medicines[${medicineIndex}][frequency]" class="form-control" placeholder="3x/day">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label><i class="fas fa-calendar-alt text-danger"></i> Duration</label>
                    <input type="text" name="prescribed_medicines[${medicineIndex}][duration]" class="form-control" placeholder="7 days">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label><i class="fas fa-info-circle text-secondary"></i> Instructions</label>
                    <input type="text" name="prescribed_medicines[${medicineIndex}][instructions]" class="form-control" placeholder="Take after meals">
                </div>
            </div>
        </div>
    `;
    
    container.appendChild(medicineRow);
    medicineIndex++;
}

function removeMedicine(button) {
    button.closest('.medicine-row').remove();
}

// Add first medicine row on page load
document.addEventListener('DOMContentLoaded', function() {
    addMedicine();
});
</script>

@endsection