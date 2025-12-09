<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: #059669;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 40px 30px;
        }
        .otp-box {
            background: #f0fdf4;
            border: 2px dashed #059669;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
        .otp-code {
            font-size: 48px;
            font-weight: bold;
            letter-spacing: 10px;
            color: #059669;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
        }
        .warning-box {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
        }
        ul {
            padding-left: 20px;
        }
        li {
            margin: 8px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔒 Password Reset Request</h1>
            <p style="margin: 5px 0 0 0; opacity: 0.9;">Mabini Health Center</p>
        </div>
        
        <div class="content">
            <p class="greeting">Hello {{ $name }}!</p>
            
            <p>We received a request to reset your password for your Mabini Health Center account.</p>
            
            <p>To reset your password, please use the following One-Time Password (OTP):</p>
            
            <div class="otp-box">
                <p style="margin: 0; color: #6c757d; font-size: 14px;">YOUR VERIFICATION CODE</p>
                <div class="otp-code">{{ $otp }}</div>
                <p style="margin: 0; color: #6c757d; font-size: 12px;">Enter this code on the verification page</p>
            </div>
            
            <div class="warning-box">
                <strong>⚠️ Security Notice:</strong>
                <ul style="margin: 10px 0 0 0;">
                    <li>This code will expire in <strong>10 minutes</strong></li>
                    <li>Never share this code with anyone</li>
                    <li>If you didn't request this, please ignore this email</li>
                    <li>Your password will remain unchanged unless you complete the reset process</li>
                </ul>
            </div>
            
            <p style="margin-top: 30px; color: #6c757d; font-size: 14px;">
                <strong>Why did I receive this?</strong><br>
                Someone (hopefully you) requested a password reset for your account. If this wasn't you, your account is still secure - just ignore this email.
            </p>
        </div>
        
        <div class="footer">
            <p style="margin: 0;">
                <strong>Mabini Health Center Queue System</strong><br>
                This is an automated email. Please do not reply.
            </p>
            <p style="margin: 10px 0 0 0;">
                © {{ date('Y') }} Mabini Health Center. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
