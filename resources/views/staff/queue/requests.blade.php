@extends('layouts.app')

@section('title', 'Pending Queue Requests')
@section('page-title', 'Pending Queue Requests')

@section('content')
<div class="page-header">
    <div class="header-actions">
        <div style="display: flex; gap: 1rem; align-items: center;">
            <select id="dateFilter" class="form-control" style="max-width: 200px;" onchange="filterByDate()">
                <option value="all">All Dates</option>
                <option value="today" {{ request('date') == 'today' ? 'selected' : '' }}>Today</option>
                <option value="tomorrow" {{ request('date') == 'tomorrow' ? 'selected' : '' }}>Tomorrow</option>
                <option value="this-week" {{ request('date') == 'this-week' ? 'selected' : '' }}>This Week</option>
            </select>
            <a href="{{ route('queue.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Queue
            </a>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success" style="background: #d1fae5; border-left: 4px solid #059669; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
        <i class="fas fa-check-circle" style="color: #059669;"></i>
        <strong style="color: #065f46;">{{ session('success') }}</strong>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger" style="background: #fee2e2; border-left: 4px solid #dc2626; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
        <i class="fas fa-exclamation-circle" style="color: #dc2626;"></i>
        <strong style="color: #991b1b;">{{ session('error') }}</strong>
    </div>
@endif

<!-- Statistics Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    <div class="stat-card" style="background: #fef3c7; border-left: 4px solid #f59e0b;">
        <div class="stat-icon" style="background: #f59e0b;"><i class="fas fa-clock"></i></div>
        <div class="stat-info">
            <h3>{{ $requests->where('approval_status', 'pending')->count() }}</h3>
            <p>Pending Review</p>
        </div>
    </div>
    <div class="stat-card" style="background: #d1fae5; border-left: 4px solid #059669;">
        <div class="stat-icon" style="background: #059669;"><i class="fas fa-check"></i></div>
        <div class="stat-info">
            <h3>{{ $requests->where('approval_status', 'approved')->count() }}</h3>
            <p>Approved</p>
        </div>
    </div>
    <div class="stat-card" style="background: #fee2e2; border-left: 4px solid #dc2626;">
        <div class="stat-icon" style="background: #dc2626;"><i class="fas fa-times"></i></div>
        <div class="stat-info">
            <h3>{{ $requests->where('approval_status', 'rejected')->count() }}</h3>
            <p>Rejected</p>
        </div>
    </div>
    <div class="stat-card" style="background: #e5e7eb; border-left: 4px solid #6b7280;">
        <div class="stat-icon" style="background: #6b7280;"><i class="fas fa-hourglass-end"></i></div>
        <div class="stat-info">
            <h3>{{ $requests->where('approval_status', 'expired')->count() }}</h3>
            <p>Expired</p>
        </div>
    </div>
</div>

@if($requests->isEmpty())
    <div class="dashboard-section" style="text-align: center; padding: 3rem;">
        <i class="fas fa-inbox" style="font-size: 4rem; color: #d1d5db; margin-bottom: 1rem;"></i>
        <h3 style="color: #6b7280;">No Pending Queue Requests</h3>
        <p style="color: #9ca3af;">All requests have been processed or there are no new submissions.</p>
    </div>
