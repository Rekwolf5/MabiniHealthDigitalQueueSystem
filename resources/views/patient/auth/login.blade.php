<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Login - Mabini Health Center</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        html, body {
            background: url('/images/Mabini.png?v=<?php echo time(); ?>') center/cover no-repeat fixed !important;
            min-height: 100vh !important;
        }
        .login-container {
            background: transparent !important;
            background-color: transparent !important;
        }
        .login-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95) !important;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3) !important;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .logo-container img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            margin-bottom: 1rem;
            border-radius: 50%;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            background: white;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo-container">
                <img src="{{ asset('images/health-center-logo.png') }}?v={{ time() }}" alt="Mabini Health Center Logo">
            </div>
            <div class="login-header">
                <h1>Patient Portal</h1>
                <p>Mabini Health Center</p>
            </div>

            @if ($errors->any())
                <div class="error-message">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('patient.login') }}" class="login-form">
                @csrf
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div style="position: relative;">
                        <input type="password" id="password" name="password" required style="padding-right: 2.5rem; font-size: 1rem; letter-spacing: 0.05em;">
                        <button type="button" onclick="togglePassword('password')" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: #6b7280; cursor: pointer; padding: 0.25rem;" aria-label="Toggle password visibility">
                            <i class="fas fa-eye" id="password-icon"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem;">
                    <input type="checkbox" name="remember" id="remember" style="width: auto; margin: 0;">
                    <label for="remember" style="margin: 0; cursor: pointer;">Remember me</label>
                </div>

                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Sign In
                </button>

                <div style="text-align: center; margin-top: 1rem;">
                    <a href="{{ route('patient.password.request') }}" style="color: #059669; text-decoration: none; font-size: 0.875rem;">
                        Forgot Password?
                    </a>
                </div>
            </form>

            <div style="text-align: center; margin-top: 1.5rem;">
                <p style="color: #718096;">
                    Don't have an account? 
                    <a href="{{ route('patient.register') }}" style="color: #48bb78; text-decoration: none;">
                        Register here
                    </a>
                </p>
                <p style="color: #718096; margin-top: 1rem;">
                    <a href="{{ route('login') }}" style="color: #059669; text-decoration: none;">
                        Staff/Admin Login
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script>
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
