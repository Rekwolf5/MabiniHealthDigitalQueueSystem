@extends('layouts.patient')

@section('title', 'Patient Dashboard - Mabini Health Center')
@section('page-title', 'My Dashboard')

@section('content')
<div class="patient-dashboard">
    <!-- Welcome Section -->
    <div class="welcome-section">
        <h2>Welcome, {{ $patient->full_name }}!</h2>
        <p>Manage your health records and appointments</p>
    </div>

    <a href="{{ route('patient.queue.request') }}" class="btn btn-primary" style="max-width: 250px; width: auto; position: relative; z-index: 10;">
        <i class="fas fa-list"></i> Request Queue
    </a>

    <!-- Current Queue Status -->
    @if($currentQueue)
    <div class="queue-status-card priority-{{ strtolower($currentQueue->priority) }}">
        <div class="queue-status-header">
            <h3>
                <i class="fas fa-clock"></i>
                Current Queue Status
            </h3>
            <span class="queue-number">{{ $currentQueue->queue_number }}</span>
        </div>
        
        <div class="queue-status-body">
            <div class="status-info">
                <span class="status-badge status-{{ strtolower($currentQueue->status) }}">
                    {{ $currentQueue->status }}
                </span>
                <span class="priority-badge priority-{{ strtolower($currentQueue->priority) }}">
                    {{ $currentQueue->priority }} Priority
                </span>
            </div>
            
            @if($currentQueue->status === 'Waiting' && $queuePosition)
                <p><strong>Your position in queue: #{{ $queuePosition }}</strong></p>
                <p>Estimated wait time: {{ $queuePosition * 15 }} minutes</p>
            @elseif($currentQueue->status === 'Consulting')
                <p><strong>You are currently being consulted</strong></p>
                <p>Please proceed to the consultation room</p>
            @endif
            
            <p>Service: {{ $currentQueue->service_type }}</p>
            <p>Arrived: {{ $currentQueue->arrived_at->format('h:i A') }}</p>
        </div>
    </div>
    @else
    <div class="no-queue-card">
        <i class="fas fa-calendar-plus"></i>
        <h3>No Active Queue</h3>
        <p>You don't have any active queue today. Visit the health center to join the queue.</p>
    </div>
    @endif

    <!-- Quick Stats -->
    <div class="patient-stats">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-file-medical"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $recentHistory->count() }}</h3>
                <p>Medical Records</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $upcomingAppointments->count() }}</h3>
                <p>Upcoming Visits</p>
            </div>
        </div>
    </div>

    <!-- Recent Medical History -->
    @if($recentHistory->count() > 0)
    <div class="dashboard-section">
        <div class="section-header">
            <h3>Recent Medical History</h3>
            <a href="{{ route('patient.medical-history') }}" class="btn btn-primary">View All</a>
        </div>
        
        <div class="medical-history-list">
            @foreach($recentHistory as $record)
            <div class="history-item">
                <div class="history-date">
                    {{ $record->visit_date->format('M d, Y') }}
                </div>
                <div class="history-details">
                    <h4>{{ $record->diagnosis }}</h4>
                    <p>{{ $record->treatment }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<style>
.patient-dashboard {
    display: grid;
    gap: 1.5rem;
    max-width: 1200px;
    margin: 0 auto;
    padding: 1rem;
}

.welcome-section {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    color: white;
    padding: 2rem;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.welcome-section h2 {
    font-size: 1.75rem;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.welcome-section p {
    font-size: 1rem;
    opacity: 0.95;
}

.btn-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    background: #059669;
    color: white;
    padding: 0.875rem 1.5rem;
    min-height: 44px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 2px 4px rgba(5, 150, 105, 0.3);
    -webkit-tap-highlight-color: transparent;
    touch-action: manipulation;
}

.btn-primary:hover {
    background: #047857;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(5, 150, 105, 0.4);
    color: white;
}

.queue-status-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid #e5e7eb;
    border-left: 5px solid #059669;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.queue-status-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.queue-status-card.priority-emergency,
.queue-status-card.priority-pwd,
.queue-status-card.priority-pregnant,
.queue-status-card.priority-senior {
    border-left-color: #dc2626;
}

.queue-status-card.priority-urgent {
    border-left-color: #f59e0b;
}

.queue-status-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.queue-status-header h3 {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #1f2937;
    font-size: 1.25rem;
    margin: 0;
}

.queue-number {
    font-size: 1.75rem;
    font-weight: bold;
    color: #059669;
    background: #ecfdf5;
    padding: 0.75rem 1.25rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(5, 150, 105, 0.2);
}

.queue-status-body {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.queue-status-body p {
    margin: 0;
    color: #4b5563;
    font-size: 0.95rem;
}

.queue-status-body p strong {
    color: #1f2937;
    font-weight: 600;
}

.status-info {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.status-badge,
.priority-badge {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    text-transform: capitalize;
}

.status-badge.status-waiting {
    background: #fef3c7;
    color: #92400e;
}

.status-badge.status-consulting {
    background: #dbeafe;
    color: #1e40af;
}

.status-badge.status-completed {
    background: #d1fae5;
    color: #065f46;
}

.priority-badge {
    background: #fee2e2;
    color: #991b1b;
}

.priority-badge.priority-regular {
    background: #e0e7ff;
    color: #3730a3;
}

.no-queue-card {
    background: white;
    border-radius: 12px;
    padding: 3rem 2rem;
    text-align: center;
    border: 2px dashed #e5e7eb;
    color: #6b7280;
}

.no-queue-card i {
    font-size: 4rem;
    margin-bottom: 1rem;
    color: #d1d5db;
}

.no-queue-card h3 {
    color: #1f2937;
    margin-bottom: 0.5rem;
    font-size: 1.5rem;
}

.no-queue-card p {
    max-width: 500px;
    margin: 0 auto;
    font-size: 1rem;
}

.patient-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.stat-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.75rem;
    flex-shrink: 0;
}

.stat-info h3 {
    font-size: 2rem;
    font-weight: 700;
    color: #059669;
    margin: 0 0 0.25rem 0;
}

.stat-info p {
    margin: 0;
    color: #6b7280;
    font-size: 0.9rem;
    font-weight: 500;
}

.dashboard-section {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.section-header h3 {
    margin: 0;
    color: #1f2937;
    font-size: 1.25rem;
    font-weight: 600;
}

.medical-history-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.history-item {
    background: #f9fafb;
    padding: 1.25rem;
    border-radius: 8px;
    display: flex;
    gap: 1rem;
    align-items: flex-start;
    border: 1px solid #e5e7eb;
    transition: all 0.2s ease;
}

.history-item:hover {
    background: #f3f4f6;
    border-color: #059669;
}

.history-date {
    background: #059669;
    color: white;
    padding: 0.625rem 0.75rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 600;
    white-space: nowrap;
    flex-shrink: 0;
    text-align: center;
}

.history-details {
    flex: 1;
}

.history-details h4 {
    color: #1f2937;
    margin-bottom: 0.375rem;
    font-size: 1.05rem;
    font-weight: 600;
}

.history-details p {
    color: #6b7280;
    font-size: 0.9rem;
    margin: 0;
}

/* Responsive Design */
@media (max-width: 768px) {
    .patient-dashboard {
        padding: 0.5rem;
        gap: 1rem;
    }
    
    .welcome-section {
        padding: 1.5rem 1rem;
    }
    
    .welcome-section h2 {
        font-size: 1.35rem;
    }
    
    .welcome-section p {
        font-size: 0.9rem;
    }

    /* Fix button width and touch targets */
    .btn-primary {
        width: 100%;
        max-width: 100% !important;
        padding: 1rem 1.5rem;
        min-height: 48px;
        font-size: 1rem;
    }

    .btn-primary i {
        font-size: 1.1rem;
    }
    
    .queue-status-card {
        padding: 1.25rem;
    }
    
    .queue-status-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .queue-status-header h3 {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .queue-status-header h3 i {
        font-size: 1.25rem;
    }
    
    .queue-number {
        font-size: 1.5rem;
        padding: 0.625rem 1rem;
    }
    
    .no-queue-card {
        padding: 2rem 1rem;
    }
    
    .no-queue-card i {
        font-size: 3rem;
        margin-bottom: 1rem;
    }
    
    .patient-stats {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .stat-card {
        padding: 1.25rem;
    }
    
    .stat-icon {
        width: 56px;
        height: 56px;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stat-icon i {
        line-height: 1;
    }
    
    .stat-info h3 {
        font-size: 1.75rem;
    }
    
    .dashboard-section {
        padding: 1.25rem;
    }
    
    .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .section-header .btn-primary {
        width: 100%;
    }
    
    .history-item {
        flex-direction: column;
        padding: 1rem;
        gap: 0.75rem;
    }
    
    .history-date {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .status-info {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .status-badge,
    .priority-badge {
        text-align: center;
        width: 100%;
        padding: 0.5rem;
    }

    .queue-status-body p {
        font-size: 0.9rem;
    }

    .btn-primary {
        padding: 0.875rem 1.25rem;
        font-size: 0.95rem;
    }
}
</style>
@endsection
