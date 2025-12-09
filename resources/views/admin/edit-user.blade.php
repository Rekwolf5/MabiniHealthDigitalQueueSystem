@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Edit User</h1>
                    <p class="text-muted mb-0">Update user information and permissions</p>
                </div>
                <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Users
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- User Information Card -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">User Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" id="editUserForm">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Full Name <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="text" 
                                class="form-control @error('name') is-invalid @enderror" 
                                id="name" 
                                name="name" 
                                value="{{ old('name', $user->name) }}"
                                placeholder="Enter full name"
                                required
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                Email Address <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                id="email" 
                                name="email" 
                                value="{{ old('email', $user->email) }}"
                                placeholder="email@example.com"
                                required
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Phone (hidden field, keeping it for compatibility) -->
                        <input type="hidden" name="phone" value="{{ old('phone', $user->phone ?? '') }}">

                        <!-- Role -->
                        <div class="mb-3">
                            <label for="role" class="form-label">
                                Role <span class="text-danger">*</span>
                            </label>
                            <select 
                                class="form-select @error('role') is-invalid @enderror" 
                                id="role" 
                                name="role"
                                required
                                onchange="toggleServiceField()"
                            >
                                <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>Service Staff</option>
                                <option value="front_desk" {{ old('role', $user->role) == 'front_desk' ? 'selected' : '' }}>Front Desk</option>
                                <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>Manager</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrator</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Service Staff: Assigned to specific service (e.g., Pharmacy, Laboratory)<br>
                                Front Desk: Manages patient registration and queue<br>
                                Manager: Full access except user management
                            </small>
                        </div>

                        <!-- Service Assignment (for staff only) -->
                        <div class="mb-3" id="serviceField" style="display: {{ old('role', $user->role) == 'staff' ? 'block' : 'none' }};">
                            <label for="service_id" class="form-label">
                                Assigned Service <span class="text-danger">*</span>
                            </label>
                            <select 
                                class="form-select @error('service_id') is-invalid @enderror" 
                                id="service_id" 
                                name="service_id"
                            >
                                <option value="">-- Select Service --</option>
                                @foreach(\App\Models\Service::where('is_active', true)->get() as $service)
                                    <option value="{{ $service->id }}" {{ old('service_id', $user->service_id) == $service->id ? 'selected' : '' }}>
                                        {{ $service->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('service_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Staff must be assigned to a service they will manage
                            </small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update User
                            </button>
                            <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password Card -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Change Password</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" id="changePasswordForm">
                        @csrf
                        @method('PUT')

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Leave password fields blank to keep the current password
                        </div>

                        <!-- New Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                New Password
                            </label>
                            <div class="input-group">
                                <input 
                                    type="password" 
                                    class="form-control @error('password') is-invalid @enderror" 
                                    id="password" 
                                    name="password"
                                    placeholder="Enter new password"
                                >
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                Minimum 8 characters, must include uppercase, lowercase, number, and special character
                            </small>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">
                                Confirm New Password
                            </label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="password_confirmation" 
                                name="password_confirmation"
                                placeholder="Re-enter new password"
                            >
                        </div>

                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key me-2"></i>Change Password
                        </button>
                    </form>
                </div>
            </div>

            <!-- Delete User Card -->
            @if($user->id !== auth()->id())
            <div class="card shadow-sm border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Danger Zone</h5>
                </div>
                <div class="card-body">
                    <h6>Delete User Account</h6>
                    <p class="text-muted mb-3">
                        Once deleted, this user will no longer be able to access the system. This action cannot be undone.
                    </p>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                        <i class="fas fa-trash-alt me-2"></i>Delete User
                    </button>

                    <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" id="deleteUserForm" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
            @else
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                You cannot delete your own account while logged in.
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- User Details Card -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-user me-2"></i>User Details
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">User ID</small>
                        <strong>{{ $user->id }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Current Role</small>
                        <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'doctor' ? 'success' : 'primary') }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Account Created</small>
                        <strong>{{ $user->created_at->format('M d, Y h:i A') }}</strong>
                    </div>
                    <div>
                        <small class="text-muted d-block">Last Updated</small>
                        <strong>{{ $user->updated_at->format('M d, Y h:i A') }}</strong>
                    </div>
                </div>
            </div>

            <!-- Password Requirements Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-shield-alt me-2"></i>Password Requirements
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0" style="font-size: 0.9rem;">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            At least 8 characters long
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            One uppercase letter (A-Z)
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            One lowercase letter (a-z)
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            One number (0-9)
                        </li>
                        <li>
                            <i class="fas fa-check-circle text-success me-2"></i>
                            One special character (!@#$%^&*)
                        </li>
                    </ul>
                </div>
            </div>
        </div>
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

.btn {
    padding: 0.5rem 1.25rem;
    border-radius: 6px;
    font-weight: 500;
}

.input-group .btn {
    padding: 0.625rem 0.875rem;
}
</style>

<script>
// Toggle password visibility
document.getElementById('togglePassword').addEventListener('click', function() {
    const password = document.getElementById('password');
    const icon = document.getElementById('toggleIcon');
    
    if (password.type === 'password') {
        password.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        password.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});

// Password form validation
document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('password_confirmation').value;
    
    // Only validate if password is being changed
    if (password || confirmPassword) {
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match!');
            return false;
        }
        
        // Check password requirements
        const hasUpperCase = /[A-Z]/.test(password);
        const hasLowerCase = /[a-z]/.test(password);
        const hasNumber = /\d/.test(password);
        const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
        const hasMinLength = password.length >= 8;
        
        if (!hasUpperCase || !hasLowerCase || !hasNumber || !hasSpecialChar || !hasMinLength) {
            e.preventDefault();
            alert('Password does not meet requirements. Please check the password requirements and try again.');
            return false;
        }
    }
});

// Delete confirmation
function confirmDelete() {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        if (confirm('This will permanently delete all user data. Are you absolutely sure?')) {
            document.getElementById('deleteUserForm').submit();
        }
    }
}

// Toggle service field based on role
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
        serviceSelect.value = ''; // Clear selection
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleServiceField();
});
</script>
@endsection
