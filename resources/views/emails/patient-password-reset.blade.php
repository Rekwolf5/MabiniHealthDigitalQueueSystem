<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .email-header {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .email-header p {
            margin: 5px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .email-body {
            padding: 40px 30px;
        }
        .email-body h2 {
            color: #1f2937;
            margin: 0 0 20px 0;
            font-size: 20px;
        }
        .email-body p {
            color: #4b5563;
            line-height: 1.6;
            margin: 0 0 15px 0;
        }
        .reset-button {
            display: inline-block;
            background-color: #059669;
            color: white;
            text-decoration: none;
            padding: 14px 30px;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: 600;
            text-align: center;
        }
        .reset-button:hover {
            background-color: #047857;
        }
        .alternative-link {
            background-color: #f3f4f6;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            word-break: break-all;
        }
        .alternative-link p {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #6b7280;
        }
        .alternative-link a {
            color: #059669;
            text-decoration: none;
            font-size: 13px;
        }
        .email-footer {
            background-color: #f9fafb;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .email-footer p {
            color: #6b7280;
            font-size: 13px;
            margin: 5px 0;
        }
        .warning-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
        }
        .warning-box p {
            color: #92400e;
            margin: 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>🏥 Mabini Health Center</h1>
            <p>Patient Portal</p>
        </div>
        
        <div class="email-body">
            <h2>Reset Your Password</h2>
            
            <p>Hello,</p>
            
            <p>We received a request to reset your password for your Mabini Health Center Patient Portal account. Click the button below to create a new password:</p>
            
            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="reset-button">Reset Password</a>
            </div>
            
            <div class="alternative-link">
                <p>If the button doesn't work, copy and paste this link into your browser:</p>
                <a href="{{ $resetUrl }}">{{ $resetUrl }}</a>
            </div>
            
            <div class="warning-box">
                <p><strong>⚠️ Security Notice:</strong> This password reset link will expire in 60 minutes. If you didn't request a password reset, please ignore this email or contact the health center if you have concerns.</p>
            </div>
            
            <p>For your security, this link can only be used once and will expire after {{ config('auth.passwords.patient_accounts.expire', 60) }} minutes.</p>
        </div>
        
        <div class="email-footer">
            <p><strong>Mabini Health Center</strong></p>
            <p>This is an automated email. Please do not reply to this message.</p>
            <p style="margin-top: 15px; font-size: 12px;">If you need assistance, please visit the health center or contact our staff.</p>
        </div>
    </div>
</body>
</html>
