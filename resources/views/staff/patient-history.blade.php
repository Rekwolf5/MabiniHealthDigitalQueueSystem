@extends('layouts.app')

@section('title', 'Patient History - Mabini Health Center')
@section('page-title', 'Patient Medical History')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h2><i class="fas fa-history"></i> Patient Medical History</h2>
            <p>Complete medical records for {{ $patient->full_name }}</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('staff.queue.management') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Queue
            </a>
            <a href="{{ route('patients.show', $patient->id) }}" class="btn btn-primary">
                <i class="fas fa-user"></i> View Patient Profile
            </a>
        </div>
    </div>

    <div class="patient-card">
        <div class="patient-header">
            <div class="patient-avatar">
                {{ strtoupper(substr($patient->first_name, 0, 1)) }}{{ strtoupper(substr($patient->last_name, 0, 1)) }}
            </div>
            <div class="patient-details">
                <h3>{{ $patient->full_name }}</h3>
                <div class="patient-meta">
                    <span><i class="fas fa-id-card"></i> ID: {{ $patient->id }}</span>
                    <span><i class="fas fa-birthday-cake"></i> {{ $patient->date_of_birth ? $patient->date_of_birth->format('M d, Y') : 'N/A' }}</span>
                    <span><i class="fas fa-venus-mars"></i> {{ $patient->gender }}</span>
                    <span><i class="fas fa-phone"></i> {{ $patient->contact_number ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <div class="patient-stats">
            <div class="stat-item">
                <div class="stat-value">{{ $medicalHistory->total() }}</div>
                <div class="stat-label">Total Visits</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $medicalHistory->where('visit_date', '>=', now()->subMonths(3))->count() }}</div>
                <div class="stat-label">Last 3 Months</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $medicalHistory->first()?->visit_date?->format('M d, Y') ?? 'N/A' }}</div>
                <div class="stat-label">Last Visit</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-file-medical"></i> Medical Records</h3>
        </div>

        <div class="card-body">
            @if($medicalHistory->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-notes-medical" style="font-size: 4rem; color: #d1d5db;"></i>
                    <p style="color: #6b7280; margin-top: 1rem; font-size: 1.125rem;">No medical records found</p>
                </div>
            @else
                <div class="timeline">
                    @foreach($medicalHistory as $record)
                        <div class="timeline-item">
                            <div class="timeline-marker">
                                <i class="fas fa-stethoscope"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="record-header">
                                    <div>
                                        <h4>{{ $record->visit_date->format('F d, Y') }}</h4>
                                        <small class="text-muted">{{ $record->visit_date ? $record->visit_date->diffForHumans() : 'Recently' }}</small>
                                    </div>
                                    <span class="badge badge-primary">Visit #{{ $loop->iteration }}</span>
                                </div>

                                <div class="record-body">
                                    @if($record->diagnosis)
                                        <div class="record-section">
                                            <h5><i class="fas fa-diagnoses"></i> Diagnosis</h5>
                                            <p>{{ $record->diagnosis }}</p>
                                        </div>
                                    @endif

                                    @if($record->treatment)
                                        <div class="record-section">
                                            <h5><i class="fas fa-prescription"></i> Treatment</h5>
                                            <p>{{ $record->treatment }}</p>
                                        </div>
                                    @endif

                                    @if($record->notes)
                                        <div class="record-section">
                                            <h5><i class="fas fa-notes-medical"></i> Notes</h5>
                                            <p>{{ $record->notes }}</p>
                                        </div>
                                    @endif

                                    @if($record->vital_signs)
                                        <div class="record-section">
                                            <h5><i class="fas fa-heartbeat"></i> Vital Signs</h5>
                                            <div class="vital-signs">
                                                @foreach(json_decode($record->vital_signs, true) ?? [] as $key => $value)
                                                    <span class="vital-item">
                                                        <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="record-footer">
                                    <small class="text-muted">
                                        <i class="fas fa-user-md"></i> Attended by: {{ $record->user->name ?? 'Unknown' }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pagination-wrapper">
                    {{ $medicalHistory->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.container {
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.page-header h2 {
    margin: 0 0 0.5rem 0;
    color: #1f2937;
    font-size: 2rem;
}

.page-header p {
    margin: 0;
    color: #6b7280;
}

.header-actions {
    display: flex;
    gap: 1rem;
}

.patient-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
    overflow: hidden;
}

.patient-header {
    padding: 2rem;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    display: flex;
    align-items: center;
    gap: 2rem;
}

.patient-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: bold;
    border: 3px solid white;
}

.patient-details h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.75rem;
}

.patient-meta {
    display: flex;
    gap: 2rem;
    flex-wrap: wrap;
    opacity: 0.9;
}

.patient-meta span {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.patient-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    border-top: 1px solid #e5e7eb;
}

.stat-item {
    padding: 1.5rem;
    text-align: center;
    border-right: 1px solid #e5e7eb;
}

.stat-item:last-child {
    border-right: none;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: bold;
    color: #10b981;
    margin-bottom: 0.5rem;
}

.stat-label {
    color: #6b7280;
    font-size: 0.875rem;
}

.card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.card-header h3 {
    margin: 0;
    color: #1f2937;
    font-size: 1.25rem;
}

.card-body {
    padding: 1.5rem;
}

.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 0.5rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e5e7eb;
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
}

.timeline-marker {
    position: absolute;
    left: -1.5rem;
    top: 0;
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    background: #10b981;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid white;
    box-shadow: 0 0 0 2px #e5e7eb;
}

.timeline-content {
    background: #f9fafb;
    border-radius: 8px;
    padding: 1.5rem;
    margin-left: 1rem;
}

.record-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e5e7eb;
}

.record-header h4 {
    margin: 0 0 0.25rem 0;
    color: #1f2937;
}

.record-body {
    margin-bottom: 1rem;
}

.record-section {
    margin-bottom: 1.5rem;
}

.record-section:last-child {
    margin-bottom: 0;
}

.record-section h5 {
    margin: 0 0 0.5rem 0;
    color: #374151;
    font-size: 0.9375rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.record-section h5 i {
    color: #10b981;
}

.record-section p {
    margin: 0;
    color: #4b5563;
    line-height: 1.6;
}

.vital-signs {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
}

.vital-item {
    display: block;
    padding: 0.5rem;
    background: white;
    border-radius: 4px;
    font-size: 0.875rem;
}

.record-footer {
    padding-top: 1rem;
    border-top: 1px solid #e5e7eb;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.8125rem;
    font-weight: 500;
}

.badge-primary {
    background: #dbeafe;
    color: #1e40af;
}

.btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.btn-primary {
    background: #10b981;
    color: white;
}

.btn-primary:hover {
    background: #059669;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.text-muted {
    color: #6b7280;
}

.pagination-wrapper {
    margin-top: 2rem;
    display: flex;
    justify-content: center;
}
</style>
@endsection
