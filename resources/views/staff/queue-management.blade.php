@extends('layouts.app')

@section('title', 'Queue Management - Mabini Health Center')
@section('page-title', 'Queue Management')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h2><i class="fas fa-tools"></i> Staff Tools</h2>
            <p>Comprehensive queue management, requests, and patient search</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('queue.display') }}" class="btn btn-secondary" target="_blank">
                <i class="fas fa-tv"></i> Queue Display
            </a>
            <a href="{{ route('queue.add') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add to Queue
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Enhanced Statistics Dashboard -->
    <div class="stats-row" style="margin-bottom: 2rem;">
        <div class="stat-card stat-total">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['total_today'] }}</h3>
                <p>Total Today</p>
            </div>
        </div>

        <div class="stat-card stat-waiting">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['waiting'] }}</h3>
                <p>Waiting</p>
            </div>
        </div>

        <div class="stat-card stat-consulting">
            <div class="stat-icon">
                <i class="fas fa-user-md"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['consulting'] }}</h3>
                <p>Consulting</p>
            </div>
        </div>

        <div class="stat-card stat-completed">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['completed'] }}</h3>
                <p>Completed</p>
            </div>
        </div>
    </div>

    <!-- Additional Performance Metrics -->
    <div style="margin-bottom: 2rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Avg Wait Time</div>
                    <div style="font-size: 2rem; font-weight: bold;">{{ $stats['average_wait_time'] }} min</div>
                </div>
                <i class="fas fa-hourglass-half" style="font-size: 3rem; opacity: 0.3;"></i>
            </div>
        </div>
        
        <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Services Today</div>
                    <div style="font-size: 2rem; font-weight: bold;">{{ count($stats['service_breakdown']) }}</div>
                </div>
                <i class="fas fa-stethoscope" style="font-size: 3rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="card" style="margin-bottom: 2rem;">
        <div style="border-bottom: 2px solid #e5e7eb; padding: 0;">
            <div class="tabs" style="display: flex; gap: 0; margin: 0;">
                <a href="{{ route('staff.queue.management', ['tab' => 'queue']) }}" 
                   class="tab-link {{ ($tab ?? 'queue') === 'queue' ? 'active' : '' }}"
                   style="padding: 1rem 1.5rem; text-decoration: none; color: {{ ($tab ?? 'queue') === 'queue' ? '#2563eb' : '#6b7280' }}; border-bottom: 3px solid {{ ($tab ?? 'queue') === 'queue' ? '#2563eb' : 'transparent' }}; transition: all 0.3s; font-weight: 500;">
                    <i class="fas fa-list-ol"></i> Queue Management
                    <span style="background: #3b82f6; color: white; padding: 2px 8px; border-radius: 12px; font-size: 11px; margin-left: 8px;">
                        {{ $stats['waiting'] + $stats['consulting'] }}
                    </span>
                </a>
                <a href="{{ route('staff.queue.management', ['tab' => 'search']) }}" 
                   class="tab-link {{ ($tab ?? 'queue') === 'search' ? 'active' : '' }}"
                   style="padding: 1rem 1.5rem; text-decoration: none; color: {{ ($tab ?? 'queue') === 'search' ? '#2563eb' : '#6b7280' }}; border-bottom: 3px solid {{ ($tab ?? 'queue') === 'search' ? '#2563eb' : 'transparent' }}; transition: all 0.3s; font-weight: 500;">
                    <i class="fas fa-search"></i> Patient Search
                </a>
                <a href="{{ route('staff.queue.management', ['tab' => 'reports']) }}" 
                   class="tab-link {{ ($tab ?? 'queue') === 'reports' ? 'active' : '' }}"
                   style="padding: 1rem 1.5rem; text-decoration: none; color: {{ ($tab ?? 'queue') === 'reports' ? '#2563eb' : '#6b7280' }}; border-bottom: 3px solid {{ ($tab ?? 'queue') === 'reports' ? '#2563eb' : 'transparent' }}; transition: all 0.3s; font-weight: 500;">
                    <i class="fas fa-chart-bar"></i> Reports & Analytics
                </a>
            </div>
        </div>
    </div>

    @if(($tab ?? 'queue') === 'queue')
    <!-- QUEUE MANAGEMENT TAB CONTENT -->

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-list-ol"></i> Today's Queue (Priority Order)</h3>
            <div class="filter-controls">
                <select id="statusFilter" class="form-control" onchange="filterQueue()">
                    <option value="">All Statuses</option>
                    <option value="Waiting">Waiting</option>
                    <option value="Consulting">Consulting</option>
                    <option value="Completed">Completed</option>
                    <option value="No Show">No Show</option>
                </select>
            </div>
        </div>

        <div class="card-body">
            @if($queue->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-inbox" style="font-size: 4rem; color: #d1d5db;"></i>
                    <p style="color: #6b7280; margin-top: 1rem; font-size: 1.125rem;">No patients in queue today</p>
                    <a href="{{ route('queue.add') }}" class="btn btn-primary" style="margin-top: 1rem;">
                        <i class="fas fa-plus"></i> Add First Patient
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table" id="queueTable">
                        <thead>
                            <tr>
                                <th>Queue #</th>
                                <th>Patient</th>
                                <th>Service</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Arrived</th>
                                <th>Wait Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($queue as $item)
                                <tr data-status="{{ $item->status }}" class="status-{{ strtolower(str_replace(' ', '-', $item->status)) }}">
                                    <td>
                                        <span class="queue-number">{{ $item->queue_number }}</span>
                                    </td>
                                    <td>
                                        <div class="patient-info">
                                            <strong>{{ $item->patient->full_name ?? $item->patient_name }}</strong>
                                            <small>
                                                @if($item->patient)
                                                    ID: {{ $item->patient->id }}
                                                @endif
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-service">{{ $item->service_type }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-priority badge-priority-{{ strtolower($item->priority) }}">
                                            @if($item->priority == 'PWD')
                                                <i class="fas fa-wheelchair"></i> PWD
                                            @elseif($item->priority == 'Pregnant')
                                                <i class="fas fa-baby"></i> Pregnant
                                            @elseif($item->priority == 'Senior')
                                                <i class="fas fa-user-friends"></i> Senior
                                            @else
                                                <i class="fas fa-user"></i> Regular
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-status badge-status-{{ strtolower(str_replace(' ', '-', $item->status)) }}">
                                            @if($item->status == 'Waiting')
                                                <i class="fas fa-clock"></i>
                                            @elseif($item->status == 'Consulting')
                                                <i class="fas fa-user-md"></i>
                                            @elseif($item->status == 'Completed')
                                                <i class="fas fa-check-circle"></i>
                                            @elseif($item->status == 'No Show')
                                                <i class="fas fa-user-times"></i>
                                            @endif
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                    <td>{{ $item->arrived_at ? $item->arrived_at->format('h:i A') : 'N/A' }}</td>
                                    <td>
                                        @if($item->status == 'Waiting' || $item->status == 'Consulting')
                                            <span class="wait-time">
                                                {{ $item->arrived_at ? $item->arrived_at->diffForHumans(null, true) : 'N/A' }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            @if($item->status == 'Waiting')
                                                <form method="POST" action="{{ route('staff.queue.call-next', $item->id) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-primary" title="Call Next">
                                                        <i class="fas fa-phone"></i> Call
                                                    </button>
                                                </form>
                                            @endif

                                            @if($item->status == 'Consulting')
                                                <button 
                                                    type="button" 
                                                    class="btn btn-sm btn-success" 
                                                    onclick="openServeModal({{ $item->id }}, '{{ $item->patient->full_name ?? $item->patient_name }}')"
                                                    title="Mark as Served"
                                                >
                                                    <i class="fas fa-check"></i> Complete
                                                </button>
                                            @endif

                                            @if($item->status == 'Waiting')
                                                <form method="POST" action="{{ route('staff.queue.mark-no-show', $item->id) }}" style="display: inline;">
                                                    @csrf
                                                    <button 
                                                        type="submit" 
                                                        class="btn btn-sm btn-warning" 
                                                        onclick="return confirm('Mark this patient as No Show?')"
                                                        title="Mark No Show"
                                                    >
                                                        <i class="fas fa-user-times"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Mark as Served Modal -->
<div id="serveModal" class="modal">
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header">
            <h3><i class="fas fa-check-circle"></i> Complete Consultation</h3>
            <button type="button" class="close-btn" onclick="closeServeModal()">&times;</button>
        </div>
        <form id="serveForm" method="POST">
            @csrf
            <div class="modal-body">
                <p><strong>Patient:</strong> <span id="patientName"></span></p>
                
                <div class="form-group">
                    <label for="symptoms">Symptoms</label>
                    <textarea 
                        id="symptoms" 
                        name="symptoms" 
                        class="form-control" 
                        rows="2"
                        placeholder="Enter patient symptoms..."
                    ></textarea>
                </div>

                <div class="form-group">
                    <label for="diagnosis">Diagnosis <span class="text-danger">*</span></label>
                    <textarea 
                        id="diagnosis" 
                        name="diagnosis" 
                        class="form-control" 
                        rows="3"
                        placeholder="Enter diagnosis..."
                        required
                    ></textarea>
                </div>

                <div class="form-group">
                    <label for="treatment">Treatment Plan</label>
                    <textarea 
                        id="treatment" 
                        name="treatment" 
                        class="form-control" 
                        rows="3"
                        placeholder="Enter treatment plan..."
                    ></textarea>
                </div>

                <!-- Basic Notes Section (Medicines should be prescribed by doctors) -->
                <div class="form-group">
                    <label><i class="fas fa-info-circle"></i> Treatment Notes</label>
                    <textarea 
                        id="treatment_notes" 
                        name="treatment_notes" 
                        class="form-control" 
                        rows="2"
                        placeholder="Brief treatment notes (detailed prescriptions should be handled by doctors)..."
                    ></textarea>
                    <small style="color: #6b7280; font-size: 0.75rem;">Note: Medicine prescriptions should be handled by qualified medical staff</small>
                </div>

                <div class="form-group">
                    <label for="notes">Additional Notes</label>
                    <textarea 
                        id="notes" 
                        name="notes" 
                        class="form-control" 
                        rows="2"
                        placeholder="Any additional notes..."
                    ></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeServeModal()">Cancel</button>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-check"></i> Mark as Served
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.container {
    padding: 2rem;
    max-width: 1600px;
    margin: 0 auto;
}

.quick-tools-card {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.quick-tools-card h4 {
    margin: 0 0 1rem 0;
    color: #1f2937;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.quick-tools-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
}

.tool-link {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem;
    background: #f9fafb;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    text-decoration: none;
    color: #374151;
    transition: all 0.2s;
    text-align: center;
}

.tool-link:hover {
    background: #10b981;
    color: white;
    border-color: #10b981;
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);
}

.tool-link.active {
    background: #10b981;
    color: white;
    border-color: #10b981;
}

.tool-link i {
    font-size: 1.5rem;
}

.tool-link span {
    font-weight: 500;
    font-size: 0.875rem;
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

.status-waiting {
    border-left: 4px solid #f59e0b;
}

.status-consulting {
    border-left: 4px solid #3b82f6;
}

.status-completed {
    border-left: 4px solid #10b981;
}

.status-no-show {
    border-left: 4px solid #ef4444;
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

.text-link {
    color: #3b82f6;
    text-decoration: none;
}

.text-link:hover {
    text-decoration: underline;
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

.badge-priority-pwd {
    background: #dbeafe;
    color: #1e40af;
}

.badge-priority-pregnant {
    background: #fce7f3;
    color: #9f1239;
}

.badge-priority-senior {
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

.badge-status-no-show {
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

.btn-success {
    background: #10b981;
    color: white;
}

.btn-success:hover {
    background: #059669;
}

.btn-warning {
    background: #f59e0b;
    color: white;
}

.btn-warning:hover {
    background: #d97706;
}

.form-control {
    padding: 0.625rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.875rem;
    width: 100%;
}

.form-control:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.alert {
    padding: 1rem;
    border-radius: 6px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #6ee7b7;
}

.alert-danger {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.text-muted {
    color: #9ca3af;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: white;
    margin: 5% auto;
    border-radius: 8px;
    width: 90%;
    max-width: 600px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    color: #1f2937;
}

.close-btn {
    background: none;
    border: none;
    font-size: 2rem;
    color: #6b7280;
    cursor: pointer;
    line-height: 1;
}

.close-btn:hover {
    color: #1f2937;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    padding: 1.5rem;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #374151;
    font-weight: 500;
}

.form-group textarea {
    resize: vertical;
}
</style>

<script>
// Auto-refresh every 30 seconds
setTimeout(() => {
    location.reload();
}, 30000);

// Medicine management removed from system
const medicines = [];
let medicineCounter = 0;

function filterQueue() {
    const status = document.getElementById('statusFilter').value;
    const rows = document.querySelectorAll('#queueTable tbody tr');
    
    rows.forEach(row => {
        const rowStatus = row.getAttribute('data-status');
        row.style.display = (!status || rowStatus === status) ? '' : 'none';
    });
}

function addMedicine() {
    const container = document.getElementById('medicinesList');
    const index = medicineCounter++;
    
    const medicineRow = document.createElement('div');
    medicineRow.className = 'medicine-row';
    medicineRow.style.cssText = 'border: 1px solid #e5e7eb; padding: 15px; border-radius: 6px; margin-bottom: 10px; background: #f9fafb;';
    medicineRow.innerHTML = `
        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 10px; align-items: start;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-size: 0.875rem; font-weight: 500;">Medicine <span style="color: #dc2626;">*</span></label>
                <select name="prescribed_medicines[${index}][medicine_id]" class="form-control medicine-select" required onchange="updateMedicineName(this, ${index})">
                    <option value="">-- Select Medicine --</option>
                    ${medicines.map(m => `
                        <option value="${m.id}" data-name="${m.name}" data-unit="${m.unit}" data-stock="${m.stock}">
                            ${m.name} (Stock: ${m.stock} ${m.unit})
                        </option>
                    `).join('')}
                </select>
                <input type="hidden" name="prescribed_medicines[${index}][name]" class="medicine-name-${index}">
                <input type="hidden" name="prescribed_medicines[${index}][unit]" class="medicine-unit-${index}">
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-size: 0.875rem; font-weight: 500;">Quantity <span style="color: #dc2626;">*</span></label>
                <input type="number" name="prescribed_medicines[${index}][quantity]" class="form-control" min="1" step="1" required placeholder="0">
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-size: 0.875rem; font-weight: 500;">Instructions</label>
                <input type="text" name="prescribed_medicines[${index}][instructions]" class="form-control" placeholder="e.g., 3x a day">
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-size: 0.875rem; font-weight: 500;">&nbsp;</label>
                <button type="button" class="btn btn-sm" style="background: #dc2626; color: white;" onclick="removeMedicine(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    container.appendChild(medicineRow);
}

function updateMedicineName(selectElement, index) {
    const selected = selectElement.selectedOptions[0];
    if (selected) {
        document.querySelector(`.medicine-name-${index}`).value = selected.getAttribute('data-name') || '';
        document.querySelector(`.medicine-unit-${index}`).value = selected.getAttribute('data-unit') || 'pcs';
    }
}

function removeMedicine(button) {
    button.closest('.medicine-row').remove();
}

function openServeModal(queueId, patientName) {
    document.getElementById('patientName').textContent = patientName;
    document.getElementById('serveForm').action = `/staff/queue/${queueId}/mark-served`;
    document.getElementById('serveModal').style.display = 'block';
    // Reset medicines list
    document.getElementById('medicinesList').innerHTML = '';
    medicineCounter = 0;
}

function closeServeModal() {
    document.getElementById('serveModal').style.display = 'none';
    document.getElementById('serveForm').reset();
    document.getElementById('medicinesList').innerHTML = '';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('serveModal');
    if (event.target == modal) {
        closeServeModal();
    }
}
</script>

    @elseif(($tab ?? 'queue') === 'reports')
    <!-- REPORTS & ANALYTICS TAB CONTENT -->
    <div class="card" style="margin-bottom: 2rem;">
        <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <h3 style="margin: 0;"><i class="fas fa-chart-line"></i> Reports & Analytics</h3>
            <p style="margin: 0.5rem 0 0; opacity: 0.9; font-size: 0.875rem;">
                Generate detailed reports and view queue analytics
            </p>
        </div>
        <div class="card-body">
            <form action="{{ route('reports.generate') }}" method="POST" target="_blank">
                @csrf
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                    <!-- Report Type -->
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #374151;">
                            <i class="fas fa-file-alt"></i> Report Type
                        </label>
                        <select name="report_type" class="form-control" required>
                            <option value="">Select Report Type</option>
                            <option value="queue">Queue Summary Report</option>
                            <option value="service">Service Performance Report</option>
                            <option value="daily">Daily Operations Report</option>
                            <option value="patient_flow">Patient Flow Analysis</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #374151;">
                            <i class="fas fa-calendar"></i> From Date
                        </label>
                        <input type="date" name="start_date" class="form-control" value="{{ today()->format('Y-m-d') }}" required>
                    </div>

                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #374151;">
                            <i class="fas fa-calendar"></i> To Date
                        </label>
                        <input type="date" name="end_date" class="form-control" value="{{ today()->format('Y-m-d') }}" required>
                    </div>

                    <!-- Service Filter -->
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #374151;">
                            <i class="fas fa-hospital"></i> Service (Optional)
                        </label>
                        <select name="service_id" class="form-control">
                            <option value="">All Services</option>
                            @foreach(\App\Models\Service::where('is_active', true)->get() as $service)
                                <option value="{{ $service->id }}">{{ $service->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem;">
                    <button type="submit" name="format" value="pdf" class="btn btn-primary" style="flex: 1;">
                        <i class="fas fa-file-pdf"></i> Generate PDF Report
                    </button>
                    <button type="submit" name="format" value="excel" class="btn btn-success" style="flex: 1;">
                        <i class="fas fa-file-excel"></i> Export to Excel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Analytics Overview -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
        <div class="card">
            <div class="card-header" style="background: #3b82f6; color: white;">
                <h4 style="margin: 0; font-size: 1rem;"><i class="fas fa-chart-bar"></i> Today's Performance</h4>
            </div>
            <div class="card-body">
                <div style="display: grid; gap: 1rem;">
                    <div style="display: flex; justify-content: space-between; padding-bottom: 0.75rem; border-bottom: 1px solid #e5e7eb;">
                        <span style="color: #6b7280;">Total Patients:</span>
                        <strong style="font-size: 1.25rem; color: #111827;">{{ $stats['total_today'] }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding-bottom: 0.75rem; border-bottom: 1px solid #e5e7eb;">
                        <span style="color: #6b7280;">Completed:</span>
                        <strong style="font-size: 1.25rem; color: #10b981;">{{ $stats['completed'] }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding-bottom: 0.75rem; border-bottom: 1px solid #e5e7eb;">
                        <span style="color: #6b7280;">Currently Waiting:</span>
                        <strong style="font-size: 1.25rem; color: #f59e0b;">{{ $stats['waiting'] }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #6b7280;">Avg Wait Time:</span>
                        <strong style="font-size: 1.25rem; color: #8b5cf6;">{{ $stats['average_wait_time'] }} min</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header" style="background: #10b981; color: white;">
                <h4 style="margin: 0; font-size: 1rem;"><i class="fas fa-hospital"></i> Service Breakdown</h4>
            </div>
            <div class="card-body">
                @if(count($stats['service_breakdown']) > 0)
                    <div style="display: grid; gap: 0.75rem;">
                        @foreach($stats['service_breakdown'] as $serviceName => $count)
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: #374151;">{{ $serviceName }}</span>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <div style="width: 100px; background: #e5e7eb; height: 8px; border-radius: 4px; overflow: hidden;">
                                        <div style="width: {{ min(($count / max(array_values($stats['service_breakdown']))) * 100, 100) }}%; background: #10b981; height: 100%;"></div>
                                    </div>
                                    <strong style="min-width: 30px; text-align: right;">{{ $count }}</strong>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="text-align: center; color: #9ca3af; padding: 2rem 0;">No data available</p>
                @endif
            </div>
        </div>
    </div>

    @elseif(($tab ?? 'queue') === 'search')
    <!-- PATIENT SEARCH TAB CONTENT -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-search"></i> Patient Search</h3>
            <p style="margin: 0.5rem 0 0; color: #6b7280; font-size: 0.875rem;">
                Search for patients by name or contact number
            </p>
        </div>
        <div class="card-body">
            <!-- Search Form -->
            <form method="GET" action="{{ route('staff.queue.management') }}" style="margin-bottom: 2rem;">
                <input type="hidden" name="tab" value="search">
                <div style="display: flex; gap: 1rem;">
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Search by patient name or contact..."
                           value="{{ request('search') }}"
                           style="flex: 1;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </form>

            <!-- Search Results -->
            @if($searchResults)
                @if($searchResults->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-user-slash" style="font-size: 4rem; color: #d1d5db;"></i>
                        <p style="color: #6b7280; margin-top: 1rem; font-size: 1.125rem;">
                            No patients found matching "{{ request('search') }}"
                        </p>
                    </div>
                @else
                    <div style="margin-bottom: 1rem; color: #6b7280;">
                        Found {{ $searchResults->total() }} patient(s)
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Patient Name</th>
                                    <th>Contact</th>
                                    <th>Recent Visits</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($searchResults as $patient)
                                    <tr>
                                        <td>
                                            <strong>{{ $patient->full_name }}</strong>
                                            <br>
                                            <small style="color: #6b7280;">ID: {{ $patient->id }}</small>
                                        </td>
                                        <td>{{ $patient->contact ?? 'N/A' }}</td>
                                        <td>
                                            @if($patient->queueEntries && $patient->queueEntries->count() > 0)
                                                <div style="font-size: 0.875rem;">
                                                    @foreach($patient->queueEntries->take(3) as $entry)
                                                        <div style="margin-bottom: 0.25rem;">
                                                            <span class="badge badge-secondary">{{ $entry->service_type }}</span>
                                                            <span style="color: #6b7280;">{{ $entry->created_at->format('M d, Y') }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span style="color: #9ca3af;">No visits</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('staff.patient.history', $patient->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-history"></i> View History
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div style="margin-top: 1rem;">
                        {{ $searchResults->appends(['tab' => 'search', 'search' => request('search')])->links() }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <i class="fas fa-search" style="font-size: 4rem; color: #d1d5db;"></i>
                    <p style="color: #6b7280; margin-top: 1rem; font-size: 1.125rem;">
                        Enter a patient name or contact to search
                    </p>
                </div>
            @endif
        </div>
    </div>
    @endif

</div>

<style>
.stat-card.stat-pending .stat-icon { 
    background: #fee2e2; 
    color: #dc2626; 
}
.tab-link:hover {
    background: #f3f4f6;
    color: #2563eb !important;
}
</style>

<!-- Rejection Modal -->
<div id="rejectModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
    <div class="modal-content" style="background-color: #fefefe; margin: 10% auto; padding: 2rem; border-radius: 12px; width: 90%; max-width: 500px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="margin: 0; color: #dc2626;"><i class="fas fa-times-circle"></i> Reject Queue Request</h3>
            <button onclick="closeRejectModal()" style="background: none; border: none; font-size: 1.5rem; color: #9ca3af; cursor: pointer;">&times;</button>
        </div>
        <form id="rejectForm" method="POST" action="">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #374151;">Rejection Reason *</label>
                <select name="rejection_reason" class="form-control" required style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 6px;">
                    <option value="">Select reason...</option>
                    <option value="Service not available on selected date">Service not available on selected date</option>
                    <option value="Fully booked - please select another date">Fully booked - please select another date</option>
                    <option value="Missing or invalid ID verification">Missing or invalid ID verification</option>
                    <option value="Duplicate request">Duplicate request</option>
                    <option value="Service requires different department">Service requires different department</option>
                    <option value="Patient no-show history">Patient no-show history</option>
                    <option value="Other">Other (specify in notes)</option>
                </select>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #374151;">Additional Notes (Optional)</label>
                <textarea name="staff_notes" rows="3" class="form-control" placeholder="Add any additional details..." style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 6px;"></textarea>
            </div>
            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
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
function showRequestDetails(requestId) {
    const detailsRow = document.getElementById('details-' + requestId);
    if (detailsRow) {
        detailsRow.style.display = detailsRow.style.display === 'none' ? 'table-row' : 'none';
    }
}

function hideRequestDetails(requestId) {
    const detailsRow = document.getElementById('details-' + requestId);
    if (detailsRow) {
        detailsRow.style.display = 'none';
    }
}

function showRejectModal(requestId) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    form.action = '/staff/queue/' + requestId + '/reject';
    modal.style.display = 'block';
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    modal.style.display = 'none';
    form.reset();
}

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const modal = document.getElementById('rejectModal');
    if (event.target == modal) {
        closeRejectModal();
    }
});

// Close modal with ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeRejectModal();
    }
});
</script>
@endsection
