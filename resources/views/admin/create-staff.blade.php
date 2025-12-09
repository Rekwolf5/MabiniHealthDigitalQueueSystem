@extends('layouts.app')

@section('title', 'Create Staff Account - Mabini Health Center')
@section('page-title', 'Create Staff Account')

@section('content')
<div class="container">
    <div class="card" style="max-width: 800px; margin: 0 auto;">
        <div class="card-header">
            <h3><i class="fas fa-user-plus"></i> Create New Staff Account</h3>
            <p style="margin: 0.5rem 0 0 0; color: #6b7280; font-size: 0.875rem;">
                Only administrators can create staff accounts
            </p>
        </div>
        
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong><i class="fas fa-exclamation-triangle"></i> Validation Errors:</strong>
                    <ul style="margin: 0.5rem 0 0 0;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.staff.store') }}">
                @csrf
                
                <div class="form-section">
                    <h4><i class="fas fa-user"></i> Personal Information</h4>
                    
                    <div class="form-group">
                        <label for="name">Full Name <span style="color: red;">*</span></label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            class="form-control" 
                            value="{{ old('name') }}" 
                            required
                            placeholder="e.g., Juan Dela Cruz"
                        >
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address <span style="color: red;">*</span></label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-control" 
                            value="{{ old('email') }}" 
                            required
                            placeholder="e.g., juan.delacruz@mabini.com"
                        >
                        <small class="form-text text-muted">
                            This will be used for login
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input 
                            type="text" 
                            id="phone" 
                            name="phone" 
                            class="form-control" 
                            value="{{ old('phone') }}"
                            placeholder="e.g., 09123456789"
                        >
                    </div>
                </div>

                <div class="form-section">
                    <h4><i class="fas fa-shield-alt"></i> Account Settings</h4>
                    
                    <div class="form-group">
                        <label for="role">Role <span style="color: red;">*</span></label>
                        <select id="role" name="role" class="form-control" required onchange="toggleServiceField()">
                            <option value="">Select Role</option>
                            <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Service Staff</option>
                            <option value="front_desk" {{ old('role') == 'front_desk' ? 'selected' : '' }}>Front Desk</option>
                            <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                        </select>
                        <small class="form-text text-muted">
                            <strong>Service Staff:</strong> Assigned to specific service (e.g., Pharmacy, Laboratory)<br>
                            <strong>Front Desk:</strong> Manages patient registration and queue<br>
                            <strong>Manager:</strong> Full access except user management<br>
                            <strong>Admin:</strong> Full system access including user management
                        </small>
                    </div>

                    <div class="form-group" id="serviceField" style="display: none;">
                        <label for="service_id">Assigned Service <span style="color: red;">*</span></label>
                        <select id="service_id" name="service_id" class="form-control">
                            <option value="">Select Service</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                    {{ $service->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> Assign this staff member to a specific service (multiple staff can work on the same service)
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="password">Password <span style="color: red;">*</span></label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-control" 
                            required
                            placeholder="Enter secure password"
                        >
                        <small class="form-text text-muted">
                            <strong>Password must contain:</strong><br>
                            • At least 8 characters<br>
                            • One uppercase letter (A-Z)<br>
                            • One lowercase letter (a-z)<br>
                            • One number (0-9)<br>
                            • One special character (!@#$%^&*)
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password <span style="color: red;">*</span></label>
                        <input 
                            type="password" 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            class="form-control" 
                            required
                            placeholder="Re-enter password"
                        >
                    </div>
                </div>

                <div class="form-actions" style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                    <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Create Staff Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.card-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.card-header h3 {
    margin: 0;
    color: #1f2937;
    font-size: 1.5rem;
}

.card-body {
    padding: 2rem;
}

.form-section {
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #e5e7eb;
}

.form-section:last-of-type {
    border-bottom: none;
}

.form-section h4 {
    color: #374151;
    margin-bottom: 1.5rem;
    font-size: 1.125rem;
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

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 1rem;
}

.form-control:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.form-text {
    display: block;
    margin-top: 0.5rem;
    font-size: 0.875rem;
}

.text-muted {
    color: #6b7280;
}

.alert {
    padding: 1rem;
    border-radius: 6px;
    margin-bottom: 1.5rem;
}

.alert-danger {
    background-color: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
}

.alert ul {
    margin: 0;
    padding-left: 1.5rem;
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 6px;
    font-size: 1rem;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
}

.btn-primary {
    background-color: #10b981;
    color: white;
}

.btn-primary:hover {
    background-color: #059669;
}

.btn-secondary {
    background-color: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background-color: #4b5563;
}
</style>

<script>
// Function to show/hide service field based on role
function toggleServiceField() {
    const role = document.getElementById('role').value;
    const serviceField = document.getElementById('serviceField');
    const serviceSelect = document.getElementById('service_id');
    
    if (role === 'staff') {
        serviceField.style.display = 'block';
        serviceSelect.required = true;
    } else {
        serviceField.style.display = 'none';
        serviceSelect.required = false;
        serviceSelect.value = '';
    }
}

// Check on page load (for old input)
document.addEventListener('DOMContentLoaded', function() {
    toggleServiceField();
});
</script>
@endsection
