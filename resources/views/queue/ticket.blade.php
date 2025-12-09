<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Queue Ticket - {{ $queue->queue_number }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .ticket-container {
            max-width: 400px;
            margin: 0 auto;
            background: white;
            border: 3px dashed #333;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .clinic-header {
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e5e7eb;
        }

        .clinic-header h1 {
            font-size: 24px;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .clinic-header p {
            font-size: 14px;
            color: #6b7280;
        }

        .queue-number {
            font-size: 72px;
            font-weight: bold;
            color: #1e40af;
            margin: 20px 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .priority-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
            margin: 10px 0;
        }

        .priority-pwdpriority, .priority-pwd {
            background: #fee2e2;
            color: #991b1b;
            border: 2px solid #dc2626;
        }

        .priority-pregnant {
            background: #fce7f3;
            color: #831843;
            border: 2px solid #ec4899;
        }

        .priority-senior {
            background: #fef3c7;
            color: #92400e;
            border: 2px solid #f59e0b;
        }

        .priority-regular {
            background: #d1fae5;
            color: #065f46;
            border: 2px solid #10b981;
        }

        .ticket-details {
            margin: 20px 0;
            padding: 20px;
            background: #f9fafb;
            border-radius: 8px;
            text-align: left;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: bold;
            color: #374151;
        }

        .detail-value {
            color: #6b7280;
        }

        .qr-code {
            margin: 20px 0;
            padding: 15px;
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            display: inline-block;
        }

        .qr-code svg {
            display: block;
        }

        .instructions {
            margin-top: 20px;
            padding: 15px;
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            border-radius: 4px;
            text-align: left;
            font-size: 12px;
            color: #1e40af;
        }

        .instructions h3 {
            margin-bottom: 10px;
            font-size: 14px;
        }

        .instructions ul {
            margin-left: 20px;
        }

        .instructions li {
            margin: 5px 0;
        }

        .print-buttons {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .ticket-container {
                max-width: 100%;
                box-shadow: none;
                border: 3px dashed #333;
            }

            .print-buttons, .instructions {
                display: none;
            }

            .qr-code {
                background: white !important;
                border: 2px solid #000;
            }
        }

        .footer-note {
            margin-top: 20px;
            font-size: 11px;
            color: #9ca3af;
            font-style: italic;
        }

        .qr-code-text {
            margin-top: 10px;
            padding: 10px;
            background: #f9fafb;
            border-radius: 5px;
            font-family: monospace;
            font-size: 10px;
            word-break: break-all;
            color: #6b7280;
            cursor: pointer;
            border: 1px dashed #d1d5db;
        }

        .qr-code-text:hover {
            background: #f3f4f6;
            border-color: #3b82f6;
        }

        .copy-hint {
            font-size: 9px;
            color: #9ca3af;
            margin-top: 5px;
        }

        .copied-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: none;
            z-index: 9999;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
            }
            to {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        <div class="clinic-header">
            <h1><i class="fas fa-hospital"></i> Mabini Health Center</h1>
            <p>Queue Management System</p>
        </div>

        <div class="queue-number">{{ $queue->queue_number }}</div>

        <div class="priority-badge priority-{{ strtolower(str_replace('/', '', $queue->priority)) }}">
            @if($queue->priority === 'PWD/Priority' || $queue->priority === 'PWD')
                <i class="fas fa-wheelchair"></i> PWD / Priority
            @elseif($queue->priority === 'Pregnant')
                <i class="fas fa-baby"></i> Pregnant
            @elseif($queue->priority === 'Senior')
                <i class="fas fa-user-clock"></i> Senior Citizen
            @else
                <i class="fas fa-users"></i> Regular
            @endif
        </div>

        <div class="ticket-details">
            @if($queue->patient)
                <div class="detail-row">
                    <span class="detail-label">Patient:</span>
                    <span class="detail-value">{{ $queue->patient->first_name }} {{ $queue->patient->last_name }}</span>
                </div>
            @endif
            
            <div class="detail-row">
                <span class="detail-label">Service:</span>
                <span class="detail-value">{{ $queue->service_type }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Date:</span>
                <span class="detail-value">{{ $queue->arrived_at ? $queue->arrived_at->format('F d, Y') : now()->format('F d, Y') }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Time:</span>
                <span class="detail-value">{{ $queue->arrived_at ? $queue->arrived_at->format('h:i A') : now()->format('h:i A') }}</span>
            </div>
        </div>

        <!-- QR Code -->
        <div class="qr-code">
            <div id="qrcode" style="display: inline-block;"></div>
        </div>

        <!-- QR Code Text (for manual entry/copying) -->
        <div class="qr-code-text" id="qrCodeText" onclick="copyQRCode()" title="Click to copy">
            {{ $queue->qr_code }}
        </div>
        <div class="copy-hint">
            <i class="fas fa-copy"></i> Click above to copy QR code for verification
        </div>

        <div class="instructions">
            <h3><i class="fas fa-info-circle"></i> Instructions:</h3>
            <ul>
                <li>Please keep this ticket with you</li>
                <li>Show this QR code to staff when called</li>
                <li>Monitor the queue display for your number</li>
                <li>Wait in the designated waiting area</li>
                <li>This ticket is valid for today only</li>
            </ul>
        </div>

        <div class="print-buttons">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Print Ticket
            </button>
            @auth('patient')
                <a href="{{ route('patient.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            @else
                <a href="{{ route('queue.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Queue
                </a>
            @endauth
        </div>

        <div class="footer-note">
            Ticket generated on {{ now()->format('F d, Y h:i A') }}
        </div>
    </div>

    <!-- Copy notification -->
    <div id="copiedNotification" class="copied-notification">
        <i class="fas fa-check-circle"></i> QR Code copied to clipboard!
    </div>

    <!-- QR Code Generation Library -->
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    
    <script>
        // Generate QR Code
        const qrCodeData = "{{ $queue->qr_code }}";
        const qrcode = new QRCode(document.getElementById("qrcode"), {
            text: qrCodeData,
            width: 200,
            height: 200,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });

        function copyQRCode() {
            const qrCode = document.getElementById('qrCodeText').textContent.trim();
            
            // Copy to clipboard
            navigator.clipboard.writeText(qrCode).then(function() {
                // Show notification
                const notification = document.getElementById('copiedNotification');
                notification.style.display = 'block';
                
                // Hide after 3 seconds
                setTimeout(function() {
                    notification.style.display = 'none';
                }, 3000);
            }).catch(function(err) {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = qrCode;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                
                const notification = document.getElementById('copiedNotification');
                notification.style.display = 'block';
                setTimeout(function() {
                    notification.style.display = 'none';
                }, 3000);
            });
        }
    </script>
</body>
</html>
