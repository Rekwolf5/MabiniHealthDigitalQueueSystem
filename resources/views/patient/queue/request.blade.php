@extends('layouts.patient')

@section('title', 'Request Queue')

@section('content')
<div class="request-queue-container">
    <div class="request-card">
        <div class="card-header-custom">
            <div class="header-content">
                <i class="fas fa-clipboard-list"></i>
                <div>
                    <h2>Request a Queue Spot</h2>
                    <p>Fill out the form below to join the queue</p>
                </div>
            </div>
        </div>

        <div class="card-body-custom">
            {{-- Success message --}}
            @if(session('success'))
                <div class="alert-success-custom">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong>Success!</strong>
                        <p>{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            {{-- Error message --}}
            @if ($errors->any())
                <div class="alert-error-custom">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        <strong>Please fix the following errors:</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- Existing pending request warning --}}
            @php
                $existingRequest = App\Models\Queue::where('patient_id', auth('patient')->user()->patient_id)
                    ->whereIn('approval_status', ['pending', 'approved'])
                    ->whereDate('requested_date', '>=', today())
                    ->first();
            @endphp
            @if($existingRequest)
                <div class="alert-warning-custom">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>You already have a pending request</strong>
                        <p>Queue #{{ $existingRequest->queue_number }} for {{ $existingRequest->requested_date->format('F d, Y') }} - Status: {{ ucfirst($existingRequest->approval_status) }}</p>
                        <p style="font-size: 0.875rem; margin-top: 0.5rem;">Please wait for staff approval or cancel your existing request before submitting a new one.</p>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('patient.queue.submit') }}" class="request-form" id="queueRequestForm">
                @csrf
                
                <div class="form-group-custom">
                    <label for="requested_date" class="form-label-custom">
                        <i class="fas fa-calendar-alt"></i>
                        When would you like to visit? *
                    </label>
                    <input 
                        type="date" 
                        name="requested_date" 
                        id="requested_date" 
                        class="form-select-custom" 
                        required
                        min="{{ today()->format('Y-m-d') }}"
                        max="{{ today()->addDays(7)->format('Y-m-d') }}"
                        value="{{ old('requested_date', today()->format('Y-m-d')) }}"
                        onchange="updateServiceAvailability()">
                    <small class="form-hint">You can request a queue for today or up to 7 days in advance</small>
                    <div id="service-availability" style="margin-top: 0.5rem; display: none;"></div>
                </div>
                
                <div class="form-group-custom">
                    <label for="priority" class="form-label-custom">
                        <i class="fas fa-user-shield"></i>
                        Priority Category *
                    </label>
                    <select name="priority" id="priority" class="form-select-custom" required onchange="toggleIdFields()">
                        <option value="">Select your priority category</option>
                        <option value="PWD" {{ old('priority') == 'PWD' ? 'selected' : '' }}>👨‍🦽 Person with Disability (PWD)</option>
                        <option value="Pregnant" {{ old('priority') == 'Pregnant' ? 'selected' : '' }}>🤰 Pregnant</option>
                        <option value="Senior" {{ old('priority') == 'Senior' ? 'selected' : '' }}>👴 Senior Citizen</option>
                        <option value="Regular" {{ old('priority') == 'Regular' ? 'selected' : '' }}>👤 Regular</option>
                    </select>
                    <small class="form-hint">Priority patients will be served first</small>
                </div>

                {{-- PWD ID Field (conditional) --}}
                <div class="form-group-custom" id="pwd-id-field" style="display: none;">
                    <label for="pwd_id" class="form-label-custom">
                        <i class="fas fa-id-card"></i>
                        PWD ID Number
                        <span class="optional-badge">Recommended</span>
                    </label>
                    <input 
                        type="text" 
                        name="pwd_id" 
                        id="pwd_id" 
                        class="form-select-custom" 
                        placeholder="Enter your PWD ID number"
                        value="{{ old('pwd_id') }}">
                    <small class="form-hint">
                        <i class="fas fa-info-circle"></i>
                        Providing your PWD ID helps staff verify your priority status. If not available, you can present it upon arrival.
                    </small>
                </div>

                {{-- Senior Citizen ID Field (conditional) --}}
                <div class="form-group-custom" id="senior-id-field" style="display: none;">
                    <label for="senior_id" class="form-label-custom">
                        <i class="fas fa-id-card"></i>
                        Senior Citizen ID Number
                        <span class="optional-badge">Recommended</span>
                    </label>
                    <input 
                        type="text" 
                        name="senior_id" 
                        id="senior_id" 
                        class="form-select-custom" 
                        placeholder="Enter your Senior Citizen ID number"
                        value="{{ old('senior_id') }}">
                    <small class="form-hint">
                        <i class="fas fa-info-circle"></i>
                        Providing your Senior Citizen ID helps staff verify your priority status. If not available, you can present it upon arrival.
                    </small>
                </div>

                <div class="form-group-custom">
                    <label for="service_type" class="form-label-custom">
                        <i class="fas fa-stethoscope"></i>
                        Service Type *
                    </label>
                    <select name="service_type" id="service_type" class="form-select-custom" required>
                        <option value="">Select the service you need</option>
                        <optgroup label="🏥 Primary Care">
                            <option value="Consultation and Treatment" {{ old('service_type') == 'Consultation and Treatment' ? 'selected' : '' }}>Consultation and Treatment</option>
                            <option value="Circumcision" {{ old('service_type') == 'Circumcision' ? 'selected' : '' }}>Circumcision</option>
                            <option value="Incision and Drainage" {{ old('service_type') == 'Incision and Drainage' ? 'selected' : '' }}>Incision and Drainage</option>
                        </optgroup>
                        <optgroup label="🔬 Laboratory & Diagnostics">
                            <option value="Laboratory Services" {{ old('service_type') == 'Laboratory Services' ? 'selected' : '' }}>Laboratory Services</option>
                        </optgroup>
                        <optgroup label="👶 Maternal & Child Health">
                            <option value="Prenatal Care" {{ old('service_type') == 'Prenatal Care' ? 'selected' : '' }}>Prenatal Care</option>
                            <option value="Normal Delivery" {{ old('service_type') == 'Normal Delivery' ? 'selected' : '' }}>Normal Delivery</option>
                            <option value="Post-natal Care" {{ old('service_type') == 'Post-natal Care' ? 'selected' : '' }}>Post-natal Care</option>
                            <option value="Newborn Screening" {{ old('service_type') == 'Newborn Screening' ? 'selected' : '' }}>Newborn Screening</option>
                        </optgroup>
                        <optgroup label="👨‍👩‍👧‍👦 Family Planning & Immunization">
                            <option value="Family Planning" {{ old('service_type') == 'Family Planning' ? 'selected' : '' }}>Family Planning</option>
                            <option value="Immunization Program" {{ old('service_type') == 'Immunization Program' ? 'selected' : '' }}>Immunization Program</option>
                        </optgroup>
                        <optgroup label="🏥 Other Services">
                            <option value="Dental Services" {{ old('service_type') == 'Dental Services' ? 'selected' : '' }}>Dental Services</option>
                            <option value="Dengue Program" {{ old('service_type') == 'Dengue Program' ? 'selected' : '' }}>Dengue Program</option>
                            <option value="Non-Communicable Diseases" {{ old('service_type') == 'Non-Communicable Diseases' ? 'selected' : '' }}>Non-Communicable Diseases</option>
                            <option value="Sanitation Inspection" {{ old('service_type') == 'Sanitation Inspection' ? 'selected' : '' }}>Sanitation Inspection</option>
                        </optgroup>
                    </select>
                    <small class="form-hint">Choose the service you need assistance with</small>
                </div>

                <div class="form-group-custom">
                    <label for="notes" class="form-label-custom">
                        <i class="fas fa-sticky-note"></i>
                        Additional Notes
                        <span class="optional-badge">Optional</span>
                    </label>
                    <textarea 
                        name="notes" 
                        id="notes" 
                        class="form-textarea-custom" 
                        rows="4" 
                        placeholder="Add any relevant information or special requests...">{{ old('notes') }}</textarea>
                    <small class="form-hint">Any additional details that might be helpful</small>
                </div>

                <div class="info-box">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        <strong>What happens next?</strong>
                        <p>Your queue request will be reviewed by our staff. Once approved, you'll receive a queue number and can track your position in real-time.</p>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('patient.dashboard') }}" class="btn-secondary-custom">
                        <i class="fas fa-arrow-left"></i>
                        Back to Dashboard
                    </a>
                    <button type="submit" class="btn-primary-custom" {{ $existingRequest ? 'disabled' : '' }}>
                        <i class="fas fa-paper-plane"></i>
                        {{ $existingRequest ? 'Already Have Pending Request' : 'Submit Request' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.request-queue-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 1.5rem;
}

.request-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07), 0 10px 25px rgba(0, 0, 0, 0.08);
}

