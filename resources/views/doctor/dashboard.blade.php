@extends('layouts.app')

@section('title', 'Doctor Dashboard')

@section('content')
<div class="dashboard-grid">
    <!-- Header Section -->
    <div class="dashboard-section" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; border: none;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">
                    <i class="fas fa-user-md"></i> Doctor Dashboard
                </h1>
                <p style="opacity: 0.9;">Welcome back, Dr. {{ Auth::user()->name }}! 👨‍⚕️</p>
            </div>
            <div>
                <a href="{{ route('doctor.my-queue') }}" class="btn btn-primary" style="background: white; color: #3b82f6; font-weight: 600;">
                    <i class="fas fa-list"></i>
                    View All Queue
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Dashboard -->
    <div class="stats-row">
        <div class="stat-card" style="border-left: 4px solid #3b82f6;">
            <div class="stat-icon" style="background: #3b82f6;">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['total_patients_today'] }}</h3>
                <p>Patients Today</p>
            </div>
        </div>
        
        <div class="stat-card" style="border-left: 4px solid #10b981;">
            <div class="stat-icon" style="background: #10b981;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['completed_today'] }}</h3>
                <p>Completed Today</p>
            </div>
        </div>
        
        <div class="stat-card" style="border-left: 4px solid #f59e0b;">
            <div class="stat-icon" style="background: #f59e0b;">
                <i class="fas fa-user-clock"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['pending'] }}</h3>
                <p>Currently Serving</p>
            </div>
        </div>
        
        <div class="stat-card" style="border-left: 4px solid #8b5cf6;">
            <div class="stat-icon" style="background: #8b5cf6;">
                <i class="fas fa-notes-medical"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['total_consultations'] }}</h3>
                <p>Total Consultations</p>
            </div>
        </div>
    </div>

    <!-- Current Patients Section -->
    <div class="dashboard-section">
        <div class="section-header" style="margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #1f2937;">
                <i class="fas fa-user-injured" style="color: #3b82f6;"></i> Current Patients
            </h2>
        </div>

        @if($currentPatients->isEmpty())
            <div style="text-align: center; padding: 3rem;">
                <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                    <div style="background: #f3f4f6; border-radius: 50%; padding: 2rem; width: 120px; height: 120px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-clipboard-check" style="font-size: 3rem; color: #d1d5db;"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 700; color: #6b7280; margin-bottom: 0.5rem;">No Patients Currently Assigned</h3>
                        <p style="color: #9ca3af;">Patients will appear here when staff assigns them to you</p>
                    </div>
                </div>
            </div>
        @else
            <div class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr style="background: #3b82f6;">
                            <th style="color: white;"><i class="fas fa-hashtag"></i> Queue #</th>
                            <th style="color: white;"><i class="fas fa-user"></i> Patient Name</th>
                            <th style="color: white;"><i class="fas fa-stethoscope"></i> Service Type</th>
                            <th style="color: white;"><i class="fas fa-clock"></i> Wait Time</th>
                            <th style="color: white;"><i class="fas fa-info-circle"></i> Status</th>
                            <th style="color: white; text-align: center;"><i class="fas fa-cog"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($currentPatients as $queue)
                        <tr class="{{ $queue->created_at->diffInMinutes(now()) <= 5 ? 'new-assignment' : '' }}">
                            <td>
                                <span style="background: #dbeafe; color: #1e40af; padding: 0.5rem; border-radius: 4px; font-weight: 700; font-size: 1rem;">
                                    {{ $queue->queue_number }}
                                    @if($queue->created_at->diffInMinutes(now()) <= 5)
                                        <span class="new-badge">NEW</span>
                                    @endif
                                </span>
                            </td>
                            <td>
                                <div class="patient-name">
                                    <i class="fas fa-user-circle"></i>
                                    <div>
                                        <strong>{{ $queue->patient->name }}</strong><br>
                                        <small style="color: #6b7280;">{{ $queue->patient->age ?? 'N/A' }} years old</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="btn-sm" style="background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd;">
                                    <i class="fas fa-{{ $queue->service_type === 'Medical Consultation' ? 'stethoscope' : 'heartbeat' }}"></i>
                                    {{ $queue->service_type ?? 'General' }}
                                </span>
                            </td>
                            <td>
                                <i class="fas fa-clock" style="color: #9ca3af;"></i>
                                <span style="color: #6b7280;">{{ $queue->arrived_at ? $queue->arrived_at->diffForHumans() : 'N/A' }}</span>
                            </td>
                            <td>
                                <span class="btn-sm" style="
                                    background: {{ $queue->status === 'consulting' ? '#fef3c7' : ($queue->status === 'completed' ? '#d1fae5' : '#f3f4f6') }};
                                    color: {{ $queue->status === 'consulting' ? '#92400e' : ($queue->status === 'completed' ? '#065f46' : '#374151') }};
                                    border: 1px solid {{ $queue->status === 'consulting' ? '#fde68a' : ($queue->status === 'completed' ? '#6ee7b7' : '#d1d5db') }};">
                                    <i class="fas fa-{{ $queue->status === 'consulting' ? 'user-md' : ($queue->status === 'completed' ? 'check-circle' : 'clock') }}"></i>
                                    {{ ucfirst($queue->status) }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('doctor.consultation', $queue->id) }}" 
                                   class="btn btn-primary">
                                    <i class="fas fa-stethoscope"></i>
                                    Start Consultation
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Recent Consultations Section -->
    <div class="dashboard-section">
        <div class="section-header" style="margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #1f2937;">
                <i class="fas fa-history" style="color: #3b82f6;"></i> Recent Consultations
            </h2>
        </div>

        @if($recentConsultations->isEmpty())
            <div style="text-align: center; padding: 3rem;">
                <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                    <div style="background: #f3f4f6; border-radius: 50%; padding: 2rem; width: 120px; height: 120px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-file-medical" style="font-size: 3rem; color: #d1d5db;"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 700; color: #6b7280; margin-bottom: 0.5rem;">No Recent Consultations</h3>
                        <p style="color: #9ca3af;">Your consultation history will appear here</p>
                    </div>
                </div>
            </div>
        @else
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @foreach($recentConsultations as $consultation)
                <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 1.5rem; transition: all 0.2s ease; background: white;">
                    <div style="display: flex; justify-between; align-items: start; margin-bottom: 1rem;">
                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                                <i class="fas fa-user-circle" style="font-size: 1.5rem; color: #3b82f6;"></i>
                                <h3 style="font-size: 1.125rem; font-weight: 700; color: #1f2937;">{{ $consultation->patient->name }}</h3>
                            </div>
                            <p style="font-size: 0.875rem; color: #6b7280; margin-left: 2.25rem;">
                                <i class="fas fa-calendar" style="color: #9ca3af;"></i>
                                {{ $consultation->created_at->format('M d, Y g:i A') }}
                            </p>
                        </div>
                        <a href="{{ route('doctor.patient-history', $consultation->patient_id) }}" 
                           class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i> View History
                        </a>
                    </div>
                    
                    <div style="background: #f9fafb; padding: 1rem; border-radius: 4px; border-left: 4px solid #3b82f6; margin-bottom: 0.75rem;">
                        <p style="font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.25rem;">
                            <i class="fas fa-notes-medical" style="color: #3b82f6;"></i> Diagnosis:
                        </p>
                        <p style="font-size: 0.875rem; color: #6b7280;">{{ Str::limit($consultation->diagnosis, 150) }}</p>
                    </div>
                    
                    @if($consultation->prescribed_medicines)
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <span class="btn-sm" style="background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7;">
                            <i class="fas fa-pills"></i> Prescription Given
                        </span>
                        @if($consultation->prescription_dispensed)
                        <span class="btn-sm" style="background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd;">
                            <i class="fas fa-check"></i> Dispensed
                        </span>
                        @else
                        <span class="btn-sm" style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a;">
                            <i class="fas fa-clock"></i> Pending Dispensing
                        </span>
                        @endif
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<style>
/* New Assignment Highlight */
.new-assignment {
    animation: pulse-highlight 2s ease-in-out infinite;
    background: linear-gradient(to right, rgba(59, 130, 246, 0.05) 0%, transparent 100%);
}

@keyframes pulse-highlight {
    0%, 100% {
        background: rgba(59, 130, 246, 0.05);
    }
    50% {
        background: rgba(59, 130, 246, 0.1);
    }
}

.new-badge {
    display: inline-block;
    background: #ef4444;
    color: white;
    font-size: 0.65rem;
    padding: 0.15rem 0.4rem;
    border-radius: 4px;
    margin-left: 0.5rem;
    font-weight: 700;
    animation: blink 1.5s ease-in-out infinite;
}

@keyframes blink {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.6;
    }
}
</style>
@endsection
