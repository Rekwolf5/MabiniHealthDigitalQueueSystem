<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Mabini Health Center</title>
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
        .success-message {
            padding: 0.875rem;
            background-color: #d1fae5;
            border: 1px solid #6ee7b7;
            color: #065f46;
            border-radius: 6px;
            margin-bottom: 1rem;
            font-size: 0.875rem;
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
                <h1>Forgot Password?</h1>
                <p>Enter your email address to receive a password reset link</p>
            </div>

            @if (session('success'))
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="error-message">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="login-form">
                @csrf
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                </div>

                <button type="submit" class="login-btn">
                    <i class="fas fa-paper-plane"></i>
                    Send Reset Link
                </button>
            </form>

            <div style="text-align: center; margin-top: 1.5rem;">
                <p style="color: #718096;">
                    Remember your password? 
                    <a href="{{ route('login') }}" style="color: #059669; text-decoration: none; font-weight: 500;">
                        Back to Login
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
