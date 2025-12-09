@extends('layouts.app')

@section('title', 'Queue Monitor - Mabini Health Center')
@section('page-title', 'Queue Monitor')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h2><i class="fas fa-tv"></i> Queue Monitor</h2>
            <p>Real-time queue monitoring for all services</p>
        </div>
        <div class="header-actions">
            <button onclick="location.reload()" class="btn btn-secondary">
                <i class="fas fa-sync"></i> Refresh
            </button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <div class="stats-row">
        <div class="stat-card stat-waiting">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $queue->where('status', 'Waiting')->count() }}</h3>
                <p>Waiting</p>
            </div>
        </div>

        <div class="stat-card stat-consulting">
            <div class="stat-icon">
                <i class="fas fa-user-md"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $queue->where('status', 'Consulting')->count() }}</h3>
                <p>Consulting</p>
            </div>
        </div>

        <div class="stat-card stat-completed">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $queue->where('status', 'Completed')->count() }}</h3>
                <p>Completed</p>
            </div>
        </div>

        <div class="stat-card stat-total">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $queue->count() }}</h3>
                <p>Total Today</p>
            </div>
        </div>
    </div>

    <!-- Filter Controls -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <div class="card-body" style="padding: 1rem;">
            <div class="filter-controls" style="display: flex; gap: 1rem; align-items: center;">
                <label style="margin: 0; font-weight: 600; color: #374151;">
                    <i class="fas fa-filter"></i> Filter by Service:
                </label>
                <select id="serviceFilter" class="form-control" onchange="filterByService()" style="max-width: 250px;">
                    <option value="">All Services</option>
                    <option value="Consultation">Consultation</option>
                    <option value="Dental">Dental</option>
                    <option value="Prenatal">Prenatal</option>
                    <option value="Delivery">Delivery</option>
                    <option value="Postpartum">Postpartum</option>
                    <option value="Immunization">Immunization</option>
                    <option value="Family Planning">Family Planning</option>
                    <option value="Circumcision">Circumcision</option>
                    <option value="Incident Report">Incident Report</option>
                    <option value="Newborn Screening">Newborn Screening</option>
                    <option value="Deworming">Deworming</option>
                    <option value="NCD">NCD</option>
                    <option value="Animal Bite">Animal Bite</option>
                    <option value="Laboratory">Laboratory</option>
                </select>
            </div>
        </div>
    </div>

    @if($queue->isEmpty())
        <div class="card">
            <div class="card-body">
                <div class="empty-state">
                    <i class="fas fa-inbox" style="font-size: 4rem; color: #d1d5db;"></i>
                    <p style="color: #6b7280; margin-top: 1rem; font-size: 1.125rem;">No queue entries for today</p>
                </div>
            </div>
        </div>
    @else
        <!-- Active Queues (Waiting and Consulting) -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
            
            <!-- Waiting Queue Column -->
            <div class="card">
                <div class="card-header" style="background: #fef3c7; border-bottom: 3px solid #f59e0b;">
                    <h3 style="margin: 0; color: #92400e;">
                        <i class="fas fa-clock"></i> Waiting Queue 
                        <span class="badge" style="background: #f59e0b; color: white; margin-left: 0.5rem;">
                            {{ $queue->where('status', 'Waiting')->count() }}
                        </span>
                    </h3>
                </div>
                <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                    @php $waitingQueue = $queue->where('status', 'Waiting'); @endphp
                    @if($waitingQueue->isEmpty())
                        <div class="empty-state" style="padding: 2rem; text-align: center;">
                            <i class="fas fa-check-circle" style="font-size: 3rem; color: #d1d5db;"></i>
                            <p style="color: #6b7280; margin-top: 1rem;">No patients waiting</p>
                        </div>
                    @else
                        @foreach($waitingQueue as $item)
                            <div class="queue-card" data-service="{{ $item->service_type }}" style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
                                    <div>
                                        <span class="queue-number" style="font-size: 1.5rem; font-weight: 700; color: #f59e0b;">
                                            #{{ $item->queue_number }}
                                        </span>
                                        <span class="badge badge-priority badge-priority-{{ strtolower($item->priority) }}" style="margin-left: 0.5rem;">
                                            @if($item->priority == 'Emergency')
                                                <i class="fas fa-exclamation-triangle"></i>
                                            @elseif($item->priority == 'Urgent')
                                                <i class="fas fa-exclamation-circle"></i>
                                            @elseif($item->priority == 'Priority')
                                                <i class="fas fa-star"></i>
                                            @endif
                                            {{ $item->priority }}
                                        </span>
                                    </div>
                                    <span class="wait-time" style="color: #6b7280; font-size: 0.875rem;">
                                        <i class="fas fa-clock"></i> {{ $item->arrived_at ? $item->arrived_at->diffForHumans(null, true) : 'N/A' }}
                                    </span>
                                </div>
                                
                                <div style="margin-bottom: 0.75rem;">
                                    <strong style="font-size: 1.125rem; color: #1f2937;">{{ $item->patient->full_name ?? $item->patient_name }}</strong>
                                    @if($item->patient)
                                        <small style="color: #6b7280; display: block;">Patient ID: {{ $item->patient->id }}</small>
                                    @endif
                                </div>

                                <div style="margin-bottom: 1rem;">
                                    <span class="badge badge-service" style="background: #e0f2fe; color: #075985; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem;">
                                        {{ $item->service_type }}
                                    </span>
                                    <span style="color: #6b7280; font-size: 0.875rem; margin-left: 0.5rem;">
                                        Arrived: {{ $item->arrived_at ? $item->arrived_at->format('h:i A') : 'N/A' }}
                                    </span>
                                </div>

                                <div style="display: flex; gap: 0.5rem;">
                                    <form method="POST" action="{{ route('queue.updateStatus', $item->id) }}" style="flex: 1;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="Consulting">
                                        <button type="submit" class="btn btn-sm btn-info" style="width: 100%;" title="Start Consultation">
                                            <i class="fas fa-play"></i> Start Consultation
                                        </button>
                                    </form>
                                    
                                    <form method="POST" action="{{ route('queue.updateStatus', $item->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="Cancelled">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Cancel" onclick="return confirm('Cancel this queue entry?')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>

                                    <a href="{{ route('patients.show', $item->patient_id) }}" class="btn btn-sm btn-secondary" title="View Patient">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Currently Consulting Column -->
            <div class="card">
                <div class="card-header" style="background: #dbeafe; border-bottom: 3px solid #3b82f6;">
                    <h3 style="margin: 0; color: #1e40af;">
                        <i class="fas fa-user-md"></i> Currently Consulting
                        <span class="badge" style="background: #3b82f6; color: white; margin-left: 0.5rem;">
                            {{ $queue->where('status', 'Consulting')->count() }}
                        </span>
                    </h3>
                </div>
                <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                    @php $consultingQueue = $queue->where('status', 'Consulting'); @endphp
                    @if($consultingQueue->isEmpty())
                        <div class="empty-state" style="padding: 2rem; text-align: center;">
                            <i class="fas fa-user-clock" style="font-size: 3rem; color: #d1d5db;"></i>
                            <p style="color: #6b7280; margin-top: 1rem;">No active consultations</p>
                        </div>
                    @else
                        @foreach($consultingQueue as $item)
                            <div class="queue-card" data-service="{{ $item->service_type }}" style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 1rem; margin-bottom: 1rem; border-left: 4px solid #3b82f6;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
                                    <div>
                                        <span class="queue-number" style="font-size: 1.5rem; font-weight: 700; color: #3b82f6;">
                                            #{{ $item->queue_number }}
                                        </span>
                                        <span class="badge badge-priority badge-priority-{{ strtolower($item->priority) }}" style="margin-left: 0.5rem;">
                                            @if($item->priority == 'Emergency')
                                                <i class="fas fa-exclamation-triangle"></i>
                                            @elseif($item->priority == 'Urgent')
                                                <i class="fas fa-exclamation-circle"></i>
                                            @elseif($item->priority == 'Priority')
                                                <i class="fas fa-star"></i>
                                            @endif
                                            {{ $item->priority }}
                                        </span>
                                    </div>
                                    <span style="color: #3b82f6; font-weight: 600; font-size: 0.875rem;">
                                        <i class="fas fa-circle" style="animation: pulse 2s infinite;"></i> IN PROGRESS
                                    </span>
                                </div>
                                
                                <div style="margin-bottom: 0.75rem;">
                                    <strong style="font-size: 1.125rem; color: #1f2937;">{{ $item->patient->full_name ?? $item->patient_name }}</strong>
                                    @if($item->patient)
                                        <small style="color: #6b7280; display: block;">Patient ID: {{ $item->patient->id }}</small>
                                    @endif
                                </div>

                                <div style="margin-bottom: 1rem;">
                                    <span class="badge badge-service" style="background: #e0f2fe; color: #075985; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem;">
                                        {{ $item->service_type }}
                                    </span>
                                    <span style="color: #6b7280; font-size: 0.875rem; margin-left: 0.5rem;">
                                        Duration: {{ $item->arrived_at ? $item->arrived_at->diffForHumans(null, true) : 'N/A' }}
                                    </span>
                                </div>

                                <div style="display: flex; gap: 0.5rem;">
                                    <form method="POST" action="{{ route('queue.updateStatus', $item->id) }}" style="flex: 1;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="Completed">
                                        <button type="submit" class="btn btn-sm btn-success" style="width: 100%;" title="Mark Complete">
                                            <i class="fas fa-check"></i> Complete
                                        </button>
                                    </form>

                                    <a href="{{ route('patients.show', $item->patient_id) }}" class="btn btn-sm btn-secondary" title="View Patient">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <!-- Completed Queue Section (Bottom) -->
        <div class="card">
            <div class="card-header" style="background: #d1fae5; border-bottom: 3px solid #059669;">
                <h3 style="margin: 0; color: #065f46;">
                    <i class="fas fa-check-circle"></i> Completed Today
                    <span class="badge" style="background: #059669; color: white; margin-left: 0.5rem;">
                        {{ $queue->where('status', 'Completed')->count() }}
                    </span>
                </h3>
            </div>
            <div class="card-body">
                @php $completedQueue = $queue->where('status', 'Completed'); @endphp
                @if($completedQueue->isEmpty())
                    <div class="empty-state" style="padding: 2rem; text-align: center;">
                        <i class="fas fa-hourglass-start" style="font-size: 3rem; color: #d1d5db;"></i>
                        <p style="color: #6b7280; margin-top: 1rem;">No completed consultations yet</p>
                    </div>
                @else
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem;">
                        @foreach($completedQueue as $item)
                            <div class="queue-card" data-service="{{ $item->service_type }}" style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 0.875rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <span class="queue-number" style="font-size: 1.25rem; font-weight: 700; color: #059669;">
                                        #{{ $item->queue_number }}
                                    </span>
                                    <span class="badge badge-priority badge-priority-{{ strtolower($item->priority) }}" style="font-size: 0.75rem;">
                                        {{ $item->priority }}
                                    </span>
                                </div>
                                
                                <div style="margin-bottom: 0.5rem;">
                                    <strong style="font-size: 1rem; color: #1f2937;">{{ $item->patient->full_name ?? $item->patient_name }}</strong>
                                </div>

                                <div style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">
                                    <div>{{ $item->service_type }}</div>
                                    <div>Completed: {{ $item->updated_at ? $item->updated_at->format('h:i A') : 'N/A' }}</div>
                                </div>

                                <a href="{{ route('patients.show', $item->patient_id) }}" class="btn btn-sm btn-secondary" style="width: 100%; padding: 0.375rem; font-size: 0.875rem;" title="View Patient">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.container {
    padding: 2rem;
    max-width: 1400px;
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

.stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.stat-icon {
    width: 64px;
    height: 64px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
}

.stat-waiting .stat-icon {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.stat-consulting .stat-icon {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
}

.stat-completed .stat-icon {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.stat-total .stat-icon {
    background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
}

.stat-content h3 {
    margin: 0 0 0.25rem 0;
    font-size: 2rem;
    color: #1f2937;
}

.stat-content p {
    margin: 0;
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
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h3 {
    margin: 0;
    color: #1f2937;
    font-size: 1.25rem;
}

.filter-controls {
    display: flex;
    gap: 1rem;
}

.card-body {
    padding: 1.5rem;
}

.table-responsive {
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th {
    background: #f9fafb;
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    color: #374151;
    border-bottom: 2px solid #e5e7eb;
}

.table td {
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
    color: #1f2937;
}

.table tbody tr:hover {
    background: #f9fafb;
}

.queue-number {
    font-family: monospace;
    font-size: 1.125rem;
    font-weight: bold;
    color: #10b981;
}

.patient-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.patient-info small {
    color: #6b7280;
    font-size: 0.8125rem;
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

.badge-service {
    background: #e0e7ff;
    color: #3730a3;
}

.badge-priority {
    font-weight: 600;
}

.badge-priority-emergency {
    background: #fee2e2;
    color: #991b1b;
}

.badge-priority-urgent {
    background: #fed7aa;
    color: #9a3412;
}

.badge-priority-priority {
    background: #fef3c7;
    color: #92400e;
}

.badge-priority-regular {
    background: #e5e7eb;
    color: #374151;
}

.badge-status {
    font-weight: 500;
}

.badge-status-waiting {
    background: #fef3c7;
    color: #92400e;
}

.badge-status-consulting {
    background: #dbeafe;
    color: #1e40af;
}

.badge-status-completed {
    background: #d1fae5;
    color: #065f46;
}

.badge-status-cancelled {
    background: #fee2e2;
    color: #991b1b;
}

.wait-time {
    color: #f59e0b;
    font-weight: 500;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
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

.btn-sm {
    padding: 0.375rem 0.625rem;
    font-size: 0.8125rem;
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

.btn-info {
    background: #3b82f6;
    color: white;
}

.btn-info:hover {
    background: #2563eb;
}

.btn-success {
    background: #10b981;
    color: white;
}

.btn-success:hover {
    background: #059669;
}

.btn-danger {
    background: #ef4444;
    color: white;
}

.btn-danger:hover {
    background: #dc2626;
}

.form-control {
    padding: 0.625rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.875rem;
    min-width: 150px;
}

.form-control:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.text-muted {
    color: #9ca3af;
}
</style>

<script>
// Auto-refresh every 30 seconds
setInterval(() => {
    location.reload();
}, 30000);

function filterByService() {
    const service = document.getElementById('serviceFilter').value;
    const cards = document.querySelectorAll('.queue-card');
    
    cards.forEach(card => {
        const cardService = card.getAttribute('data-service');
        const serviceMatch = !service || cardService === service;
        
        card.style.display = serviceMatch ? 'block' : 'none';
    });
}
</script>
@endsection
