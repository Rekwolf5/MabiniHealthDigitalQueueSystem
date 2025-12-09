<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Mabini Health Center</title>
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
        .password-requirements {
            margin-top: 0.5rem;
            padding: 0.75rem;
            background: rgba(59, 130, 246, 0.1);
            border-radius: 6px;
            border-left: 4px solid #3b82f6;
        }
        .password-requirements ul {
            margin: 0;
            padding-left: 1.25rem;
            font-size: 0.75rem;
            color: #475569;
            line-height: 1.6;
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
                <h1>Reset Password</h1>
                <p>Enter your new password below</p>
            </div>

            @if ($errors->any())
                <div class="error-message">
                    <strong><i class="fas fa-exclamation-triangle"></i> Error:</strong>
                    <ul style="margin: 0.5rem 0 0 0; padding-left: 1.25rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="login-form">
                @csrf
                
                <input type="hidden" name="token" value="{{ $token }}">
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ $email ?? old('email') }}" required autofocus readonly>
                </div>

                <div class="form-group">
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required>
                </div>

                <div class="password-requirements">
                    <strong style="font-size: 0.875rem; color: #1e40af;">
                        <i class="fas fa-shield-alt"></i> Password Requirements:
                    </strong>
                    <ul>
                        <li>At least 8 characters long</li>
                        <li>One uppercase letter (A-Z)</li>
                        <li>One lowercase letter (a-z)</li>
                        <li>One number (0-9)</li>
                        <li>One special character (!@#$%^&*)</li>
                    </ul>
                </div>

                <button type="submit" class="login-btn" style="margin-top: 1.5rem;">
                    <i class="fas fa-lock"></i>
                    Reset Password
                </button>
            </form>

            <div style="text-align: center; margin-top: 1.5rem;">
                <p style="color: #718096;">
                    <a href="{{ route('login') }}" style="color: #059669; text-decoration: none; font-weight: 500;">
                        <i class="fas fa-arrow-left"></i> Back to Login
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