@else
    <div class="dashboard-section">
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            @foreach($requests as $request)
                <div class="request-card" style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; border-left: 5px solid {{ $request->approval_status == 'pending' ? '#f59e0b' : ($request->approval_status == 'approved' ? '#059669' : ($request->approval_status == 'rejected' ? '#dc2626' : '#6b7280')) }};">
                    <!-- Request Header -->
                    <div style="background: #f9fafb; padding: 1.25rem; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="background: {{ $request->approval_status == 'pending' ? '#fef3c7' : ($request->approval_status == 'approved' ? '#d1fae5' : ($request->approval_status == 'rejected' ? '#fee2e2' : '#f3f4f6')) }}; color: {{ $request->approval_status == 'pending' ? '#92400e' : ($request->approval_status == 'approved' ? '#065f46' : ($request->approval_status == 'rejected' ? '#991b1b' : '#6b7280')) }}; padding: 0.75rem 1.25rem; border-radius: 8px; font-weight: 700; font-size: 1.25rem;">
                                #{{ $request->queue_number }}
                            </div>
                            <div>
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                                    <h3 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: #1f2937;">
                                        {{ $request->patient ? $request->patient->full_name : 'Unknown Patient' }}
                                    </h3>
                                    @if(in_array($request->patient_category, ['PWD', 'Senior', 'Pregnant']))
                                        <span style="background: {{ $request->patient_category == 'PWD' ? '#dbeafe' : ($request->patient_category == 'Senior' ? '#fef3c7' : '#fce7f3') }}; color: {{ $request->patient_category == 'PWD' ? '#1e40af' : ($request->patient_category == 'Senior' ? '#92400e' : '#9f1239') }}; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
                                            @if($request->patient_category == 'PWD') 👨‍🦽 PWD
                                            @elseif($request->patient_category == 'Senior') 👴 Senior
                                            @elseif($request->patient_category == 'Pregnant') 🤰 Pregnant
                                            @endif
                                        </span>
                                    @endif
                                </div>
                                <p style="margin: 0; color: #6b7280; font-size: 0.875rem;">
                                    <i class="fas fa-id-card"></i> {{ $request->patient ? $request->patient->patient_id : 'N/A' }}
                                    <span style="margin: 0 0.5rem;">•</span>
                                    <i class="fas fa-calendar"></i> {{ $request->requested_date ? $request->requested_date->format('F d, Y') : 'Today' }}
                                    @if($request->requested_date && $request->requested_date->isToday())
                                        <span style="background: #059669; color: white; padding: 0.125rem 0.5rem; border-radius: 4px; font-size: 0.75rem; margin-left: 0.5rem;">TODAY</span>
                                    @elseif($request->requested_date && $request->requested_date->isTomorrow())
                                        <span style="background: #f59e0b; color: white; padding: 0.125rem 0.5rem; border-radius: 4px; font-size: 0.75rem; margin-left: 0.5rem;">TOMORROW</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <span style="background: {{ $request->approval_status == 'pending' ? '#fef3c7' : ($request->approval_status == 'approved' ? '#d1fae5' : ($request->approval_status == 'rejected' ? '#fee2e2' : '#f3f4f6')) }}; color: {{ $request->approval_status == 'pending' ? '#92400e' : ($request->approval_status == 'approved' ? '#065f46' : ($request->approval_status == 'rejected' ? '#991b1b' : '#6b7280')) }}; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.875rem;">
                                {{ ucfirst($request->approval_status) }}
                            </span>
                            <span style="color: #9ca3af; font-size: 0.875rem;">
                                <i class="fas fa-clock"></i> {{ $request->requested_at ? $request->requested_at->diffForHumans() : $request->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>

                    <!-- Request Body -->
                    <div style="padding: 1.5rem;">
                        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
                            <!-- Left Column - Patient Details -->
                            <div>
                                <h4 style="color: #059669; font-size: 1rem; font-weight: 600; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                                    <i class="fas fa-user"></i> Patient Information
                                </h4>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.5rem;">
                                    <div>
                                        <small style="color: #6b7280; font-weight: 600; display: block;">Age</small>
                                        <span style="color: #1f2937;">{{ $request->patient ? $request->patient->age : 'N/A' }} years</span>
                                    </div>
                                    <div>
                                        <small style="color: #6b7280; font-weight: 600; display: block;">Gender</small>
                                        <span style="color: #1f2937;">{{ $request->patient ? ucfirst($request->patient->gender) : 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <small style="color: #6b7280; font-weight: 600; display: block;">Contact</small>
                                        <span style="color: #1f2937;"><i class="fas fa-phone"></i> {{ $request->patient ? $request->patient->contact : 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <small style="color: #6b7280; font-weight: 600; display: block;">Address</small>
                                        <span style="color: #1f2937; font-size: 0.875rem;">{{ $request->patient ? Str::limit($request->patient->address, 50) : 'N/A' }}</span>
                                    </div>
                                </div>

                                @if($request->pwd_id || $request->senior_id)
                                    <div style="background: #eff6ff; padding: 1rem; border-radius: 8px; border-left: 3px solid #3b82f6; margin-bottom: 1.5rem;">
                                        <h5 style="color: #1e40af; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">
                                            <i class="fas fa-id-card"></i> ID Verification
                                        </h5>
                                        @if($request->pwd_id)
                                            <p style="margin: 0; color: #1f2937; font-size: 0.875rem;">
                                                <strong>PWD ID:</strong> {{ $request->pwd_id }}
                                            </p>
                                        @endif
                                        @if($request->senior_id)
                                            <p style="margin: 0; color: #1f2937; font-size: 0.875rem;">
                                                <strong>Senior Citizen ID:</strong> {{ $request->senior_id }}
                                            </p>
                                        @endif
                                    </div>
                                @endif

                                <h4 style="color: #059669; font-size: 1rem; font-weight: 600; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                                    <i class="fas fa-stethoscope"></i> Service Requested
                                </h4>
                                <div style="background: #f9fafb; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                                    <p style="margin: 0; color: #1f2937; font-weight: 600;">{{ $request->service_type }}</p>
                                    @if($request->notes)
                                        <p style="margin: 0.5rem 0 0 0; color: #6b7280; font-size: 0.875rem;">
                                            <strong>Notes:</strong> {{ $request->notes }}
                                        </p>
                                    @endif
                                </div>

                                @php
                                    $recentVisits = collect();
                                    if ($request->patient_id) {
                                        $recentVisits = App\Models\Queue::where('patient_id', $request->patient_id)
                                            ->where('status', 'Completed')
                                            ->orderBy('completed_at', 'desc')
                                            ->limit(3)
                                            ->get();
                                    }
                                @endphp
                                @if($recentVisits->count() > 0)
                                    <h4 style="color: #059669; font-size: 1rem; font-weight: 600; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                                        <i class="fas fa-history"></i> Recent Visit History
                                    </h4>
                                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                        @foreach($recentVisits as $visit)
                                            <div style="background: #f9fafb; padding: 0.75rem; border-radius: 6px; font-size: 0.875rem;">
                                                <div style="display: flex; justify-content: space-between;">
                                                    <span style="color: #1f2937; font-weight: 600;">{{ $visit->service_type }}</span>
                                                    <span style="color: #6b7280;">{{ $visit->completed_at->format('M d, Y') }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div style="background: #f9fafb; padding: 1rem; border-radius: 8px; text-align: center;">
                                        <small style="color: #9ca3af;">No previous visits recorded</small>
                                    </div>
                                @endif
                            </div>

                            <!-- Right Column - Actions -->
                            <div>
                                @if($request->approval_status == 'pending')
                                    <div style="background: #f9fafb; padding: 1.5rem; border-radius: 8px;">
                                        <h4 style="color: #1f2937; font-size: 1rem; font-weight: 600; margin-bottom: 1rem;">
                                            Review Actions
                                        </h4>
                                        
                                        <!-- Approve Form -->
                                        <form method="POST" action="{{ route('staff.queue.approve', $request->id) }}" style="margin-bottom: 1rem;">
                                            @csrf
                                            <div style="margin-bottom: 0.75rem;">
                                                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                                                    Staff Notes (Optional)
                                                </label>
                                                <textarea name="staff_notes" rows="3" class="form-control" placeholder="Add any notes about this approval..." style="font-size: 0.875rem;"></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-success" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.75rem;" onclick="return confirm('Approve this queue request for {{ $request->requested_date->format('F d, Y') }}?')">
                                                <i class="fas fa-check-circle"></i> Approve Request
                                            </button>
                                        </form>

                                        <!-- Reject Form -->
                                        <form method="POST" action="{{ route('staff.queue.reject', $request->id) }}" onsubmit="return validateRejectForm(this)">
                                            @csrf
                                            <div style="margin-bottom: 0.75rem;">
                                                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                                                    Rejection Reason *
                                                </label>
                                                <select name="rejection_reason" class="form-control" required style="margin-bottom: 0.5rem; font-size: 0.875rem;">
                                                    <option value="">Select reason...</option>
                                                    <option value="Service not available on selected date">Service not available on selected date</option>
                                                    <option value="Fully booked - please select another date">Fully booked - please select another date</option>
                                                    <option value="Missing or invalid ID verification">Missing or invalid ID verification</option>
                                                    <option value="Duplicate request">Duplicate request</option>
                                                    <option value="Service requires different department">Service requires different department</option>
                                                    <option value="other">Other (specify below)</option>
                                                </select>
                                                <textarea name="staff_notes" rows="2" class="form-control" placeholder="Additional details..." style="font-size: 0.875rem;"></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-danger" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.75rem;" onclick="return confirm('Reject this queue request?')">
                                                <i class="fas fa-times-circle"></i> Reject Request
                                            </button>
                                        </form>
                                    </div>
                                @elseif($request->approval_status == 'approved')
                                    <div style="background: #d1fae5; padding: 1.5rem; border-radius: 8px; border: 2px solid #059669;">
                                        <div style="text-align: center; color: #065f46;">
                                            <i class="fas fa-check-circle" style="font-size: 3rem; margin-bottom: 0.5rem;"></i>
                                            <h4 style="margin: 0; font-weight: 600;">Approved</h4>
                                            <p style="margin: 0.5rem 0 0 0; font-size: 0.875rem;">
                                                by {{ $request->reviewer->name ?? 'Staff' }}<br>
                                                {{ $request->reviewed_at->diffForHumans() }}
                                            </p>
                                            @if($request->staff_notes)
                                                <div style="background: white; padding: 0.75rem; border-radius: 6px; margin-top: 1rem; text-align: left;">
                                                    <small style="font-weight: 600; display: block; margin-bottom: 0.25rem;">Staff Notes:</small>
                                                    <small style="color: #6b7280;">{{ $request->staff_notes }}</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @elseif($request->approval_status == 'rejected')
                                    <div style="background: #fee2e2; padding: 1.5rem; border-radius: 8px; border: 2px solid #dc2626;">
                                        <div style="text-align: center; color: #991b1b;">
                                            <i class="fas fa-times-circle" style="font-size: 3rem; margin-bottom: 0.5rem;"></i>
                                            <h4 style="margin: 0; font-weight: 600;">Rejected</h4>
                                            <p style="margin: 0.5rem 0 0 0; font-size: 0.875rem;">
                                                by {{ $request->reviewer->name ?? 'Staff' }}<br>
                                                {{ $request->reviewed_at->diffForHumans() }}
                                            </p>
                                            <div style="background: white; padding: 0.75rem; border-radius: 6px; margin-top: 1rem; text-align: left;">
                                                <small style="font-weight: 600; display: block; margin-bottom: 0.25rem;">Reason:</small>
                                                <small style="color: #6b7280;">{{ $request->rejection_reason ?? 'No reason provided' }}</small>
                                                @if($request->staff_notes)
                                                    <small style="display: block; margin-top: 0.5rem; color: #6b7280;">{{ $request->staff_notes }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<script>
function filterByDate() {
    const filter = document.getElementById('dateFilter').value;
    const url = new URL(window.location.href);
    if (filter === 'all') {
        url.searchParams.delete('date');
    } else {
        url.searchParams.set('date', filter);
    }
    window.location.href = url.toString();
}

function validateRejectForm(form) {
    const reason = form.querySelector('select[name="rejection_reason"]').value;
    if (!reason) {
        alert('Please select a rejection reason');
        return false;
    }
    return confirm('Are you sure you want to reject this request?');
}
</script>

<style>
.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.stat-info h3 {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    color: #1f2937;
}

.stat-info p {
    margin: 0;
    color: #6b7280;
    font-size: 0.875rem;
    font-weight: 500;
}

.form-control {
    padding: 0.5rem 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.875rem;
    width: 100%;
}

.form-control:focus {
    outline: none;
    border-color: #059669;
    box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
}

.btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-success {
    background: #059669;
    color: white;
}

.btn-success:hover {
    background: #047857;
}

.btn-danger {
    background: #dc2626;
    color: white;
}

.btn-danger:hover {
    background: #b91c1c;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
}
</style>
@endsection
