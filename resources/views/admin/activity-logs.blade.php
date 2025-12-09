@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Activity Logs</h1>
                    <p class="text-muted mb-0">System audit trail and user activity history</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Print Logs
                    </button>
                    <button class="btn btn-primary" onclick="refreshLogs()">
                        <i class="fas fa-sync-alt me-2"></i>Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2"></i>Filters
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.activity.logs') }}" method="GET" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="action" class="form-label">Action Type</label>
                        <select class="form-select" id="action" name="action">
                            <option value="">All Actions</option>
                            <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Create</option>
                            <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Update</option>
                            <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Delete</option>
                            <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login</option>
                            <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Logout</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="user_type" class="form-label">User Type</label>
                        <select class="form-select" id="user_type" name="user_type">
                            <option value="">All Users</option>
                            <option value="admin" {{ request('user_type') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="manager" {{ request('user_type') == 'manager' ? 'selected' : '' }}>Manager</option>
                            <option value="staff" {{ request('user_type') == 'staff' ? 'selected' : '' }}>Service Staff</option>
                            <option value="front_desk" {{ request('user_type') == 'front_desk' ? 'selected' : '' }}>Front Desk</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label for="search" class="form-label">Search Description</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Search in activity description...">
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-2"></i>Apply Filters
                        </button>
                        <a href="{{ route('admin.activity.logs') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-white-50">Total Activities</h6>
                            <h2 class="mb-0 mt-2">{{ number_format($logs->total()) }}</h2>
                        </div>
                        <div>
                            <i class="fas fa-list-ul fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-white-50">Today's Activities</h6>
                            <h2 class="mb-0 mt-2">{{ number_format($logs->where('created_at', '>=', today())->count()) }}</h2>
                        </div>
                        <div>
                            <i class="fas fa-calendar-day fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-white-50">This Week</h6>
                            <h2 class="mb-0 mt-2">{{ number_format($logs->where('created_at', '>=', now()->startOfWeek())->count()) }}</h2>
                        </div>
                        <div>
                            <i class="fas fa-calendar-week fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-white-50">This Month</h6>
                            <h2 class="mb-0 mt-2">{{ number_format($logs->where('created_at', '>=', now()->startOfMonth())->count()) }}</h2>
                        </div>
                        <div>
                            <i class="fas fa-calendar-alt fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Logs Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Activity History</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 140px;">Date & Time</th>
                            <th style="width: 100px;">Action</th>
                            <th>Description</th>
                            <th style="width: 150px;">User</th>
                            <th style="width: 100px;">Type</th>
                            <th style="width: 120px;">IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="text-muted">{{ $log->id }}</td>
                            <td>
                                <small>
                                    {{ $log->created_at->format('M d, Y') }}<br>
                                    <span class="text-muted">{{ $log->created_at->format('h:i A') }}</span>
                                </small>
                            </td>
                            <td>
                                @php
                                    $actionColors = [
                                        'create' => 'success',
                                        'update' => 'info',
                                        'delete' => 'danger',
                                        'login' => 'primary',
                                        'logout' => 'secondary',
                                        'restock' => 'warning',
                                        'dispense' => 'purple'
                                    ];
                                    $color = $actionColors[$log->action] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }}">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td>
                                <div class="activity-description">
                                    {{ $log->description }}
                                    @if($log->model_type && $log->model_id)
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-link me-1"></i>
                                            {{ class_basename($log->model_type) }} #{{ $log->model_id }}
                                        </small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if(in_array($log->user_type, ['staff', 'admin', 'manager', 'front_desk']))
                                    @php
                                        $user = \App\Models\User::find($log->user_id);
                                    @endphp
                                    @if($user)
                                        <div>
                                            <strong>{{ $user->name }}</strong><br>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted">User #{{ $log->user_id }}</span>
                                    @endif
                                @elseif($log->user_type == 'patient')
                                    @php
                                        $patient = \App\Models\PatientAccount::find($log->user_id);
                                    @endphp
                                    @if($patient)
                                        <div>
                                            <strong>{{ $patient->full_name }}</strong><br>
                                            <small class="text-muted">{{ $patient->email }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted">Patient #{{ $log->user_id }}</span>
                                    @endif
                                @else
                                    <span class="text-muted">System</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $typeColors = [
                                        'admin' => 'danger',
                                        'manager' => 'info',
                                        'staff' => 'primary',
                                        'front_desk' => 'success',
                                        'patient' => 'info'
                                    ];
                                    $typeColor = $typeColors[$log->user_type] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $typeColor }}">
                                    {{ ucfirst(str_replace('_', ' ', $log->user_type ?? 'System')) }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted font-monospace">
                                    {{ $log->ip_address ?? 'N/A' }}
                                </small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">No activity logs found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">
                        Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ number_format($logs->total()) }} entries
                    </small>
                </div>
                <div>
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 8px;
}

.card-header {
    border-bottom: 1px solid #e9ecef;
    padding: 1rem 1.25rem;
    border-radius: 8px 8px 0 0 !important;
}

.table {
    font-size: 0.9rem;
}

.table thead th {
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
    padding: 0.75rem;
}

.table tbody td {
    padding: 0.75rem;
    vertical-align: middle;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}

.badge {
    font-weight: 500;
    padding: 0.35rem 0.65rem;
}

.bg-purple {
    background-color: #6f42c1 !important;
}

.activity-description {
    line-height: 1.4;
}

.form-control, .form-select {
    border-radius: 6px;
    border: 1px solid #dee2e6;
    padding: 0.5rem 0.75rem;
    font-size: 0.9rem;
}

.btn {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-weight: 500;
}

@media print {
    .card-header,
    .btn,
    .pagination,
    .card-footer {
        display: none !important;
    }
    
    .table {
        font-size: 10pt;
    }
}
</style>

<script>
function refreshLogs() {
    location.reload();
}

// Set max date for date inputs to today
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('date_from').setAttribute('max', today);
    document.getElementById('date_to').setAttribute('max', today);
});
</script>
@endsection
