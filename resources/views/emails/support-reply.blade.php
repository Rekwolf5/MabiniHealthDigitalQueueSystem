<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Reply</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .email-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
            color: #333;
        }
        .message-box {
            background: #f9fafb;
            border-left: 4px solid #059669;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .message-box .label {
            font-weight: 600;
            color: #059669;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .message-box .text {
            color: #4b5563;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .reply-box {
            background: #d1fae5;
            border-left: 4px solid #059669;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .reply-box .label {
            font-weight: 600;
            color: #047857;
            font-size: 14px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        .reply-box .label svg {
            width: 20px;
            height: 20px;
            margin-right: 8px;
        }
        .reply-box .text {
            color: #065f46;
            white-space: pre-wrap;
            word-wrap: break-word;
            line-height: 1.6;
        }
        .info-box {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .info-box p {
            margin: 0;
            color: #92400e;
            font-size: 14px;
        }
        .cta-button {
            display: inline-block;
            background: #059669;
            color: white !important;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 6px;
            font-weight: 500;
            margin: 20px 0;
            transition: background 0.3s;
        }
        .cta-button:hover {
            background: #047857;
        }
        .footer {
            background: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 5px 0;
        }
        .divider {
            height: 1px;
            background: #e5e7eb;
            margin: 20px 0;
        }
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }
            .content {
                padding: 20px;
            }
            .header h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>📧 Support Reply</h1>
            <p>Response from Mabini Health Center</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Hello <strong>{{ $name }}</strong>,
            </div>

            <p>We have reviewed your support request and are providing a response below.</p>

            <!-- Original Message -->
            <div class="message-box">
                <div class="label">📝 Your Original Message</div>
                <div class="text">
                    <strong>Subject:</strong> {{ $subject }}<br><br>
                    {{ $originalMessage }}
                </div>
            </div>

            <!-- Admin Reply -->
            <div class="reply-box">
                <div class="label">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                    </svg>
                    Our Response
                </div>
                <div class="text">{{ $reply }}</div>
            </div>

            <div class="divider"></div>

            <!-- Additional Questions -->
            <p style="margin-top: 20px;">
                <strong>Have more questions?</strong><br>
                Feel free to submit another support request through your dashboard, and we'll be happy to help!
            </p>

            <center>
                <a href="{{ url('/patient/support') }}" class="cta-button">
                    Submit Another Request
                </a>
            </center>

            <!-- Info Box -->
            <div class="info-box">
                <p>
                    <strong>💡 Tip:</strong> Please do not reply directly to this email. 
                    Use the support form in your dashboard to continue the conversation.
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Mabini Health Center</strong></p>
            <p>Your Health, Our Priority</p>
            <p style="margin-top: 15px;">
                📍 Mabini, Batangas<br>
                📞 Contact: (123) 456-7890<br>
                📧 Email: {{ config('mail.from.address') }}
            </p>
            <p style="margin-top: 15px; color: #9ca3af;">
                This email was sent to {{ $name }} ({{ $originalMessage ? 'Patient' : 'User' }})
            </p>
        </div>
    </div>
</body>
</html>
