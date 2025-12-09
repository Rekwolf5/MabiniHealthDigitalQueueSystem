@extends('layouts.app')

@section('title', 'Queue Management - Mabini Health Center')
@section('page-title', 'Queue Management')

@section('content')
<div class="page-header">
    <div class="header-actions">
        <a href="{{ route('queue.add') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Add to Queue
        </a>
    </div>
</div>

<!-- Doctor Filter -->
<div class="dashboard-section" style="margin-bottom: 1rem;">
    <div style="background: white; padding: 1rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        @if(request('doctor_id') || request('unassigned'))
            <div style="background: #dbeafe; padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem; border-left: 4px solid #3b82f6;">
                <strong style="color: #1e40af;">
                    <i class="fas fa-filter"></i> Active Filter:
                </strong>
                <span style="color: #1f2937; margin-left: 0.5rem;">
                    @if(request('unassigned'))
                        Showing Unassigned Patients Only
                    @elseif(request('doctor_id'))
                        @php
                            $selectedDoctor = $doctors->firstWhere('id', request('doctor_id'));
                        @endphp
                        Showing Patients for Dr. {{ $selectedDoctor->name ?? 'Unknown' }}
                    @endif
                </span>
                <a href="{{ route('queue.index') }}" style="margin-left: 1rem; color: #ef4444; text-decoration: underline; font-size: 0.875rem;">
                    <i class="fas fa-times-circle"></i> Clear
                </a>
            </div>
        @endif
        <form method="GET" action="{{ route('queue.index') }}" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
            <label for="doctor_id" style="font-weight: 600; color: #374151; margin: 0;">
                <i class="fas fa-filter"></i> Filter by Doctor:
            </label>
            <select name="doctor_id" id="doctor_id" class="form-control" onchange="this.form.submit()" style="max-width: 250px;">
                <option value="">All Doctors</option>
                <option value="" disabled>──────────</option>
                <option value="" {{ request('unassigned') === '1' ? 'selected' : '' }}>
                    {{ request('unassigned') === '1' ? '✓ Unassigned Only' : 'Unassigned Only' }}
                </option>
                <option value="" disabled>──────────</option>
                @foreach($doctors as $doctor)
                    <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>
                        Dr. {{ $doctor->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" name="unassigned" value="1" class="btn btn-outline-secondary">
                <i class="fas fa-filter"></i> Show Unassigned
            </button>
            @if(request('doctor_id') || request('unassigned'))
                <a href="{{ route('queue.index') }}" class="btn btn-outline-danger">
                    <i class="fas fa-times"></i> Clear Filter
                </a>
            @endif
        </form>
    </div>
</div>

@if($queue->count() > 0)
    <!-- Active Queues (Waiting and Consulting) -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
        
        <!-- Waiting Queue Column -->
        <div class="card" id="waiting-card">
            <div class="card-header" style="background: #fef3c7; border-bottom: 3px solid #f59e0b; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; color: #92400e;">
                    <i class="fas fa-clock"></i> Waiting Queue 
                    <span class="badge" style="background: #f59e0b; color: white; margin-left: 0.5rem;">
                        {{ $queue->filter(function($item) { 
                            $status = strtolower($item->status);
                            return ($status === 'waiting' || $status === 'pending') && 
                                   (!isset($item->approval_status) || $item->approval_status === 'approved' || $item->approval_status === 'pending');
                        })->count() }}
                    </span>
                    @php $skippedQueue = $queue->filter(function($item) { return strtolower($item->status) === 'skipped'; }); @endphp
                    @if($skippedQueue->count() > 0)
                        <span class="badge" style="background: #6b7280; color: white; margin-left: 0.5rem; font-size: 0.75rem;">
                            +{{ $skippedQueue->count() }} Skipped
                        </span>
                    @endif
                </h3>
                <button type="button" onclick="expandColumn('waiting')" class="btn btn-sm" style="background: #f59e0b; color: white; padding: 0.375rem 0.75rem;" title="Expand to fullscreen">
                    <i class="fas fa-expand-alt"></i>
                </button>
            </div>
            <div class="card-body" id="waiting-body" style="max-height: 600px; overflow-y: auto;">
                @php $waitingQueue = $queue->filter(function($item) { 
                    // Show items that are in Waiting or Pending status
                    $status = strtolower($item->status);
                    return ($status === 'waiting' || $status === 'pending') && 
                           (!isset($item->approval_status) || $item->approval_status === 'approved' || $item->approval_status === 'pending');
                }); @endphp
                @if($waitingQueue->isEmpty())
                    <div class="empty-state" style="padding: 2rem; text-align: center;">
                        <i class="fas fa-check-circle" style="font-size: 3rem; color: #d1d5db;"></i>
                        <p style="color: #6b7280; margin-top: 1rem;">No patients waiting</p>
                    </div>
                @else
                    @foreach($waitingQueue as $item)
                        @php
                            $isPending = strtolower($item->status) === 'pending';
                            $cardBg = $isPending ? '#fef3c7' : 'white';
                            $borderColor = $isPending ? '#f59e0b' : '#e5e7eb';
                        @endphp
                        <div class="queue-card" style="background: {{ $cardBg }}; border: 1px solid {{ $borderColor }}; border-radius: 8px; padding: 1rem; margin-bottom: 1rem; border-left: 4px solid {{ $item->priority == 'Emergency' ? '#dc2626' : ($item->priority == 'Urgent' ? '#f59e0b' : '#059669') }};">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
                                <div>
                                    <span style="font-size: 1.5rem; font-weight: 700; color: #f59e0b;">
                                        #{{ $item->queue_number }}
                                    </span>
                                    @if($isPending)
                                        <span class="badge" style="margin-left: 0.5rem; background: #fbbf24; color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem;">
                                            <i class="fas fa-hourglass-half"></i> PENDING APPROVAL
                                        </span>
                                    @endif
                                    <span class="badge" style="margin-left: 0.5rem; background: {{ $item->priority == 'Emergency' ? '#fee2e2' : ($item->priority == 'Urgent' ? '#fed7aa' : '#fef3c7') }}; color: {{ $item->priority == 'Emergency' ? '#991b1b' : ($item->priority == 'Urgent' ? '#9a3412' : '#92400e') }}; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem;">
                                        @if($item->priority == 'Emergency')
                                            <i class="fas fa-exclamation-triangle"></i>
                                        @elseif($item->priority == 'Urgent')
                                            <i class="fas fa-exclamation-circle"></i>
                                        @endif
                                        {{ $item->priority }}
                                    </span>
                                </div>
                                <span style="color: #6b7280; font-size: 0.875rem;">
                                    <i class="fas fa-clock"></i> {{ $item->arrived_at ? $item->arrived_at->diffForHumans(null, true) : 'N/A' }}
                                </span>
                            </div>
                            
                            <div style="margin-bottom: 0.75rem;">
                                <strong style="font-size: 1.125rem; color: #1f2937;">{{ $item->patient->full_name }}</strong>
                            </div>

                            <div style="margin-bottom: 0.75rem; font-size: 0.875rem; color: #6b7280;">
                                <div><i class="fas fa-calendar"></i> Arrived: {{ $item->arrived_at->format('h:i A') }}</div>
                                <div><i class="fas fa-briefcase-medical"></i> {{ $item->service_type }}</div>
                                @if($item->assignedDoctor)
                                    <div style="color: #3b82f6;">
                                        <i class="fas fa-user-md"></i> Dr. {{ $item->assignedDoctor->name }}
                                        @if($item->doctor_accepted_at)
                                            <span style="background: #d1fae5; color: #065f46; padding: 0.125rem 0.5rem; border-radius: 8px; font-size: 0.75rem; margin-left: 0.25rem;">
                                                <i class="fas fa-check-circle"></i> Accepted
                                            </span>
                                        @else
                                            <span style="background: #fef3c7; color: #92400e; padding: 0.125rem 0.5rem; border-radius: 8px; font-size: 0.75rem; margin-left: 0.25rem;">
                                                <i class="fas fa-clock"></i> Pending
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <div style="color: #9ca3af;">
                                        <i class="fas fa-user-md"></i> <em>No doctor assigned</em>
                                    </div>
                                @endif
                            </div>

                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <!-- Assign Doctor -->
                                <form method="POST" action="{{ route('queue.assign-doctor', $item->id) }}" style="display: flex; gap: 0.5rem;">
                                    @csrf
                                    <select name="doctor_id" class="form-control form-control-sm" required style="flex: 1; font-size: 0.875rem;">
                                        <option value="">{{ $item->assignedDoctor ? 'Reassign Doctor' : 'Assign Doctor' }}</option>
                                        @foreach($doctors as $doctor)
                                            <option value="{{ $doctor->id }}" {{ $item->assigned_doctor_id == $doctor->id ? 'selected' : '' }}>
                                                Dr. {{ $doctor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-info">
                                        <i class="fas fa-user-md"></i>
                                    </button>
                                </form>

                                <div style="display: flex; gap: 0.5rem;">
                                    @if($isPending)
                                        <!-- Approve button for pending patients -->
                                        <form method="POST" action="{{ route('queue.updateStatus', $item->id) }}" style="flex: 1;">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="Waiting">
                                            <button type="submit" class="btn btn-sm btn-primary" style="width: 100%;">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('staff.vital-signs.form', $item->id) }}" class="btn btn-sm btn-warning" style="flex: 1;">
                                            <i class="fas fa-heartbeat"></i> Vitals
                                        </a>
                                    @endif
                                    
                                    <form method="POST" action="{{ route('queue.updateStatus', $item->id) }}" style="flex: 1;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="Consulting">
                                        <button type="submit" class="btn btn-sm btn-success" style="width: 100%;">
                                            <i class="fas fa-play"></i> Start
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('queue.updateStatus', $item->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="Skipped">
                                        <button type="submit" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-forward"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
                
                <!-- Skipped Patients Section -->
                @php $skippedQueue = $queue->filter(function($item) { return strtolower($item->status) === 'skipped'; }); @endphp
                @if($skippedQueue->count() > 0)
                    <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 2px dashed #d1d5db;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <h4 style="margin: 0; color: #6b7280; font-size: 0.95rem; font-weight: 600;">
                                <i class="fas fa-forward"></i> Skipped Patients ({{ $skippedQueue->count() }})
                            </h4>
                            <small style="color: #9ca3af; font-size: 0.8rem;">Can be requeued when they return</small>
                        </div>
                        @foreach($skippedQueue as $item)
                            <div class="queue-card" style="background: #f9fafb; border: 1px solid #d1d5db; border-radius: 8px; padding: 1rem; margin-bottom: 1rem; border-left: 4px solid #6b7280; opacity: 0.85;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
                                    <div>
                                        <span style="font-size: 1.25rem; font-weight: 700; color: #6b7280;">
                                            #{{ $item->queue_number }}
                                        </span>
                                        <span class="badge" style="margin-left: 0.5rem; background: #f3f4f6; color: #6b7280; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem;">
                                            <i class="fas fa-forward"></i> Skipped
                                        </span>
                                    </div>
                                    <span style="color: #9ca3af; font-size: 0.875rem;">
                                        <i class="fas fa-clock"></i> {{ $item->updated_at ? $item->updated_at->diffForHumans() : 'N/A' }}
                                    </span>
                                </div>
                                
                                <div style="margin-bottom: 0.75rem;">
                                    <strong style="font-size: 1rem; color: #4b5563;">{{ $item->patient->full_name }}</strong>
                                </div>

                                <div style="margin-bottom: 0.75rem; font-size: 0.875rem; color: #6b7280;">
                                    <div><i class="fas fa-briefcase-medical"></i> {{ $item->service_type }}</div>
                                    @if($item->assignedDoctor)
                                        <div><i class="fas fa-user-md"></i> Dr. {{ $item->assignedDoctor->name }}</div>
                                    @endif
                                </div>

                                <div style="display: flex; gap: 0.5rem;">
                                    <form method="POST" action="{{ route('queue.updateStatus', $item->id) }}" style="flex: 1;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="Waiting">
                                        <button type="submit" class="btn btn-sm btn-success" style="width: 100%;">
                                            <i class="fas fa-redo"></i> Requeue
                                        </button>
                                    </form>
                                    
                                    <form method="POST" action="{{ route('queue.updateStatus', $item->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="Cancelled">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Cancel Queue">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Currently Consulting Column -->
        <div class="card" id="consulting-card">
            <div class="card-header" style="background: #dbeafe; border-bottom: 3px solid #3b82f6; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; color: #1e40af;">
                    <i class="fas fa-user-md"></i> Currently Consulting
                    <span class="badge" style="background: #3b82f6; color: white; margin-left: 0.5rem;">
                        {{ $queue->filter(function($item) { return strtolower($item->status) === 'consulting'; })->count() }}
                    </span>
                </h3>
                <button type="button" onclick="expandColumn('consulting')" class="btn btn-sm" style="background: #3b82f6; color: white; padding: 0.375rem 0.75rem;" title="Expand to fullscreen">
                    <i class="fas fa-expand-alt"></i>
                </button>
            </div>
            <div class="card-body" id="consulting-body" style="max-height: 600px; overflow-y: auto;">
                @php $consultingQueue = $queue->filter(function($item) { return strtolower($item->status) === 'consulting'; }); @endphp
                @if($consultingQueue->isEmpty())
                    <div class="empty-state" style="padding: 2rem; text-align: center;">
                        <i class="fas fa-user-clock" style="font-size: 3rem; color: #d1d5db;"></i>
                        <p style="color: #6b7280; margin-top: 1rem;">No active consultations</p>
                    </div>
                @else
                    @foreach($consultingQueue as $item)
                        <div class="queue-card" style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 1rem; margin-bottom: 1rem; border-left: 4px solid #3b82f6;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
                                <div>
                                    <span style="font-size: 1.5rem; font-weight: 700; color: #3b82f6;">
                                        #{{ $item->queue_number }}
                                    </span>
                                </div>
                                <span style="color: #3b82f6; font-weight: 600; font-size: 0.875rem;">
                                    <i class="fas fa-circle" style="animation: pulse 2s infinite;"></i> IN PROGRESS
                                </span>
                            </div>
                            
                            <div style="margin-bottom: 0.75rem;">
                                <strong style="font-size: 1.125rem; color: #1f2937;">{{ $item->patient->full_name }}</strong>
                            </div>

                            <div style="margin-bottom: 0.75rem; font-size: 0.875rem; color: #6b7280;">
                                <div><i class="fas fa-briefcase-medical"></i> {{ $item->service_type }}</div>
                                <div><i class="fas fa-clock"></i> Duration: {{ $item->arrived_at ? $item->arrived_at->diffForHumans(null, true) : 'N/A' }}</div>
                                @if($item->assignedDoctor)
                                    <div style="color: #3b82f6;">
                                        <i class="fas fa-user-md"></i> Dr. {{ $item->assignedDoctor->name }}
                                    </div>
                                @endif
                            </div>

                            <div style="display: flex; gap: 0.5rem;">
                                <a href="{{ route('staff.vital-signs.form', $item->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-heartbeat"></i>
                                </a>
                                
                                <form method="POST" action="{{ route('queue.updateStatus', $item->id) }}" style="flex: 1;">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="Completed">
                                    <button type="submit" class="btn btn-sm btn-success" style="width: 100%;">
                                        <i class="fas fa-check"></i> Complete
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <!-- Completed Queue Section (Bottom) -->
    <div class="card" id="completed-card">
        <div class="card-header" style="background: #d1fae5; border-bottom: 3px solid #059669; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; color: #065f46;">
                <i class="fas fa-check-circle"></i> Completed Today
                <span class="badge" style="background: #059669; color: white; margin-left: 0.5rem;">
                    {{ $queue->filter(function($item) { return strtolower($item->status) === 'completed'; })->count() }}
                </span>
            </h3>
            <button type="button" onclick="expandColumn('completed')" class="btn btn-sm" style="background: #059669; color: white; padding: 0.375rem 0.75rem;" title="Expand to fullscreen">
                <i class="fas fa-expand-alt"></i>
            </button>
        </div>
        <div class="card-body" id="completed-body">
            @php $completedQueue = $queue->filter(function($item) { return strtolower($item->status) === 'completed'; }); @endphp
            @if($completedQueue->isEmpty())
                <div class="empty-state" style="padding: 2rem; text-align: center;">
                    <i class="fas fa-hourglass-start" style="font-size: 3rem; color: #d1d5db;"></i>
                    <p style="color: #6b7280; margin-top: 1rem;">No completed consultations yet</p>
                </div>
            @else
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem;">
                    @foreach($completedQueue as $item)
                        <div class="queue-card" style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 0.875rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                <span style="font-size: 1.25rem; font-weight: 700; color: #059669;">
                                    #{{ $item->queue_number }}
                                </span>
                            </div>
                            
                            <div style="margin-bottom: 0.5rem;">
                                <strong style="font-size: 1rem; color: #1f2937;">{{ $item->patient->full_name }}</strong>
                            </div>

                            <div style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">
                                <div>{{ $item->service_type }}</div>
                                <div>Completed: {{ $item->updated_at ? $item->updated_at->format('h:i A') : 'N/A' }}</div>
                                @if($item->assignedDoctor)
                                    <div><i class="fas fa-user-md"></i> Dr. {{ $item->assignedDoctor->name }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@else
<div class="dashboard-section">
    <div style="text-align: center; padding: 3rem; color: #6b7280;">
        <i class="fas fa-clipboard-list" style="font-size: 3rem; margin-bottom: 1rem; color: #d1d5db;"></i>
        <h3 style="margin-bottom: 0.5rem;">No patients in queue today</h3>
        <p>Add the first patient to get started.</p>
        <a href="{{ route('queue.add') }}" class="btn btn-primary" style="margin-top: 1rem;">
            <i class="fas fa-plus"></i>
            Add Patient to Queue
        </a>
    </div>
</div>
@endif

<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    overflow: hidden;
}

.card-header {
    padding: 1rem 1.5rem;
}

.card-body {
    padding: 1.5rem;
}

.empty-state {
    text-align: center;
    padding: 3rem 2rem;
}

.btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.8125rem;
}

.btn-primary {
    background: #059669;
    color: white;
}

.btn-primary:hover {
    background: #047857;
}

.btn-info {
    background: #0ea5e9;
    color: white;
}

.btn-info:hover {
    background: #0284c7;
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

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
}

.btn-danger {
    background: #ef4444;
    color: white;
}

.btn-danger:hover {
    background: #dc2626;
}

.btn-outline-secondary {
    background: white;
    color: #6b7280;
    border: 1px solid #d1d5db;
}

.btn-outline-secondary:hover {
    background: #f9fafb;
}

.btn-outline-danger {
    background: white;
    color: #ef4444;
    border: 1px solid #ef4444;
}

.btn-outline-danger:hover {
    background: #fef2f2;
}

.form-control {
    padding: 0.5rem 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.875rem;
}

.form-control:focus {
    outline: none;
    border-color: #059669;
    box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
}

.form-control-sm {
    padding: 0.375rem 0.625rem;
    font-size: 0.8125rem;
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        gap: 1rem;
    }
    
    div[style*="grid-template-columns: 1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}

/* Fullscreen Modal Styles */
.fullscreen-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    z-index: 9999;
    overflow-y: auto;
    animation: fadeIn 0.3s ease;
}

.fullscreen-modal.active {
    display: block;
}

.fullscreen-content {
    max-width: 1400px;
    margin: 2rem auto;
    padding: 2rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    position: relative;
}

.fullscreen-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e5e7eb;
}

.fullscreen-close {
    background: #ef4444;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
}

.fullscreen-close:hover {
    background: #dc2626;
    transform: scale(1.05);
}

.fullscreen-body {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    max-height: calc(100vh - 200px);
    overflow-y: auto;
}

.fullscreen-body .queue-card {
    display: grid;
    grid-template-columns: auto 1fr auto auto;
    gap: 1rem;
    align-items: center;
    padding: 0.75rem 1rem !important;
    margin-bottom: 0 !important;
}

.fullscreen-body .queue-card > div {
    margin-bottom: 0 !important;
}

.fullscreen-body .queue-number-section {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    min-width: 150px;
}

.fullscreen-body .patient-info-section {
    display: flex;
    gap: 2rem;
    align-items: center;
    flex: 1;
}

.fullscreen-body .patient-name {
    font-weight: 600;
    font-size: 1rem;
    min-width: 200px;
}

.fullscreen-body .patient-details {
    display: flex;
    gap: 1.5rem;
    font-size: 0.875rem;
    color: #6b7280;
}

.fullscreen-body .patient-details > div {
    white-space: nowrap;
}

.fullscreen-body .actions-section {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.fullscreen-body .actions-section form {
    margin: 0 !important;
    display: inline-block;
}

.fullscreen-body .btn-sm {
    padding: 0.375rem 0.625rem;
    font-size: 0.8125rem;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@media (max-width: 768px) {
    .fullscreen-content {
        margin: 1rem;
        padding: 1rem;
    }
}
</style>

<!-- Fullscreen Modal Container -->
<div id="fullscreen-modal" class="fullscreen-modal">
    <div class="fullscreen-content">
        <div class="fullscreen-header">
            <h2 id="fullscreen-title" style="margin: 0; font-size: 1.75rem; font-weight: 700;"></h2>
            <button onclick="closeFullscreen()" class="fullscreen-close">
                <i class="fas fa-times"></i> Close
            </button>
        </div>
        <div id="fullscreen-body" class="fullscreen-body"></div>
    </div>
</div>

<script>
function expandColumn(columnName) {
    const modal = document.getElementById('fullscreen-modal');
    const modalTitle = document.getElementById('fullscreen-title');
    const modalBody = document.getElementById('fullscreen-body');
    const sourceBody = document.getElementById(columnName + '-body');
    
    // Set title based on column
    const titles = {
        'waiting': '⏰ Waiting Queue - Full View',
        'consulting': '👨‍⚕️ Currently Consulting - Full View',
        'completed': '✅ Completed Today - Full View'
    };
    
    const colors = {
        'waiting': '#f59e0b',
        'consulting': '#3b82f6',
        'completed': '#059669'
    };
    
    modalTitle.innerHTML = titles[columnName];
    modalTitle.style.color = colors[columnName];
    
    // Clone and restructure the content for compact list view
    const sourceCards = sourceBody.querySelectorAll('.queue-card');
    modalBody.innerHTML = '';
    
    sourceCards.forEach(card => {
        const clone = card.cloneNode(true);
        
        // Extract key information
        const queueNumber = clone.querySelector('[style*="font-size: 1.5rem"]') || clone.querySelector('[style*="font-size: 1.25rem"]');
        const patientName = clone.querySelector('strong[style*="font-size: 1.125rem"]') || clone.querySelector('strong[style*="font-size: 1rem"]');
        const badges = clone.querySelectorAll('.badge');
        const details = clone.querySelector('[style*="font-size: 0.875rem"]');
        const actions = clone.querySelector('[style*="display: flex; gap: 0.5rem"]') || clone.querySelector('.action-buttons');
        
        // Create compact row structure
        const compactCard = document.createElement('div');
        compactCard.className = 'queue-card';
        compactCard.style.cssText = clone.style.cssText;
        
        // Queue number + badges section
        const numberSection = document.createElement('div');
        numberSection.className = 'queue-number-section';
        if (queueNumber) numberSection.appendChild(queueNumber.cloneNode(true));
        badges.forEach(badge => numberSection.appendChild(badge.cloneNode(true)));
        
        // Patient info section
        const infoSection = document.createElement('div');
        infoSection.className = 'patient-info-section';
        
        if (patientName) {
            const nameDiv = document.createElement('div');
            nameDiv.className = 'patient-name';
            nameDiv.textContent = patientName.textContent;
            infoSection.appendChild(nameDiv);
        }
        
        if (details) {
            const detailsDiv = document.createElement('div');
            detailsDiv.className = 'patient-details';
            const detailItems = details.querySelectorAll('div');
            detailItems.forEach(item => {
                const itemClone = item.cloneNode(true);
                detailsDiv.appendChild(itemClone);
            });
            infoSection.appendChild(detailsDiv);
        }
        
        // Actions section
        const actionsSection = document.createElement('div');
        actionsSection.className = 'actions-section';
        if (actions) {
            actionsSection.innerHTML = actions.innerHTML;
        }
        
        // Assemble compact card
        compactCard.appendChild(numberSection);
        compactCard.appendChild(infoSection);
        compactCard.appendChild(actionsSection);
        
        modalBody.appendChild(compactCard);
    });
    
    // If no cards, show empty state
    if (sourceCards.length === 0) {
        modalBody.innerHTML = sourceBody.innerHTML;
    }
    
    // Show modal
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeFullscreen() {
    const modal = document.getElementById('fullscreen-modal');
    modal.classList.remove('active');
    document.body.style.overflow = 'auto';
}

// Close on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeFullscreen();
    }
});

// Close on background click
document.getElementById('fullscreen-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeFullscreen();
    }
});
</script>
@endsection
