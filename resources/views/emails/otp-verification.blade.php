<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            background: #f8f9fa;
            border: 2px dashed #667eea;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
        .otp-code {
            font-size: 48px;
            font-weight: bold;
            letter-spacing: 10px;
            color: #667eea;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
        }
        .info-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
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
            <h1>🏥 Mabini Health Center</h1>
            <p style="margin: 5px 0 0 0; opacity: 0.9;">Email Verification</p>
        </div>
        
        <div class="content">
            <p class="greeting">Hello {{ $name }}!</p>
            
            <p>Thank you for registering with Mabini Health Center Queue System.</p>
            
            <p>To complete your registration and verify your email address, please use the following One-Time Password (OTP):</p>
            
            <div class="otp-box">
                <p style="margin: 0; color: #6c757d; font-size: 14px;">YOUR VERIFICATION CODE</p>
                <div class="otp-code">{{ $otp }}</div>
                <p style="margin: 0; color: #6c757d; font-size: 12px;">Enter this code in the verification page</p>
            </div>
            
            <div class="info-box">
                <strong>⏰ Important:</strong>
                <ul style="margin: 10px 0 0 0;">
                    <li>This code will expire in <strong>10 minutes</strong></li>
                    <li>Do not share this code with anyone</li>
                    <li>If you didn't request this code, please ignore this email</li>
                </ul>
            </div>
            
            <p style="margin-top: 30px;">Once verified, you'll be able to:</p>
            <ul>
                <li>✅ Request queue numbers online</li>
                <li>✅ Track your queue status in real-time</li>
                <li>✅ View your medical records and consultations</li>
                <li>✅ Receive notifications about your appointments</li>
            </ul>
            
            <p style="margin-top: 30px; color: #6c757d; font-size: 14px;">
                If you're having trouble, please contact the Mabini Health Center directly.
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
