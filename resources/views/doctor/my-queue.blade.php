@extends('layouts.app')

@section('title', 'My Patients Queue')

@section('content')
<div class="dashboard-grid">
    <!-- Header Section -->
    <div class="dashboard-section" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; border: none;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">
                    <i class="fas fa-user-md"></i> My Patients Today
                </h1>
                <p style="opacity: 0.9;">All patients assigned to you on {{ date('F d, Y') }}</p>
            </div>
            <div>
                <a href="{{ route('doctor.dashboard') }}" class="btn btn-secondary" style="background: white; color: #3b82f6; font-weight: 600;">
                    <i class="fas fa-arrow-left"></i>
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Summary -->
    @if(!$myPatients->isEmpty())
    <div class="stats-row">
        <div class="stat-card" style="border-left: 4px solid #3b82f6;">
            <div class="stat-icon" style="background: #3b82f6;">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $myPatients->count() }}</h3>
                <p>Total Patients</p>
            </div>
        </div>
        
        <div class="stat-card" style="border-left: 4px solid #f59e0b;">
            <div class="stat-icon" style="background: #f59e0b;">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $myPatients->where('status', 'Waiting')->count() }}</h3>
                <p>Waiting</p>
            </div>
        </div>
        
        <div class="stat-card" style="border-left: 4px solid #8b5cf6;">
            <div class="stat-icon" style="background: #8b5cf6;">
                <i class="fas fa-user-md"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $myPatients->whereIn('status', ['Serving', 'Consulting'])->count() }}</h3>
                <p>In Consultation</p>
            </div>
        </div>
        
        <div class="stat-card" style="border-left: 4px solid #10b981;">
            <div class="stat-icon" style="background: #10b981;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $myPatients->where('status', 'Completed')->count() }}</h3>
                <p>Completed</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Patients List -->
    <div class="dashboard-section" style="padding: 0;">
        @if($myPatients->isEmpty())
            <div style="text-align: center; padding: 4rem;">
                <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                    <div style="background: #f3f4f6; border-radius: 50%; padding: 2.5rem; width: 140px; height: 140px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-users-slash" style="font-size: 4rem; color: #d1d5db;"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 1.5rem; font-weight: 700; color: #6b7280; margin-bottom: 0.5rem;">No Patients Assigned</h3>
                        <p style="color: #9ca3af; font-size: 1rem;">You don't have any patients assigned to you today</p>
                        <p style="color: #9ca3af; font-size: 0.875rem; margin-top: 0.5rem;">Patients will appear here when staff assigns them to you</p>
                    </div>
                </div>
            </div>
        @else
            <div class="data-table-container" style="border-radius: 0;">
                <table class="data-table">
                    <thead>
                        <tr style="background: #3b82f6;">
                            <th style="color: white;"><i class="fas fa-hashtag"></i> Queue #</th>
                            <th style="color: white;"><i class="fas fa-user"></i> Patient</th>
                            <th style="color: white;"><i class="fas fa-user-tag"></i> Age / Gender</th>
                            <th style="color: white;"><i class="fas fa-stethoscope"></i> Service Type</th>
                            <th style="color: white;"><i class="fas fa-exclamation-triangle"></i> Priority</th>
                            <th style="color: white;"><i class="fas fa-info-circle"></i> Status</th>
                            <th style="color: white;"><i class="fas fa-clock"></i> Arrived</th>
                            <th style="color: white;"><i class="fas fa-hourglass-half"></i> Wait Time</th>
                            <th style="color: white; text-align: center;"><i class="fas fa-cog"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($myPatients as $queue)
                        <tr>
                            <td>
                                <span style="background: #dbeafe; color: #1e40af; padding: 0.5rem; border-radius: 4px; font-weight: 700; font-size: 1rem;">
                                    {{ $queue->queue_number }}
                                </span>
                            </td>
                            <td>
                                <div class="patient-name">
                                    <i class="fas fa-user-circle"></i>
                                    <div>
                                        <strong>{{ $queue->patient->name }}</strong><br>
                                        <small style="color: #6b7280;">
                                            <i class="fas fa-phone" style="font-size: 0.7rem;"></i> {{ $queue->patient->phone }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <strong style="color: #374151;">{{ $queue->patient->age }}</strong> 
                                    <small style="color: #9ca3af;">years</small>
                                </div>
                                <span class="btn-sm" style="
                                    background: {{ $queue->patient->gender === 'male' ? '#dbeafe' : '#fce7f3' }};
                                    color: {{ $queue->patient->gender === 'male' ? '#1e40af' : '#9f1239' }};
                                    border: 1px solid {{ $queue->patient->gender === 'male' ? '#93c5fd' : '#fbcfe8' }};
                                    font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                    <i class="fas fa-{{ $queue->patient->gender === 'male' ? 'mars' : 'venus' }}"></i>
                                    {{ ucfirst($queue->patient->gender) }}
                                </span>
                            </td>
                            <td>
                                <span class="btn-sm" style="background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd;">
                                    <i class="fas fa-{{ $queue->service_type === 'Medical Consultation' ? 'stethoscope' : 'tooth' }}"></i>
                                    {{ $queue->service_type }}
                                </span>
                            </td>
                            <td>
                                @if($queue->priority == 'High')
                                <span class="btn-sm" style="background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;">
                                    <i class="fas fa-exclamation-circle"></i> {{ $queue->priority }}
                                </span>
                                @else
                                <span class="btn-sm" style="background: #f3f4f6; color: #374151; border: 1px solid #d1d5db;">
                                    {{ $queue->priority }}
                                </span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusStyles = [
                                        'Waiting' => ['bg' => '#fef3c7', 'color' => '#92400e', 'border' => '#fde68a', 'icon' => 'clock'],
                                        'Serving' => ['bg' => '#dbeafe', 'color' => '#1e40af', 'border' => '#93c5fd', 'icon' => 'user-md'],
                                        'Consulting' => ['bg' => '#e0e7ff', 'color' => '#3730a3', 'border' => '#c7d2fe', 'icon' => 'stethoscope'],
                                        'Completed' => ['bg' => '#d1fae5', 'color' => '#065f46', 'border' => '#6ee7b7', 'icon' => 'check-circle'],
                                        'Skipped' => ['bg' => '#f3f4f6', 'color' => '#374151', 'border' => '#d1d5db', 'icon' => 'forward'],
                                        'Unattended' => ['bg' => '#fee2e2', 'color' => '#991b1b', 'border' => '#fecaca', 'icon' => 'times-circle'],
                                    ];
                                    $style = $statusStyles[$queue->status] ?? ['bg' => '#f3f4f6', 'color' => '#374151', 'border' => '#d1d5db', 'icon' => 'info-circle'];
                                @endphp
                                <span class="btn-sm" style="background: {{ $style['bg'] }}; color: {{ $style['color'] }}; border: 1px solid {{ $style['border'] }};">
                                    <i class="fas fa-{{ $style['icon'] }}"></i> {{ $queue->status }}
                                </span>
                            </td>
                            <td>
                                <i class="fas fa-clock" style="color: #9ca3af;"></i>
                                <span style="font-weight: 600; color: #374151;">{{ $queue->arrived_at->format('g:i A') }}</span>
                            </td>
                            <td>
                                <span style="font-weight: 700; font-size: 1rem; color: {{ $queue->wait_time > 30 ? '#dc2626' : '#6b7280' }};">
                                    {{ $queue->wait_time }}
                                </span>
                                <small style="color: #9ca3af;">mins</small>
                                @if($queue->wait_time > 30)
                                    <br><span class="btn-sm" style="background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; font-size: 0.7rem; padding: 0.125rem 0.375rem;">
                                        <i class="fas fa-exclamation-triangle"></i> Long Wait
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if(!$queue->doctor_accepted_at)
                                    <!-- Not yet accepted - show accept/reject buttons -->
                                    <div style="display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap;">
                                        <form method="POST" action="{{ route('doctor.queue.accept', $queue->id) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Accept this patient">
                                                <i class="fas fa-check"></i> Accept
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="showRejectModal({{ $queue->id }}, '{{ $queue->patient->full_name }}', '{{ $queue->queue_number }}')" title="Reject this patient">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    </div>
                                @elseif($queue->status == 'Waiting')
                                    <!-- Accepted and waiting - show start button -->
                                    <div style="margin-bottom: 0.5rem;">
                                        <span class="btn-sm" style="background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; font-size: 0.75rem;">
                                            <i class="fas fa-check-circle"></i> Accepted
                                        </span>
                                    </div>
                                    <a href="{{ route('doctor.consultation', $queue->id) }}" class="btn btn-primary">
                                        <i class="fas fa-stethoscope"></i> Start
                                    </a>
                                @elseif($queue->status == 'Consulting')
                                    <!-- In consultation -->
                                    <a href="{{ route('doctor.consultation', $queue->id) }}" class="btn btn-primary">
                                        <i class="fas fa-stethoscope"></i>
                                        @if($queue->consultation)
                                            Continue
                                        @else
                                            Start
                                        @endif
                                    </a>
                                @elseif($queue->status == 'Completed')
                                    <a href="{{ route('doctor.patient-history', $queue->patient_id) }}" class="btn btn-sm btn-success">
                                        <i class="fas fa-check-circle"></i> View Record
                                    </a>
                                @else
                                    <span style="color: #9ca3af; font-style: italic;">
                                        <i class="fas fa-ban"></i> Not ready
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Summary Footer -->
            <div style="padding: 1.5rem; background: #f9fafb; border-top: 1px solid #e5e7eb;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <div style="font-size: 0.875rem; color: #6b7280;">
                        <i class="fas fa-users" style="color: #3b82f6;"></i>
                        Total: <span style="font-weight: 700; color: #1f2937; font-size: 1rem;">{{ $myPatients->count() }}</span> patients
                    </div>
                    <div style="display: flex; gap: 1.5rem; font-size: 0.875rem; flex-wrap: wrap;">
                        <div>
                            <span style="color: #6b7280;">Waiting:</span>
                            <span style="font-weight: 700; color: #f59e0b;">{{ $myPatients->where('status', 'Waiting')->count() }}</span>
                        </div>
                        <div>
                            <span style="color: #6b7280;">Serving:</span>
                            <span style="font-weight: 700; color: #3b82f6;">{{ $myPatients->whereIn('status', ['Serving', 'Consulting'])->count() }}</span>
                        </div>
                        <div>
                            <span style="color: #6b7280;">Completed:</span>
                            <span style="font-weight: 700; color: #10b981;">{{ $myPatients->where('status', 'Completed')->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Reject Patient Modal -->
<div id="rejectModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 8px; padding: 2rem; max-width: 500px; width: 90%;">
        <h3 style="margin-bottom: 1rem; color: #1f2937;">
            <i class="fas fa-exclamation-triangle" style="color: #dc2626;"></i>
            Reject Patient
        </h3>
        <p style="margin-bottom: 1rem; color: #6b7280;">
            You are about to reject <strong id="rejectPatientName"></strong> (Queue #<span id="rejectQueueNumber"></span>)
        </p>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="form-group">
                <label for="rejection_reason">Reason for Rejection *</label>
                <textarea id="rejection_reason" name="rejection_reason" rows="4" required placeholder="Please provide a reason for rejecting this patient..."></textarea>
                <small style="color: #6b7280;">This will help staff reassign the patient appropriately</small>
            </div>
            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
                <button type="button" onclick="closeRejectModal()" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-check"></i> Confirm Rejection
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showRejectModal(queueId, patientName, queueNumber) {
    document.getElementById('rejectPatientName').textContent = patientName;
    document.getElementById('rejectQueueNumber').textContent = queueNumber;
    document.getElementById('rejectForm').action = `/doctor/queue/${queueId}/reject`;
    document.getElementById('rejectModal').style.display = 'flex';
}

function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
    document.getElementById('rejection_reason').value = '';
}

// Close modal when clicking outside
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});
</script>
@endsection
