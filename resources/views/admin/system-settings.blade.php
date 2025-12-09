@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">System Settings</h1>
                    <p class="text-muted mb-0">Configure system-wide settings and preferences</p>
                </div>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('settingsForm').submit()">
                    <i class="fas fa-save me-2"></i>Save All Changes
                </button>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('admin.system.settings.update') }}" method="POST" id="settingsForm">
        @csrf

        <!-- General Settings -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-cog me-2"></i>General Settings
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="health_center_name" class="form-label">Health Center Name</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="health_center_name" 
                            name="settings[health_center_name]"
                            value="{{ old('settings.health_center_name', \App\Models\SystemSetting::get('health_center_name', 'Mabini Health Center')) }}"
                        >
                        <small class="form-text text-muted">Displayed on reports and headers</small>
                    </div>
                    <div class="col-md-6">
                        <label for="health_center_address" class="form-label">Address</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="health_center_address" 
                            name="settings[health_center_address]"
                            value="{{ old('settings.health_center_address', \App\Models\SystemSetting::get('health_center_address', 'Mabini, Batangas')) }}"
                        >
                    </div>
                    <div class="col-md-6">
                        <label for="contact_number" class="form-label">Contact Number</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="contact_number" 
                            name="settings[contact_number]"
                            value="{{ old('settings.contact_number', \App\Models\SystemSetting::get('contact_number', '')) }}"
                            placeholder="+63 XXX XXX XXXX"
                        >
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email Address</label>
                        <input 
                            type="email" 
                            class="form-control" 
                            id="email" 
                            name="settings[email]"
                            value="{{ old('settings.email', \App\Models\SystemSetting::get('email', '')) }}"
                            placeholder="contact@example.com"
                        >
                    </div>
                </div>
            </div>
        </div>

        <!-- Operating Hours -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-clock me-2"></i>Operating Hours
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="opening_time" class="form-label">Opening Time</label>
                        <input 
                            type="time" 
                            class="form-control" 
                            id="opening_time" 
                            name="settings[opening_time]"
                            value="{{ old('settings.opening_time', \App\Models\SystemSetting::get('opening_time', '08:00')) }}"
                        >
                    </div>
                    <div class="col-md-6">
                        <label for="closing_time" class="form-label">Closing Time</label>
                        <input 
                            type="time" 
                            class="form-control" 
                            id="closing_time" 
                            name="settings[closing_time]"
                            value="{{ old('settings.closing_time', \App\Models\SystemSetting::get('closing_time', '17:00')) }}"
                        >
                    </div>
                    <div class="col-12">
                        <label class="form-label">Operating Days</label>
                        <div class="d-flex flex-wrap gap-3">
                            @php
                                $operatingDays = \App\Models\SystemSetting::get('operating_days', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
                                $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                            @endphp
                            @foreach($days as $day)
                            <div class="form-check">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    id="day_{{ $day }}" 
                                    name="settings[operating_days][]"
                                    value="{{ $day }}"
                                    {{ in_array($day, (array)$operatingDays) ? 'checked' : '' }}
                                >
                                <label class="form-check-label" for="day_{{ $day }}">
                                    {{ ucfirst($day) }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Queue Settings -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-users me-2"></i>Queue Management Settings
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="max_queue_per_day" class="form-label">Max Queue Per Day</label>
                        <input 
                            type="number" 
                            class="form-control" 
                            id="max_queue_per_day" 
                            name="settings[max_queue_per_day]"
                            value="{{ old('settings.max_queue_per_day', \App\Models\SystemSetting::get('max_queue_per_day', 100)) }}"
                            min="1"
                        >
                        <small class="form-text text-muted">Maximum patients per day (0 = unlimited)</small>
                    </div>
                    <div class="col-md-4">
                        <label for="avg_consultation_time" class="form-label">Avg. Consultation (minutes)</label>
                        <input 
                            type="number" 
                            class="form-control" 
                            id="avg_consultation_time" 
                            name="settings[avg_consultation_time]"
                            value="{{ old('settings.avg_consultation_time', \App\Models\SystemSetting::get('avg_consultation_time', 15)) }}"
                            min="1"
                        >
                        <small class="form-text text-muted">Used for wait time estimates</small>
                    </div>
                    <div class="col-md-4">
                        <label for="queue_auto_refresh" class="form-label">Auto Refresh (seconds)</label>
                        <input 
                            type="number" 
                            class="form-control" 
                            id="queue_auto_refresh" 
                            name="settings[queue_auto_refresh]"
                            value="{{ old('settings.queue_auto_refresh', \App\Models\SystemSetting::get('queue_auto_refresh', 30)) }}"
                            min="5"
                        >
                        <small class="form-text text-muted">Queue display refresh interval</small>
                    </div>
                </div>
                <hr class="my-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input type="hidden" name="settings[allow_walk_in]" value="0">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                id="allow_walk_in" 
                                name="settings[allow_walk_in]"
                                value="1"
                                {{ \App\Models\SystemSetting::get('allow_walk_in', true) ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="allow_walk_in">
                                Allow Walk-in Patients
                            </label>
                        </div>
                        <small class="form-text text-muted">Enable patients to queue without online registration</small>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input type="hidden" name="settings[allow_online_queue]" value="0">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                id="allow_online_queue" 
                                name="settings[allow_online_queue]"
                                value="1"
                                {{ \App\Models\SystemSetting::get('allow_online_queue', true) ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="allow_online_queue">
                                Allow Online Queue Registration
                            </label>
                        </div>
                        <small class="form-text text-muted">Enable patients to join queue online</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Queue Cutoff Settings -->
        <div class="card shadow-sm mb-4 border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">
                    <i class="fas fa-stopwatch me-2"></i>Queue Cutoff Settings
                    <span class="badge bg-warning text-dark ms-2">IMPORTANT</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>About Cutoff Time:</strong> When the health center reaches the cutoff time, all remaining patients in queue (Waiting/Pending/Skipped) will automatically be marked as "Unattended" and will receive a notification to return tomorrow.
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="queue_cutoff_time" class="form-label">
                            <i class="fas fa-hand-paper text-danger"></i> Queue Cutoff Time
                            <span class="badge bg-danger">Required</span>
                        </label>
                        <input 
                            type="time" 
                            class="form-control" 
                            id="queue_cutoff_time" 
                            name="settings[queue_cutoff_time]"
                            value="{{ old('settings.queue_cutoff_time', \App\Models\SystemSetting::get('queue_cutoff_time', '17:00')) }}"
                            required
                        >
                        <small class="form-text text-muted">
                            Time when remaining queues are automatically marked as "Unattended"
                        </small>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="cutoff_warning_time" class="form-label">
                            <i class="fas fa-bell text-warning"></i> Cutoff Warning Time
                        </label>
                        <input 
                            type="time" 
                            class="form-control" 
                            id="cutoff_warning_time" 
                            name="settings[cutoff_warning_time]"
                            value="{{ old('settings.cutoff_warning_time', \App\Models\SystemSetting::get('cutoff_warning_time', '16:00')) }}"
                        >
                        <small class="form-text text-muted">
                            Staff will see a warning when approaching cutoff time (typically 1 hour before)
                        </small>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input type="hidden" name="settings[enable_cutoff_notifications]" value="0">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                id="enable_cutoff_notifications" 
                                name="settings[enable_cutoff_notifications]"
                                value="1"
                                {{ \App\Models\SystemSetting::get('enable_cutoff_notifications', true) ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="enable_cutoff_notifications">
                                <i class="fas fa-envelope"></i> Send Cutoff Notifications
                            </label>
                        </div>
                        <small class="form-text text-muted">
                            Automatically notify patients via email when their queue is marked as unattended
                        </small>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input type="hidden" name="settings[enable_auto_cutoff]" value="0">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                id="enable_auto_cutoff" 
                                name="settings[enable_auto_cutoff]"
                                value="1"
                                {{ \App\Models\SystemSetting::get('enable_auto_cutoff', true) ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="enable_auto_cutoff">
                                <i class="fas fa-robot"></i> Enable Automatic Cutoff
                            </label>
                        </div>
                        <small class="form-text text-muted">
                            Automatically process cutoff at the specified time (requires Laravel scheduler to be running)
                        </small>
                    </div>
                </div>
                
                <div class="alert alert-warning mt-4 mb-0">
                    <h6 class="alert-heading"><i class="fas fa-terminal me-2"></i>Manual Cutoff</h6>
                    <p class="mb-2">Need to process cutoff immediately? Use this button to manually trigger cutoff processing.</p>
                    <form method="POST" action="{{ route('admin.process-cutoff') }}" 
                          onsubmit="return confirm('⚠️ This will immediately mark all waiting patients as UNATTENDED and send notifications. Are you sure?');"
                          style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-hand-paper me-2"></i>Process Cutoff Now
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Medicine Inventory Settings -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-pills me-2"></i>Medicine Inventory Settings
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="low_stock_threshold" class="form-label">Low Stock Threshold</label>
                        <input 
                            type="number" 
                            class="form-control" 
                            id="low_stock_threshold" 
                            name="settings[low_stock_threshold]"
                            value="{{ old('settings.low_stock_threshold', \App\Models\SystemSetting::get('low_stock_threshold', 10)) }}"
                            min="1"
                        >
                        <small class="form-text text-muted">Alert when stock falls below this level</small>
                    </div>
                    <div class="col-md-4">
                        <label for="expiry_alert_days" class="form-label">Expiry Alert (days)</label>
                        <input 
                            type="number" 
                            class="form-control" 
                            id="expiry_alert_days" 
                            name="settings[expiry_alert_days]"
                            value="{{ old('settings.expiry_alert_days', \App\Models\SystemSetting::get('expiry_alert_days', 90)) }}"
                            min="1"
                        >
                        <small class="form-text text-muted">Alert when medicine expires within days</small>
                    </div>
                    <div class="col-md-4">
                        <label for="default_reorder_level" class="form-label">Default Reorder Level</label>
                        <input 
                            type="number" 
                            class="form-control" 
                            id="default_reorder_level" 
                            name="settings[default_reorder_level]"
                            value="{{ old('settings.default_reorder_level', \App\Models\SystemSetting::get('default_reorder_level', 15)) }}"
                            min="1"
                        >
                        <small class="form-text text-muted">Default reorder level for new medicines</small>
                    </div>
                </div>
                <hr class="my-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input type="hidden" name="settings[enable_batch_tracking]" value="0">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                id="enable_batch_tracking" 
                                name="settings[enable_batch_tracking]"
                                value="1"
                                {{ \App\Models\SystemSetting::get('enable_batch_tracking', true) ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="enable_batch_tracking">
                                Enable Batch/Lot Tracking
                            </label>
                        </div>
                        <small class="form-text text-muted">Track individual batches with different expiry dates</small>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input type="hidden" name="settings[require_batch_number]" value="0">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                id="require_batch_number" 
                                name="settings[require_batch_number]"
                                value="1"
                                {{ \App\Models\SystemSetting::get('require_batch_number', false) ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="require_batch_number">
                                Require Batch Number on Restock
                            </label>
                        </div>
                        <small class="form-text text-muted">Make batch number mandatory when restocking</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Settings -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">
                    <i class="fas fa-shield-alt me-2"></i>Security Settings
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="session_lifetime" class="form-label">Session Lifetime (minutes)</label>
                        <input 
                            type="number" 
                            class="form-control" 
                            id="session_lifetime" 
                            name="settings[session_lifetime]"
                            value="{{ old('settings.session_lifetime', \App\Models\SystemSetting::get('session_lifetime', 120)) }}"
                            min="15"
                        >
                        <small class="form-text text-muted">Auto logout after inactivity</small>
                    </div>
                    <div class="col-md-4">
                        <label for="max_login_attempts" class="form-label">Max Login Attempts</label>
                        <input 
                            type="number" 
                            class="form-control" 
                            id="max_login_attempts" 
                            name="settings[max_login_attempts]"
                            value="{{ old('settings.max_login_attempts', \App\Models\SystemSetting::get('max_login_attempts', 5)) }}"
                            min="3"
                        >
                        <small class="form-text text-muted">Lock account after failed attempts</small>
                    </div>
                    <div class="col-md-4">
                        <label for="lockout_duration" class="form-label">Lockout Duration (minutes)</label>
                        <input 
                            type="number" 
                            class="form-control" 
                            id="lockout_duration" 
                            name="settings[lockout_duration]"
                            value="{{ old('settings.lockout_duration', \App\Models\SystemSetting::get('lockout_duration', 5)) }}"
                            min="1"
                        >
                        <small class="form-text text-muted">How long to lock account</small>
                    </div>
                </div>
                <hr class="my-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input type="hidden" name="settings[enable_activity_logging]" value="0">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                id="enable_activity_logging" 
                                name="settings[enable_activity_logging]"
                                value="1"
                                {{ \App\Models\SystemSetting::get('enable_activity_logging', true) ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="enable_activity_logging">
                                Enable Activity Logging
                            </label>
                        </div>
                        <small class="form-text text-muted">Log all user activities for audit trail</small>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input type="hidden" name="settings[require_strong_password]" value="0">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                id="require_strong_password" 
                                name="settings[require_strong_password]"
                                value="1"
                                {{ \App\Models\SystemSetting::get('require_strong_password', true) ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="require_strong_password">
                                Require Strong Passwords
                            </label>
                        </div>
                        <small class="form-text text-muted">Enforce password complexity requirements</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Maintenance -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-tools me-2"></i>System Maintenance
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="form-check form-switch">
                            <input type="hidden" name="settings[maintenance_mode]" value="0">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                id="maintenance_mode" 
                                name="settings[maintenance_mode]"
                                value="1"
                                {{ \App\Models\SystemSetting::get('maintenance_mode', false) ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="maintenance_mode">
                                <strong>Enable Maintenance Mode</strong>
                            </label>
                        </div>
                        <small class="form-text text-muted">
                            <i class="fas fa-exclamation-triangle text-warning me-1"></i>
                            This will prevent all users (except admins) from accessing the system
                        </small>
                    </div>
                    <div class="col-md-12">
                        <label for="maintenance_message" class="form-label">Maintenance Message</label>
                        <textarea 
                            class="form-control" 
                            id="maintenance_message" 
                            name="settings[maintenance_message]"
                            rows="3"
                            placeholder="We are currently performing system maintenance. Please check back later."
                        >{{ old('settings.maintenance_message', \App\Models\SystemSetting::get('maintenance_message', 'System is under maintenance. Please check back later.')) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save me-2"></i>Save All Settings
            </button>
        </div>
    </form>
</div>

<style>
.card {
    border: none;
    border-radius: 8px;
}

.card-header {
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    padding: 1rem 1.25rem;
    border-radius: 8px 8px 0 0 !important;
}

.form-label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border-radius: 6px;
    border: 1px solid #dee2e6;
    padding: 0.625rem 0.875rem;
}

.form-control:focus, .form-select:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.form-check-input {
    width: 1.2rem;
    height: 1.2rem;
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.btn {
    padding: 0.5rem 1.25rem;
    border-radius: 6px;
    font-weight: 500;
}

hr {
    opacity: 0.1;
}
</style>

<script>
// Confirmation before enabling maintenance mode
document.getElementById('maintenance_mode').addEventListener('change', function() {
    if (this.checked) {
        if (!confirm('Are you sure you want to enable maintenance mode? This will prevent users from accessing the system.')) {
            this.checked = false;
        }
    }
});

// Form submission confirmation
document.getElementById('settingsForm').addEventListener('submit', function(e) {
    if (!confirm('Are you sure you want to save these settings? Some changes may require users to log in again.')) {
        e.preventDefault();
    }
});
</script>
@endsection
