@extends('layouts.app')

@section('title', 'Add to Queue - Mabini Health Center')
@section('page-title', 'Add Patient to Queue')

@section('content')
<div class="form-container">
    @if ($errors->any())
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <strong>Please fix the following errors:</strong>
            <ul style="margin: 0.5rem 0 0 1rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('queue.store') }}" method="POST" class="queue-form">
        @csrf
        
        <div class="form-section">
            <h3>Queue Information</h3>
            
            <div class="form-group">
                <label for="patient_name">Patient Name *</label>
                <input type="text" id="patient_name" name="patient_name" value="{{ old('patient_name') }}" required>
                <small style="color: #718096;">Enter patient's name (e.g., "Maria Santos"). Patient must be registered first.</small>
                
                @if($patients->count() > 0)
                <div style="margin-top: 0.5rem;">
                    <small style="color: #059669;"><strong>Registered Patients:</strong></small>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.25rem;">
                        @foreach($patients->take(10) as $patient)
                            <span style="background: #ecfdf5; color: #059669; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; cursor: pointer;" 
                                  onclick="document.getElementById('patient_name').value = '{{ $patient->full_name }}'">
                                {{ $patient->full_name }}
                            </span>
                        @endforeach
                        @if($patients->count() > 10)
                            <span style="color: #6b7280; font-size: 0.75rem;">+{{ $patients->count() - 10 }} more...</span>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="patient_category">Patient Category *</label>
                    <select id="patient_category" name="patient_category" required onchange="updatePriority()">
                        <option value="">-- Select Category --</option>
                        <optgroup label="Priority Patients (Same Priority Level)">
                            <option value="PWD" {{ old('patient_category') == 'PWD' ? 'selected' : '' }}>PWD (Person with Disability)</option>
                            <option value="Pregnant" {{ old('patient_category') == 'Pregnant' ? 'selected' : '' }}>Pregnant Women</option>
                            <option value="Senior" {{ old('patient_category') == 'Senior' ? 'selected' : '' }}>Senior Citizen</option>
                        </optgroup>
                        <optgroup label="Regular Patients">
                            <option value="Regular" {{ old('patient_category') == 'Regular' ? 'selected' : '' }}>Regular Patient</option>
                        </optgroup>
                    </select>
                    <input type="hidden" id="priority" name="priority" value="{{ old('priority', 'Regular') }}">
                    <small style="color: #718096;">PWD, Pregnant, and Senior patients have the same priority level</small>
                </div>
                <div class="form-group">
                    <label for="service_type">Service Type *</label>
                    <select id="service_type" name="service_type" required>
                        <option value="">-- Select Service --</option>
                        <optgroup label="Primary Care">
                            <option value="Consultation and Treatment" {{ old('service_type') == 'Consultation and Treatment' ? 'selected' : '' }}>Consultation and Treatment</option>
                            <option value="Circumcision" {{ old('service_type') == 'Circumcision' ? 'selected' : '' }}>Circumcision</option>
                            <option value="Incision and Drainage" {{ old('service_type') == 'Incision and Drainage' ? 'selected' : '' }}>Incision and Drainage</option>
                        </optgroup>
                        <optgroup label="Laboratory & Diagnostics">
                            <option value="Laboratory Services" {{ old('service_type') == 'Laboratory Services' ? 'selected' : '' }}>Laboratory Services</option>
                        </optgroup>
                        <optgroup label="Maternal & Child Health">
                            <option value="Prenatal Care" {{ old('service_type') == 'Prenatal Care' ? 'selected' : '' }}>Prenatal Care</option>
                            <option value="Normal Delivery" {{ old('service_type') == 'Normal Delivery' ? 'selected' : '' }}>Normal Delivery</option>
                            <option value="Post-natal Care" {{ old('service_type') == 'Post-natal Care' ? 'selected' : '' }}>Post-natal Care</option>
                            <option value="Newborn Screening" {{ old('service_type') == 'Newborn Screening' ? 'selected' : '' }}>Newborn Screening</option>
                        </optgroup>
                        <optgroup label="Family Planning & Immunization">
                            <option value="Family Planning" {{ old('service_type') == 'Family Planning' ? 'selected' : '' }}>Family Planning</option>
                            <option value="Immunization Program" {{ old('service_type') == 'Immunization Program' ? 'selected' : '' }}>Immunization Program</option>
                        </optgroup>
                        <optgroup label="Other Services">
                            <option value="Dental Services" {{ old('service_type') == 'Dental Services' ? 'selected' : '' }}>Dental Services</option>
                            <option value="Dengue Program" {{ old('service_type') == 'Dengue Program' ? 'selected' : '' }}>Dengue Program</option>
                            <option value="Non-Communicable Diseases" {{ old('service_type') == 'Non-Communicable Diseases' ? 'selected' : '' }}>Non-Communicable Diseases</option>
                            <option value="Sanitation Inspection" {{ old('service_type') == 'Sanitation Inspection' ? 'selected' : '' }}>Sanitation Inspection</option>
                        </optgroup>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="assigned_doctor_id">Assign Doctor (Optional)</label>
                <select id="assigned_doctor_id" name="assigned_doctor_id" class="form-control">
                    <option value="">-- No Doctor Assigned --</option>
                    @foreach(\App\Models\User::where('role', 'doctor')->orderBy('name')->get() as $doctor)
                        <option value="{{ $doctor->id }}" {{ old('assigned_doctor_id') == $doctor->id ? 'selected' : '' }}>
                            Dr. {{ $doctor->name }}
                        </option>
                    @endforeach
                </select>
                <small style="color: #718096;">Optionally assign a doctor when adding to queue</small>
            </div>

            <div class="form-group">
                <label for="notes">Notes (Optional)</label>
                <textarea id="notes" name="notes" rows="3" placeholder="Any additional notes or symptoms...">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('queue.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Add to Queue
            </button>
        </div>
    </form>
</div>

<script>
function updatePriority() {
    const category = document.getElementById('patient_category').value;
    const priorityInput = document.getElementById('priority');
    
    // PWD, Pregnant, Senior → Priority
    if (['PWD', 'Pregnant', 'Senior'].includes(category)) {
        priorityInput.value = 'Priority';
    } else {
        priorityInput.value = 'Regular';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updatePriority();
});
</script>

@if($patients->count() == 0)
<div class="dashboard-section" style="margin-top: 1.5rem;">
    <div style="text-align: center; padding: 2rem; color: #6b7280;">
        <i class="fas fa-user-plus" style="font-size: 2rem; margin-bottom: 1rem; color: #d1d5db;"></i>
        <h3 style="margin-bottom: 0.5rem;">No patients registered yet</h3>
        <p>You need to register patients first before adding them to the queue.</p>
        <a href="{{ route('patients.create') }}" class="btn btn-primary" style="margin-top: 1rem;">
            <i class="fas fa-user-plus"></i>
            Register First Patient
        </a>
    </div>
</div>
@endif
@endsection
