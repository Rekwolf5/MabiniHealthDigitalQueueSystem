<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Registration - Mabini Health Center</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-card" style="max-width: 500px;">
            <div class="login-header">
                <h1>Patient Registration</h1>
                <p>Create your account</p>
            </div>

            @if ($errors->any())
                <div class="error-message">
                    <ul style="margin: 0; padding-left: 1rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('patient.register') }}" class="login-form" id="registerForm">
                @csrf
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="middle_name">M.I.</label>
                        <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name') }}" placeholder="e.g., S." maxlength="50">
                        <small style="color: #a0aec0; font-size: 0.7rem;">Optional</small>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr; gap: 0.75rem;">
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="suffix">Suffix</label>
                        <select id="suffix_select" onchange="toggleCustomSuffix()" style="font-size: 0.9rem; margin-bottom: 0.5rem;">
                            <option value="">None</option>
                            <option value="Jr." {{ old('suffix') == 'Jr.' ? 'selected' : '' }}>Jr.</option>
                            <option value="Sr." {{ old('suffix') == 'Sr.' ? 'selected' : '' }}>Sr.</option>
                            <option value="II" {{ old('suffix') == 'II' ? 'selected' : '' }}>II</option>
                            <option value="III" {{ old('suffix') == 'III' ? 'selected' : '' }}>III</option>
                            <option value="IV" {{ old('suffix') == 'IV' ? 'selected' : '' }}>IV</option>
                            <option value="V" {{ old('suffix') == 'V' ? 'selected' : '' }}>V</option>
                            <option value="custom">Custom...</option>
                        </select>
                        <input type="text" 
                               id="suffix_custom" 
                               name="suffix" 
                               value="{{ old('suffix') }}" 
                               placeholder="Enter custom suffix"
                               style="display: none; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem;">
                        <small style="color: #a0aec0; font-size: 0.7rem;">Optional</small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div style="position: relative;">
                            <input type="password" id="password" name="password" required style="padding-right: 2.5rem; font-size: 1rem; letter-spacing: 0.05em;">
                            <button type="button" onclick="togglePassword('password')" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: #6b7280; cursor: pointer; padding: 0.25rem;" aria-label="Toggle password visibility">
                                <i class="fas fa-eye" id="password-icon"></i>
                            </button>
                        </div>
                        <small style="color: #a0aec0; font-size: 0.7rem;">Min 8 chars, uppercase, number, symbol</small>
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <div style="position: relative;">
                            <input type="password" id="password_confirmation" name="password_confirmation" required style="padding-right: 2.5rem; font-size: 1rem; letter-spacing: 0.05em;">
                            <button type="button" onclick="togglePassword('password_confirmation')" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: #6b7280; cursor: pointer; padding: 0.25rem;" aria-label="Toggle password visibility">
                                <i class="fas fa-eye" id="password_confirmation-icon"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="date_of_birth">Date of Birth</label>
                        <input type="date" 
                               id="date_of_birth" 
                               name="date_of_birth" 
                               value="{{ old('date_of_birth') }}" 
                               max="{{ date('Y-m-d') }}"
                               onchange="calculateAge()"
                               required>
                        <small style="color: #a0aec0; font-size: 0.75rem;">
                            <i class="fas fa-info-circle"></i> Age will be calculated automatically
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="age">Age</label>
                        <input type="number" 
                               id="age" 
                               name="age" 
                               value="{{ old('age') }}" 
                               readonly 
                               style="background: #2d3748; cursor: not-allowed;"
                               placeholder="Auto-calculated">
                        <small style="color: #a0aec0; font-size: 0.75rem;">
                            <i class="fas fa-info-circle"></i> Calculated from date of birth
                        </small>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="contact">Contact Number (Philippine Mobile)</label>
                    <input type="tel" 
                           id="contact" 
                           name="contact" 
                           value="{{ old('contact') }}" 
                           placeholder="09XXXXXXXXX or +639XXXXXXXXX"
                           pattern="^(09|\+639)\d{9}$"
                           required>
                    <small style="color: #a0aec0; font-size: 0.75rem;">
                        <i class="fas fa-info-circle"></i> Format: 09XXXXXXXXX or +639XXXXXXXXX
                    </small>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                </div>

                <button type="submit" class="login-btn">
                    <i class="fas fa-user-plus"></i>
                    Create Account
                </button>
            </form>

            <div style="text-align: center; margin-top: 1.5rem;">
                <p style="color: #718096;">
                    Already have an account? 
                    <a href="{{ route('patient.login') }}" style="color: #48bb78; text-decoration: none;">
                        Sign in here
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script>
    // Custom suffix toggle
    function toggleCustomSuffix() {
        const select = document.getElementById('suffix_select');
        const customInput = document.getElementById('suffix_custom');
        
        if (select.value === 'custom') {
            customInput.style.display = 'block';
            customInput.value = '';
            customInput.focus();
        } else {
            customInput.style.display = 'none';
            customInput.value = select.value;
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('registerForm');
        const contactInput = document.getElementById('contact');
        
        // Check if suffix has a custom value on load
        const suffixCustom = document.getElementById('suffix_custom');
        const suffixSelect = document.getElementById('suffix_select');
        const commonSuffixes = ['Jr.', 'Sr.', 'II', 'III', 'IV', 'V', ''];
        
        if (suffixCustom && suffixCustom.value && !commonSuffixes.includes(suffixCustom.value)) {
            suffixSelect.value = 'custom';
            suffixCustom.style.display = 'block';
        }
        
        // Contact number validation and formatting
        function validatePhilippineNumber(input) {
            if (!input.value) return false;
            const phoneRegex = /^(09|\+639)\d{9}$/;
            return phoneRegex.test(input.value);
        }
        
        function formatContactNumber(input) {
            // Remove all non-digit characters except +
            let value = input.value.replace(/[^\d+]/g, '');
            
            // Auto-format: if starts with 639, add +
            if (value.startsWith('639') && !value.startsWith('+639')) {
                value = '+' + value;
            }
            
            input.value = value;
        }
        
        // Real-time validation for contact number
        contactInput.addEventListener('input', function() {
            formatContactNumber(this);
            
            if (this.value && !validatePhilippineNumber(this)) {
                this.style.borderColor = '#fc8181';
                this.style.background = '#fff5f5';
            } else {
                this.style.borderColor = '#48bb78';
                this.style.background = '#f0fff4';
            }
        });
        
        // Form submission validation
        form.addEventListener('submit', function(e) {
            // Validate contact number
            if (!validatePhilippineNumber(contactInput)) {
                e.preventDefault();
                alert('Please enter a valid Philippine mobile number.\n\nValid formats:\n• 09XXXXXXXXX (11 digits)\n• +639XXXXXXXXX (13 digits)\n\nExample: 09171234567 or +639171234567');
                contactInput.focus();
                contactInput.style.borderColor = '#fc8181';
                contactInput.style.background = '#fff5f5';
                return false;
            }
        });
        
        // Calculate age from date of birth
        function calculateAge() {
            const dobInput = document.getElementById('date_of_birth');
            const ageInput = document.getElementById('age');
            
            if (dobInput && dobInput.value) {
                const dob = new Date(dobInput.value);
                const today = new Date();
                
                let age = today.getFullYear() - dob.getFullYear();
                const monthDiff = today.getMonth() - dob.getMonth();
                
                // Adjust age if birthday hasn't occurred this year
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                    age--;
                }
                
                ageInput.value = age;
            }
        }
        
        // Calculate age on page load if date is already filled
        const dobInput = document.getElementById('date_of_birth');
        if (dobInput && dobInput.value) {
            calculateAge();
        }
    });

    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '-icon');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    </script>
</body>
</html>