.card-header-custom {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    padding: 2rem;
    color: white;
}

.header-content {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.header-content > i {
    font-size: 3rem;
    opacity: 0.9;
}

.header-content h2 {
    margin: 0 0 0.25rem 0;
    font-size: 1.75rem;
    font-weight: 600;
}

.header-content p {
    margin: 0;
    font-size: 1rem;
    opacity: 0.9;
}

.card-body-custom {
    padding: 2rem;
}

.alert-success-custom,
.alert-error-custom {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem 1.25rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
}

.alert-success-custom {
    background: #d1fae5;
    border-left: 4px solid #059669;
}

.alert-success-custom i {
    color: #059669;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.alert-success-custom strong {
    color: #065f46;
    display: block;
    margin-bottom: 0.25rem;
}

.alert-success-custom p {
    color: #047857;
    margin: 0;
}

.alert-error-custom {
    background: #fee2e2;
    border-left: 4px solid #dc2626;
}

.alert-error-custom i {
    color: #dc2626;
    font-size: 1.5rem;
    flex-shrink: 0;
    margin-top: 0.25rem;
}

.alert-error-custom strong {
    color: #991b1b;
    display: block;
    margin-bottom: 0.5rem;
}

.alert-error-custom ul {
    margin: 0;
    padding-left: 1.25rem;
    color: #b91c1c;
}

.alert-error-custom li {
    margin-bottom: 0.25rem;
}

.request-form {
    display: flex;
    flex-direction: column;
    gap: 1.75rem;
}

.form-group-custom {
    display: flex;
    flex-direction: column;
    gap: 0.625rem;
}

.form-label-custom {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: #1f2937;
    font-size: 1rem;
}

.form-label-custom i {
    color: #059669;
    font-size: 1.1rem;
}

.optional-badge {
    background: #e5e7eb;
    color: #6b7280;
    font-size: 0.75rem;
    padding: 0.25rem 0.625rem;
    border-radius: 12px;
    font-weight: 500;
    margin-left: 0.5rem;
}

.form-select-custom,
.form-textarea-custom {
    padding: 0.875rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 1rem;
    transition: all 0.2s ease;
    font-family: inherit;
    min-height: 44px;
}

.form-select-custom:focus,
.form-textarea-custom:focus {
    outline: none;
    border-color: #059669;
    box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
}

.form-select-custom {
    cursor: pointer;
    background: white;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23059669' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    padding-right: 2.5rem;
    touch-action: manipulation;
    -webkit-tap-highlight-color: transparent;
}

.form-textarea-custom {
    resize: vertical;
    min-height: 100px;
}

.form-hint {
    color: #6b7280;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.375rem;
}

.info-box {
    background: #eff6ff;
    border: 2px solid #bfdbfe;
    border-radius: 10px;
    padding: 1.25rem;
    display: flex;
    gap: 1rem;
    align-items: flex-start;
}

.info-box i {
    color: #3b82f6;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.info-box strong {
    color: #1e40af;
    display: block;
    margin-bottom: 0.375rem;
}

.info-box p {
    color: #1e40af;
    margin: 0;
    font-size: 0.9rem;
    line-height: 1.5;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    padding-top: 0.5rem;
}

.btn-primary-custom,
.btn-secondary-custom {
    display: inline-flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.875rem 1.75rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
    text-decoration: none;
}

.btn-primary-custom {
    background: #059669;
    color: white;
    box-shadow: 0 2px 4px rgba(5, 150, 105, 0.3);
}

.btn-primary-custom:hover {
    background: #047857;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(5, 150, 105, 0.4);
    color: white;
}

.btn-secondary-custom {
    background: white;
    color: #6b7280;
    border: 2px solid #e5e7eb;
}

.btn-secondary-custom:hover {
    background: #f9fafb;
    border-color: #d1d5db;
    color: #4b5563;
}

.alert-warning-custom {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem 1.25rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
    background: #fef3c7;
    border-left: 4px solid #f59e0b;
}

.alert-warning-custom i {
    color: #f59e0b;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.alert-warning-custom strong {
    color: #92400e;
    display: block;
    margin-bottom: 0.25rem;
}

.alert-warning-custom p {
    color: #d97706;
    margin: 0;
}

.btn-primary-custom:disabled {
    background: #d1d5db;
    cursor: not-allowed;
    opacity: 0.6;
}

.btn-primary-custom:disabled:hover {
    transform: none;
    box-shadow: none;
}

/* Responsive Design */
@media (max-width: 768px) {
    .request-queue-container {
        padding: 1rem;
    }
    
    .card-header-custom {
        padding: 1.5rem;
    }
    
    .header-content {
        gap: 1rem;
    }
    
    .header-content > i {
        font-size: 2.5rem;
    }
    
    .header-content h2 {
        font-size: 1.5rem;
    }
    
    .header-content p {
        font-size: 0.9rem;
    }
    
    .card-body-custom {
        padding: 1.5rem;
    }

    .form-select-custom,
    .form-textarea-custom {
        padding: 1rem;
        min-height: 48px;
        font-size: 1rem;
    }

    .form-select-custom {
        background-size: 14px;
        padding-right: 3rem;
    }
    
    .form-actions {
        flex-direction: column-reverse;
        gap: 0.75rem;
    }
    
    .btn-primary-custom,
    .btn-secondary-custom {
        width: 100%;
        justify-content: center;
        padding: 1rem;
        min-height: 48px;
        touch-action: manipulation;
        -webkit-tap-highlight-color: transparent;
    }
    
    .info-box {
        flex-direction: column;
        gap: 0.75rem;
    }
}

@media (max-width: 480px) {
    .request-queue-container {
        padding: 0.5rem;
    }
    
    .header-content {
        flex-direction: column;
        text-align: center;
    }
    
    .header-content > i {
        font-size: 2rem;
    }
    
    .card-body-custom {
        padding: 1.25rem;
    }
    
    .request-form {
        gap: 1.5rem;
    }
}
</style>

<script>
// Toggle ID fields based on priority selection
function toggleIdFields() {
    const priority = document.getElementById('priority').value;
    const pwdField = document.getElementById('pwd-id-field');
    const seniorField = document.getElementById('senior-id-field');
    
    // Hide all ID fields first
    pwdField.style.display = 'none';
    seniorField.style.display = 'none';
    
    // Show relevant field
    if (priority === 'PWD') {
        pwdField.style.display = 'flex';
    } else if (priority === 'Senior') {
        seniorField.style.display = 'flex';
    }
}

// Update service availability info based on selected date
function updateServiceAvailability() {
    const dateInput = document.getElementById('requested_date');
    const availabilityDiv = document.getElementById('service-availability');
    const selectedDate = new Date(dateInput.value);
    const dayOfWeek = selectedDate.getDay();
    
    let message = '';
    let color = '';
    
    // Weekend check
    if (dayOfWeek === 0 || dayOfWeek === 6) {
        message = '⚠️ Health center is closed on weekends';
        color = '#dc2626';
    } else {
        message = '✅ All services available';
        color = '#059669';
    }
    
    availabilityDiv.innerHTML = `<small style="color: ${color}; font-weight: 600;">${message}</small>`;
    availabilityDiv.style.display = 'block';
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleIdFields();
    updateServiceAvailability();
});
</script>
@endsection
