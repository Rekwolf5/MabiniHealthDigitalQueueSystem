@extends('layouts.app')

@section('title', 'Front Desk Queue - Mabini Health Center')
@section('page-title', 'Front Desk Queue')

@section('content')
<div class="dashboard-grid">
    <!-- Page Header with Enhanced Design -->
    <div class="dashboard-section" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); color: white; border: none;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">
                    <i class="fas fa-clipboard-list"></i> Front Desk Queue
                </h1>
                <p style="opacity: 0.9;">Manage walk-in patients and queue assignments</p>
            </div>
            <div>
                <button onclick="showAddPatientModal()" class="btn btn-primary" style="background: white; color: #059669; font-weight: 600;">
                    <i class="fas fa-plus"></i>
                    Add Walk-in Patient
                </button>
            </div>
        </div>
    </div>

    <!-- Service Availability Dashboard -->
    <div class="dashboard-section" id="serviceStatusDashboard">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0; font-size: 1.25rem; font-weight: 600;">
                <i class="fas fa-hospital"></i> Service Availability Status
            </h3>
            <span id="lastUpdated" style="font-size: 0.875rem; color: #6b7280;">
                <i class="fas fa-sync-alt fa-spin"></i> Updating...
            </span>
        </div>
        
        <div id="serviceStatusCards" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">
            <!-- Service cards will be loaded here -->
            <div style="text-align: center; padding: 2rem; color: #9ca3af; grid-column: 1 / -1;">
                <i class="fas fa-spinner fa-spin" style="font-size: 2rem;"></i>
                <p style="margin-top: 0.5rem;">Loading services...</p>
            </div>
        </div>
    </div>

    <!-- Statistics Dashboard -->
    <div class="stats-row">
        <div class="stat-card" style="border-left: 4px solid #3b82f6;">
            <div class="stat-icon" style="background: #3b82f6;">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['total_today'] }}</h3>
                <p>Total Today</p>
            </div>
        </div>
        
        <div class="stat-card" style="border-left: 4px solid #eab308;">
            <div class="stat-icon" style="background: #eab308;">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['waiting'] }}</h3>
                <p>Waiting</p>
            </div>
        </div>
        
        <div class="stat-card" style="border-left: 4px solid #6366f1;">
            <div class="stat-icon" style="background: #6366f1;">
                <i class="fas fa-user-clock"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['in_progress'] }}</h3>
                <p>In Progress</p>
            </div>
        </div>
        
        <div class="stat-card" style="border-left: 4px solid #10b981;">
            <div class="stat-icon" style="background: #10b981;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['completed'] }}</h3>
                <p>Completed</p>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="dashboard-section">
        <div style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #1f2937; margin: 0;">
                <i class="fas fa-search" style="color: #059669;"></i> Search & Filter Queue
            </h2>
            <button type="button" 
                    onclick="toggleFilters()" 
                    id="filterToggleBtn"
                    class="btn btn-secondary"
                    style="display: flex; align-items: center; gap: 0.5rem; background: #6b7280; color: white; border: none; padding: 0.625rem 1.25rem; font-weight: 500;">
                <i class="fas fa-filter" id="filterIcon" style="opacity: 1;"></i>
                <span id="filterToggleText">Show Filters</span>
                <i class="fas fa-chevron-down" id="filterChevron" style="opacity: 1;"></i>
            </button>
        </div>
        
        <form method="GET" 
              action="{{ route('front-desk.index') }}" 
              id="filterForm"
              style="background: #f9fafb; padding: 1.5rem; border-radius: 8px; border: 1px solid #e5e7eb; display: none;">
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1rem;">
                <!-- Search Input -->
                <div class="form-group" style="grid-column: span 2;">
                    <label><i class="fas fa-search" style="color: #059669;"></i> Search</label>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search by name, queue number, or contact..." 
                           style="width: 100%;">
                </div>
                
                <!-- Status Filter -->
                <div class="form-group">
                    <label style="color: #1f2937; font-weight: 600;"><i class="fas fa-flag" style="color: #059669;"></i> Status</label>
                    <select name="status" style="width: 100%;">
                        <option value="">All Status</option>
                        <option value="waiting" {{ request('status') === 'waiting' ? 'selected' : '' }}>Waiting</option>
                        <option value="called" {{ request('status') === 'called' ? 'selected' : '' }}>Called</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                
                <!-- Service Filter -->
                <div class="form-group">
                    <label style="color: #1f2937; font-weight: 600;"><i class="fas fa-hospital" style="color: #059669;"></i> Service</label>
                    <select name="service_id" style="width: 100%;">
                        <option value="">All Services</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>
                                {{ $service->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Priority Filter -->
                <div class="form-group">
                    <label style="color: #1f2937; font-weight: 600;"><i class="fas fa-exclamation-triangle" style="color: #059669;"></i> Priority</label>
                    <select name="priority" style="width: 100%;">
                        <option value="">All Priorities</option>
                        <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="senior" {{ request('priority') === 'senior' ? 'selected' : '' }}>Senior Citizen</option>
                        <option value="pwd" {{ request('priority') === 'pwd' ? 'selected' : '' }}>PWD</option>
                        <option value="emergency" {{ request('priority') === 'emergency' ? 'selected' : '' }}>Emergency</option>
                    </select>
                </div>
            </div>
            
            <!-- Filter Buttons -->
            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                <a href="{{ route('front-desk.index') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Queue Table -->
    <div class="data-table-container" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
        <table class="data-table" style="min-width: 100%; width: max-content;">
            <thead>
                <tr style="background: #059669;">
                    <th style="color: black !important;"><i class="fas fa-hashtag"></i> Queue #</th>
                    <th style="color: black !important;"><i class="fas fa-user"></i> Patient Name</th>
                    <th style="color: black !important;"><i class="fas fa-clipboard-list"></i> Reason for Visit</th>
                    <th style="color: black !important;"><i class="fas fa-hospital"></i> Assigned Service</th>
                    <th style="color: black !important;"><i class="fas fa-flag"></i> Priority</th>
                    <th style="color: black !important;"><i class="fas fa-clock"></i> Status</th>
                    <th style="color: black !important;"><i class="fas fa-calendar"></i> Time</th>
                    <th style="color: white !important; text-align: center; min-width: 180px; position: sticky; right: 0; background: #059669; z-index: 10;">
                        <i class="fas fa-cog"></i> Actions
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($queues as $queue)
                <tr>
                    <td>
                        <span style="background: #dbeafe; color: #1e40af; padding: 0.5rem; border-radius: 4px; font-weight: 700; font-family: monospace;">
                            {{ $queue->queue_number }}
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-user-circle" style="color: #6b7280;"></i>
                            <div>
                                <strong>{{ $queue->patient_name }}</strong>
                                @if($queue->age)
                                    <small style="color: #6b7280;">({{ $queue->age }}y)</small>
                                @endif
                                @if($queue->contact_number)
                                    <br><small style="color: #6b7280;">
                                        <i class="fas fa-phone" style="font-size: 0.7rem;"></i> {{ $queue->contact_number }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="max-width: 250px;">
                            <strong style="color: #1f2937;">{{ $queue->chief_complaint }}</strong>
                            @if($queue->allergies)
                                <br><span style="background: #fef2f2; color: #dc2626; padding: 0.125rem 0.25rem; border-radius: 3px; font-size: 0.75rem; margin-top: 0.25rem; display: inline-block;">
                                    <i class="fas fa-exclamation-triangle"></i> {{ Str::limit($queue->allergies, 40) }}
                                </span>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if($queue->service)
                            <span style="background: #e0e7ff; color: #3730a3; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.875rem;">
                                {{ $queue->service->name }}
                            </span>
                        @else
                            <span style="color: #6b7280;">Not assigned</span>
                        @endif
                    </td>
                    <td>
                        <span class="btn-sm" style="background: 
                            {{ $queue->priority === 'emergency' ? '#fee2e2; color: #991b1b' : 
                               ($queue->priority === 'senior' ? '#f3e8ff; color: #7c3aed' : 
                               ($queue->priority === 'pwd' ? '#dbeafe; color: #1e40af' : 
                               ($queue->priority === 'pregnant' ? '#fce7f3; color: #be185d' : '#f0f9ff; color: #0369a1'))) }}">
                            <i class="fas fa-{{ $queue->priority === 'emergency' ? 'exclamation-triangle' : 
                                              ($queue->priority === 'senior' ? 'user-friends' : 
                                              ($queue->priority === 'pwd' ? 'wheelchair' : 
                                              ($queue->priority === 'pregnant' ? 'baby' : 'user'))) }}"></i>
                            {{ $queue->priority === 'normal' ? 'Regular' : ucfirst($queue->priority) }}
                        </span>
                    </td>
                    <td>
                        <span class="btn-sm" style="background: 
                            {{ $queue->status === 'waiting' ? '#fef3c7; color: #92400e' : 
                               ($queue->status === 'called' ? '#dbeafe; color: #1e40af' : 
                               ($queue->status === 'in_progress' ? '#f3e8ff; color: #7c3aed' : 
                               ($queue->status === 'completed' ? '#dcfce7; color: #166534' : '#fee2e2; color: #991b1b'))) }}">
                            <i class="fas fa-{{ $queue->status === 'waiting' ? 'clock' : 
                                              ($queue->status === 'called' ? 'phone' : 
                                              ($queue->status === 'in_progress' ? 'user-clock' : 
                                              ($queue->status === 'completed' ? 'check-circle' : 'times-circle'))) }}"></i>
                            {{ ucfirst(str_replace('_', ' ', $queue->status)) }}
                        </span>
                    </td>
                    <td>
                        <div style="font-size: 0.875rem;">
                            <i class="fas fa-calendar" style="color: #9ca3af;"></i>
                            {{ $queue->arrived_at->format('g:i A') }}
                            <br><small style="color: #6b7280;">
                                {{ $queue->arrived_at->diffForHumans() }}
                            </small>
                        </div>
                    </td>
                        {{ $queue->arrived_at->format('h:i A') }}<br>
                        <small style="color: #6b7280;">{{ $queue->arrived_at->diffForHumans() }}</small>
                    </td>
                    <td style="position: sticky; right: 0; background: white; box-shadow: -2px 0 4px rgba(0,0,0,0.05); z-index: 5;">
                        <div class="action-buttons" style="justify-content: center; display: flex; flex-wrap: nowrap; gap: 0.25rem; padding: 0.25rem;">
                            @if($queue->status === 'waiting')
                                <form method="POST" action="{{ route('front-desk.call', $queue->id) }}" style="display: inline; margin: 0;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-info" title="Call Patient" style="min-width: 35px; padding: 0.375rem;">
                                        <i class="fas fa-phone"></i>
                                    </button>
                                </form>
                            @endif
                            
            <!-- Print Queue Ticket Button -->
            <button onclick="printTicket('{{ $queue->id }}', '{{ $queue->queue_number }}', '{{ addslashes($queue->patient_name) }}', '{{ $queue->service ? addslashes($queue->service->name) : 'Unassigned' }}', '{{ $queue->priority }}')" 
                    class="btn btn-sm btn-success" title="Print Queue Ticket" style="min-width: 35px; padding: 0.375rem; background: #10b981; border: none;">
                <i class="fas fa-print"></i>
            </button>                            @if(in_array($queue->status, ['called', 'in_progress']))
                                <form method="POST" action="{{ route('front-desk.complete', $queue->id) }}" style="display: inline; margin: 0;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" title="Mark Complete" style="min-width: 35px; padding: 0.375rem;">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            @endif
                            
                            <button onclick="editQueue({{ $queue->id }}, '{{ $queue->patient_name }}', '{{ $queue->contact_number }}', {{ $queue->service_id ?? 'null' }}, '{{ $queue->priority }}', '{{ addslashes($queue->notes) }}')" 
                                    class="btn btn-sm btn-warning" title="Edit" style="min-width: 35px; padding: 0.375rem;">
                                <i class="fas fa-edit"></i>
                            </button>
                            
                            @if($queue->status !== 'completed')
                                <form method="POST" action="{{ route('front-desk.cancel', $queue->id) }}" 
                                      style="display: inline; margin: 0;"
                                      onsubmit="return confirm('Are you sure you want to cancel this queue entry?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger" title="Cancel" style="min-width: 35px; padding: 0.375rem;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 3rem;">
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                            <div style="background: #f3f4f6; border-radius: 50%; padding: 2rem; width: 120px; height: 120px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-clipboard-list" style="font-size: 3rem; color: #d1d5db;"></i>
                            </div>
                            <div>
                                <h3 style="font-size: 1.25rem; font-weight: 700; color: #6b7280; margin-bottom: 0.5rem;">No Patients in Queue</h3>
                                <p style="color: #9ca3af;">Add walk-in patients to get started</p>
                                <button onclick="showAddPatientModal()" class="btn btn-primary" style="margin-top: 1rem;">
                                    <i class="fas fa-plus"></i> Add First Patient
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($queues->hasPages())
    <div style="margin-top: 1.5rem; display: flex; justify-content: center;">
        {{ $queues->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<!-- Add Patient Modal -->
<div id="addPatientModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
    <div class="modal-content" style="background-color: #fefefe; margin: 5% auto; padding: 20px; border: none; border-radius: 8px; width: 90%; max-width: 500px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; color: #1f2937;"><i class="fas fa-plus-circle"></i> Add Walk-in Patient</h3>
            <button onclick="hideAddPatientModal()" style="background: none; border: none; font-size: 1.5rem; color: #6b7280; cursor: pointer;">&times;</button>
        </div>
        
        <form method="POST" action="{{ route('front-desk.store') }}" id="addPatientForm">
            @csrf
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="patient_name" style="display: block; margin-bottom: 5px; font-weight: 600;">Patient Name *</label>
                <input type="text" id="patient_name" name="patient_name" required style="width: 100%;">
            </div>
            
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div class="form-group">
                    <label for="contact_number" style="display: block; margin-bottom: 5px; font-weight: 600;">Contact Number</label>
                    <input type="text" id="contact_number" name="contact_number" placeholder="For follow-up or emergencies" style="width: 100%;">
                </div>
                
                <div class="form-group">
                    <label for="age" style="display: block; margin-bottom: 5px; font-weight: 600;">Age (Optional)</label>
                    <input type="number" id="age" name="age" min="1" max="120" placeholder="Age" style="width: 100%;">
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="chief_complaint" style="display: block; margin-bottom: 5px; font-weight: 600;">Reason for Visit *</label>
                <input type="text" id="chief_complaint" name="chief_complaint" placeholder="e.g., Fever, Toothache, Check-up, Blood pressure monitoring" required style="width: 100%;">
                <small style="color: #6b7280; font-size: 0.875rem;">This helps us route you to the right service</small>
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="allergies" style="display: block; margin-bottom: 5px; font-weight: 600;">Important Medical Alert (Optional)</label>
                <input type="text" id="allergies" name="allergies" placeholder="e.g., Allergies, current medications, or urgent medical conditions" style="width: 100%;">
                <small style="color: #6b7280; font-size: 0.875rem;">Only critical information that staff should know immediately</small>
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="service_id" style="display: block; margin-bottom: 5px; font-weight: 600;">
                    Service Destination *
                    <span style="color: #dc2626; font-size: 0.875rem;">(Required)</span>
                </label>
                <select id="service_id" name="service_id" required style="width: 100%;" onchange="updateServiceInfo()">
                    <option value="">-- Select Service --</option>
                    @foreach($services as $service)
                        @php
                            $availability = $service->getAvailabilityStatus();
                            $isDisabled = !$service->isAvailableForNewPatients() && $availability['status'] === 'unavailable';
                        @endphp
                        <option value="{{ $service->id }}" 
                                {{ $isDisabled ? 'disabled' : '' }}
                                data-status="{{ $availability['status'] }}"
                                data-message="{{ $availability['message'] }}"
                                data-color="{{ $availability['color'] }}">
                            {{ $service->name }}
                            @if($availability['status'] === 'full')
                                (FULL - Emergency only)
                            @elseif($availability['status'] === 'unavailable')
                                (UNAVAILABLE)
                            @elseif($availability['status'] === 'not_started')
                                (Not yet open)
                            @elseif($availability['status'] === 'closed')
                                (Closed)
                            @endif
                        </option>
                    @endforeach
                </select>
                <div id="service-info" style="margin-top: 5px; font-size: 0.875rem; display: none;">
                    <span id="service-status" style="font-weight: 600;"></span>
                </div>
                <small style="color: #1f2937; font-size: 0.875rem; font-weight: 500;">
                    <i class="fas fa-info-circle" style="color: #059669;"></i> 
                    Select where to send this patient. If patient needs multiple services, you can transfer them later.
                </small>
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="priority" style="display: block; margin-bottom: 5px; font-weight: 600;">Patient Category *</label>
                <select id="priority" name="priority" required style="width: 100%;">
                    <option value="normal">Regular Patient</option>
                    <option value="senior">Senior Citizen (60+ years)</option>
                    <option value="pwd">Person with Disability</option>
                    <option value="pregnant">Pregnant</option>
                    <option value="emergency">Emergency Case</option>
                </select>
                <small style="color: #6b7280; font-size: 0.875rem;">Senior citizens, PWD, and pregnant women get priority service as per Republic Act</small>
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="notes" style="display: block; margin-bottom: 5px; font-weight: 600;">Notes</label>
                <textarea id="notes" name="notes" rows="3" style="width: 100%;"></textarea>
            </div>
            
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="hideAddPatientModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add to Queue
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Patient Modal -->
<div id="editPatientModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
    <div class="modal-content" style="background-color: #fefefe; margin: 5% auto; padding: 20px; border: none; border-radius: 8px; width: 90%; max-width: 500px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; color: #1f2937;"><i class="fas fa-edit"></i> Edit Queue Entry</h3>
            <button onclick="hideEditPatientModal()" style="background: none; border: none; font-size: 1.5rem; color: #6b7280; cursor: pointer;">&times;</button>
        </div>
        
        <form method="POST" id="editPatientForm">
            @csrf
            @method('PUT')
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="edit_patient_name" style="display: block; margin-bottom: 5px; font-weight: 600;">Patient Name *</label>
                <input type="text" id="edit_patient_name" name="patient_name" required style="width: 100%;">
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="edit_contact_number" style="display: block; margin-bottom: 5px; font-weight: 600;">Contact Number</label>
                <input type="text" id="edit_contact_number" name="contact_number" style="width: 100%;">
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="edit_service_id" style="display: block; margin-bottom: 5px; font-weight: 600;">Service Needed</label>
                <select id="edit_service_id" name="service_id" style="width: 100%;">
                    <option value="">Will be assigned later</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="edit_priority" style="display: block; margin-bottom: 5px; font-weight: 600;">Priority *</label>
                <select id="edit_priority" name="priority" required style="width: 100%;">
                    <option value="normal">Normal</option>
                    <option value="senior">Senior Citizen</option>
                    <option value="pwd">PWD</option>
                    <option value="pregnant">Pregnant</option>
                    <option value="emergency">Emergency</option>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="edit_notes" style="display: block; margin-bottom: 5px; font-weight: 600;">Notes</label>
                <textarea id="edit_notes" name="notes" rows="3" style="width: 100%;"></textarea>
            </div>
            
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="hideEditPatientModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Filter functions
function toggleFilters() {
    const form = document.getElementById('filterForm');
    const icon = document.getElementById('filterIcon');
    const text = document.getElementById('filterToggleText');
    const chevron = document.getElementById('filterChevron');
    
    if (form.style.display === 'none') {
        form.style.display = 'block';
        text.textContent = 'Hide Filters';
        chevron.classList.remove('fa-chevron-down');
        chevron.classList.add('fa-chevron-up');
        localStorage.setItem('queueFiltersOpen', 'true');
    } else {
        form.style.display = 'none';
        text.textContent = 'Show Filters';
        chevron.classList.remove('fa-chevron-up');
        chevron.classList.add('fa-chevron-down');
        localStorage.setItem('queueFiltersOpen', 'false');
    }
}

// Modal functions
function showAddPatientModal() {
    document.getElementById('addPatientModal').style.display = 'block';
}

function hideAddPatientModal() {
    document.getElementById('addPatientModal').style.display = 'none';
    document.getElementById('addPatientForm').reset();
}

function editQueue(id, name, contact, serviceId, priority, notes) {
    document.getElementById('editPatientForm').action = `/front-desk/${id}`;
    document.getElementById('edit_patient_name').value = name;
    document.getElementById('edit_contact_number').value = contact || '';
    document.getElementById('edit_service_id').value = serviceId || '';
    document.getElementById('edit_priority').value = priority;
    document.getElementById('edit_notes').value = notes || '';
    document.getElementById('editPatientModal').style.display = 'block';
}

function hideAddPatientModal() {
    document.getElementById('addPatientModal').style.display = 'none';
}

function updateServiceInfo() {
    const select = document.getElementById('service_id');
    const serviceInfo = document.getElementById('service-info');
    const serviceStatus = document.getElementById('service-status');
    
    if (select.value === '') {
        serviceInfo.style.display = 'none';
        return;
    }
    
    const selectedOption = select.options[select.selectedIndex];
    const status = selectedOption.getAttribute('data-status');
    const message = selectedOption.getAttribute('data-message');
    const color = selectedOption.getAttribute('data-color');
    
    if (status && status !== 'available') {
        serviceInfo.style.display = 'block';
        serviceStatus.textContent = message;
        serviceStatus.style.color = color === 'green' ? '#10b981' : 
                                   color === 'yellow' ? '#f59e0b' : '#ef4444';
        
        // Show warning for full services
        if (status === 'full') {
            serviceStatus.innerHTML = '⚠️ ' + message + ' (Emergency cases will still be accepted)';
        }
    } else {
        serviceInfo.style.display = 'none';
    }
}

// Close modals when clicking outside
window.onclick = function(event) {
    const addModal = document.getElementById('addPatientModal');
    const editModal = document.getElementById('editPatientModal');
    
    if (event.target === addModal) {
        hideAddPatientModal();
    }
    if (event.target === editModal) {
        hideEditPatientModal();
    }
}

// Print queue ticket function
function printTicket(queueId, queueNumber, patientName, serviceName, priority) {
    const printWindow = window.open('', '_blank');
    const currentTime = new Date().toLocaleString();
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Queue Ticket - ${queueNumber}</title>
            <style>
                @media print { @page { margin: 0; } }
                body { 
                    font-family: 'Courier New', monospace; 
                    margin: 0; 
                    padding: 20px; 
                    width: 300px;
                    background: white;
                }
                .ticket { 
                    border: 2px dashed #333; 
                    padding: 15px; 
                    text-align: center; 
                    background: white;
                }
                .header { 
                    font-size: 16px; 
                    font-weight: bold; 
                    margin-bottom: 10px; 
                    border-bottom: 1px solid #333;
                    padding-bottom: 5px;
                }
                .queue-number { 
                    font-size: 36px; 
                    font-weight: bold; 
                    margin: 15px 0; 
                    border: 3px solid #333;
                    padding: 10px;
                }
                .details { 
                    font-size: 12px; 
                    line-height: 1.6; 
                    text-align: left; 
                    margin: 10px 0;
                }
                .priority { 
                    font-size: 14px; 
                    font-weight: bold; 
                    margin: 10px 0; 
                    padding: 5px;
                    border: 1px solid #333;
                }
                .footer { 
                    font-size: 10px; 
                    margin-top: 15px; 
                    border-top: 1px solid #333;
                    padding-top: 10px;
                    color: #666;
                }
            </style>
        </head>
        <body>
            <div class="ticket">
                <div class="header">MABINI HEALTH CENTER</div>
                <div class="header" style="font-size: 14px; font-weight: normal;">Queue Management System</div>
                
                <div class="queue-number">${queueNumber}</div>
                
                <div class="details">
                    <strong>Patient:</strong> ${patientName}<br>
                    <strong>Service:</strong> ${serviceName}<br>
                    <strong>Category:</strong> ${priority.charAt(0).toUpperCase() + priority.slice(1)}<br>
                    <strong>Time:</strong> ${currentTime}
                </div>
                
                <div class="priority">
                    ${priority === 'emergency' ? '🚨 EMERGENCY CASE' : 
                      priority === 'senior' ? '👴 SENIOR CITIZEN' : 
                      priority === 'pwd' ? '♿ PWD PRIORITY' : 
                      priority === 'pregnant' ? '🤰 PREGNANT PRIORITY' : '✅ REGULAR PATIENT'}
                </div>
                
                <div style="font-size: 11px; margin: 10px 0; padding: 8px; background: #f0f0f0; border-radius: 3px;">
                    <strong>Queue Number Meaning:</strong><br>
                    ${queueNumber.includes('E-') ? 'E- = Emergency Priority' :
                      queueNumber.includes('S-') ? 'S- = Senior Citizen Priority' :
                      queueNumber.includes('P-') ? 'P- = PWD Priority' : 'Regular Queue'}<br>
                    ${queueNumber.includes('GP-') ? 'GP = General Practitioner' :
                      queueNumber.includes('DEN-') ? 'DEN = Dental Service' :
                      queueNumber.includes('LAB-') ? 'LAB = Laboratory Service' :
                      queueNumber.includes('PHR-') ? 'PHR = Pharmacy' :
                      queueNumber.includes('MCH-') ? 'MCH = Maternal & Child Health' : 'GEN = General Service'}
                </div>
                
                <div class="footer">
                    Please keep this ticket and wait for your number to be called.<br>
                    Thank you for your patience!<br><br>
                    For assistance, please approach the front desk.
                </div>
            </div>
        </body>
        </html>
    `);
    
    printWindow.document.close();
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 250);
}

// Auto-refresh page every 30 seconds to keep queue status updated
setInterval(function() {
    if (!document.getElementById('addPatientModal').style.display.includes('block') && 
        !document.getElementById('editPatientModal').style.display.includes('block')) {
        window.location.reload();
    }
}, 30000);

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Show filters if previously open
    if (localStorage.getItem('queueFiltersOpen') === 'true') {
        toggleFilters();
    }
    
    // Load service status
    loadServiceStatus();
    
    // Refresh service status every 30 seconds
    setInterval(loadServiceStatus, 30000);
});

// Load service status from API with dynamic capacity calculation
function loadServiceStatus() {
    fetch('/api/service-status')
        .then(response => response.json())
        .then(services => {
            const container = document.getElementById('serviceStatusCards');
            const lastUpdated = document.getElementById('lastUpdated');
            
            if (services.length === 0) {
                container.innerHTML = `
                    <div style="text-align: center; padding: 2rem; color: #9ca3af; grid-column: 1 / -1;">
                        <i class="fas fa-hospital-alt" style="font-size: 2rem; opacity: 0.5;"></i>
                        <p style="margin: 0.5rem 0 0;">No services available</p>
                    </div>
                `;
                return;
            }
            
            // Load dynamic capacity for each service
            Promise.all(services.map(service => 
                fetch(`/front-desk/service-capacity/${service.id}`)
                    .then(r => r.json())
                    .then(capacity => ({...service, capacity}))
                    .catch(() => ({...service, capacity: null}))
            )).then(servicesWithCapacity => {
                container.innerHTML = servicesWithCapacity.map(service => {
                    const capacity = service.capacity;
                    const isAvailable = service.is_active && service.available_today && capacity && capacity.available;
                    const queueCount = capacity?.current_waiting || 0;
                    const availableSlots = capacity?.available_slots || 0;
                    const avgServiceTime = capacity?.avg_service_time || 15;
                    const estimatedWaitMins = capacity?.estimated_wait_time || 0;
                    const estimatedWaitHrs = Math.ceil(estimatedWaitMins / 60);
                    const limit = service.daily_patient_limit || 50;
                    const percentage = (queueCount / limit) * 100;
                    
                    let statusColor, statusIcon, statusText, statusBg;
                    if (!isAvailable) {
                        statusColor = '#ef4444';
                        statusIcon = 'fa-times-circle';
                        statusText = capacity?.reason || service.unavailable_reason || 'Unavailable';
                        statusBg = '#fef2f2';
                    } else if (availableSlots === 0) {
                        statusColor = '#f59e0b';
                        statusIcon = 'fa-exclamation-triangle';
                        statusText = 'Full - No Slots';
                        statusBg = '#fffbeb';
                    } else if (availableSlots <= 5) {
                        statusColor = '#f59e0b';
                        statusIcon = 'fa-clock';
                        statusText = `${availableSlots} Slots Left`;
                        statusBg = '#fffbeb';
                    } else {
                        statusColor = '#10b981';
                        statusIcon = 'fa-check-circle';
                        statusText = `${availableSlots} Slots Available`;
                        statusBg = '#f0fdf4';
                    }
                    
                    return `
                        <div style="border: 2px solid ${statusColor}; border-radius: 8px; padding: 1rem; background: white;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
                                <div style="flex: 1;">
                                    <h4 style="margin: 0; font-size: 1rem; font-weight: 600; color: #111827;">${service.name}</h4>
                                    ${isAvailable ? `<small style="color: #6b7280; font-size: 0.75rem;">Avg: ${avgServiceTime} min/patient</small>` : ''}
                                </div>
                                <div style="padding: 0.25rem 0.75rem; border-radius: 999px; background: ${statusBg}; display: flex; align-items: center; gap: 0.35rem;">
                                    <i class="fas ${statusIcon}" style="color: ${statusColor}; font-size: 0.75rem;"></i>
                                    <span style="font-size: 0.75rem; font-weight: 600; color: ${statusColor};">${statusText}</span>
                                </div>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; margin-top: 0.75rem;">
                                <div style="background: #f9fafb; padding: 0.5rem; border-radius: 6px;">
                                    <div style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem;">Waiting Now</div>
                                    <div style="font-size: 1.25rem; font-weight: 700; color: #111827;">${queueCount}</div>
                                </div>
                                <div style="background: #f9fafb; padding: 0.5rem; border-radius: 6px;">
                                    <div style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem;">Est. Wait</div>
                                    <div style="font-size: 1.25rem; font-weight: 700; color: #111827;">${isAvailable ? (estimatedWaitMins < 60 ? estimatedWaitMins + ' min' : estimatedWaitHrs + ' hr') : '–'}</div>
                                </div>
                            </div>
                            
                            ${isAvailable && capacity ? `
                                <div style="margin-top: 0.75rem;">
                                    <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem;">
                                        <span>Capacity Until Cutoff (${capacity.cutoff_time})</span>
                                        <span>${queueCount + availableSlots}/${queueCount + availableSlots}</span>
                                    </div>
                                    <div style="background: #e5e7eb; border-radius: 999px; height: 6px; overflow: hidden;">
                                        <div style="background: ${availableSlots <= 5 ? '#f59e0b' : '#10b981'}; height: 100%; width: ${(queueCount / (queueCount + availableSlots)) * 100}%; transition: width 0.3s;"></div>
                                    </div>
                                    ${capacity.daily_limit_remaining !== null ? `
                                        <div style="font-size: 0.7rem; color: #9ca3af; margin-top: 0.35rem;">
                                            Daily limit: ${capacity.daily_limit_remaining} remaining
                                        </div>
                                    ` : ''}
                                </div>
                            ` : ''}
                        </div>
                    `;
                }).join('');
                
                // Update last refreshed time
                const now = new Date();
                lastUpdated.innerHTML = `<i class="fas fa-check-circle" style="color: #10b981;"></i> Updated ${now.toLocaleTimeString()}`;
            });
        })
        .catch(error => {
            console.error('Error loading service status:', error);
            document.getElementById('serviceStatusCards').innerHTML = `
                <div style="text-align: center; padding: 2rem; color: #ef4444; grid-column: 1 / -1;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 2rem;"></i>
                    <p style="margin: 0.5rem 0 0;">Failed to load services</p>
                </div>
            `;
        });
}
</script>
@endsection