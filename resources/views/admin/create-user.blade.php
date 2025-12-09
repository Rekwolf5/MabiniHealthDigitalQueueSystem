@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Create New User</h1>
                    <p class="text-muted mb-0">Add a new staff member or administrator</p>
                </div>
                <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Users
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">User Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.store') }}" method="POST" id="createUserForm">
                        @csrf

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
                                value="{{ old('name') }}"
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
                                value="{{ old('email') }}"
                                placeholder="email@example.com"
                                required
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                This will be used for login credentials
                            </small>
                        </div>

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
                                <option value="">Select role...</option>
                                <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Service Staff</option>
                                <option value="front_desk" {{ old('role') == 'front_desk' ? 'selected' : '' }}>Front Desk</option>
                                <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <strong>Service Staff:</strong> Assigned to specific service (e.g., Pharmacy, Laboratory)<br>
                                <strong>Front Desk:</strong> Manages patient registration and queue<br>
                                <strong>Manager:</strong> Can access all features except user management<br>
                                <strong>Admin:</strong> Full system access including user management
                            </small>
                        </div>

                        <!-- Assigned Service (Only for Staff) -->
                        <div class="mb-3" id="serviceField" style="display: none;">
                            <label for="service_id" class="form-label">
                                Assigned Service <span class="text-danger">*</span>
                            </label>
                            <select 
                                class="form-select @error('service_id') is-invalid @enderror" 
                                id="service_id" 
                                name="service_id"
                            >
                                <option value="">Select service...</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                        {{ $service->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('service_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Assign this staff member to a specific service (multiple staff can work on the same service)
                            </small>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input 
                                    type="password" 
                                    class="form-control @error('password') is-invalid @enderror" 
                                    id="password" 
                                    name="password"
                                    placeholder="Enter password"
                                    required
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
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">
                                Confirm Password <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="password_confirmation" 
                                name="password_confirmation"
                                placeholder="Re-enter password"
                                required
                            >
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-user-plus me-2"></i>Create User
                            </button>
                            <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Password Requirements Card -->
            <div class="card shadow-sm mb-3">
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

            <!-- Role Permissions Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-user-shield me-2"></i>Role Permissions
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="text-primary">Staff</h6>
                    <ul class="mb-3" style="font-size: 0.9rem;">
                        <li>Manage queue and consultations</li>
                        <li>View and edit patient records</li>
                        <li>Manage medicine inventory</li>
                        <li>Dispense medications</li>
                        <li>View reports</li>
                    </ul>

                    <h6 class="text-success">Doctor</h6>
                    <ul class="mb-3" style="font-size: 0.9rem;">
                        <li>View patient information</li>
                        <li>Manage consultations</li>
                        <li>Create medical records</li>
                        <li>Prescribe medications</li>
                        <li>View medical history</li>
                    </ul>

                    <h6 class="text-danger">Administrator</h6>
                    <ul class="mb-0" style="font-size: 0.9rem;">
                        <li>All staff permissions</li>
                        <li>Create and manage users</li>
                        <li>View activity logs</li>
                        <li>Configure system settings</li>
                        <li>Full system access</li>
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

// Form validation
document.getElementById('createUserForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('password_confirmation').value;
    
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
});
</script>
@endsection
