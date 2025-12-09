@extends('layouts.app')

@section('title', 'QR Scanner - Mabini Health Center')
@section('page-title', 'QR Code Scanner')

@section('content')
<div class="container">
    <div class="scanner-card">
        <div class="scanner-header">
            <h2><i class="fas fa-qrcode"></i> Scan Patient QR Code</h2>
            <p>Scan the QR code on patient's ticket to verify their queue number</p>
        </div>

        <div class="scanner-body">
            <!-- Manual Input Option -->
            <div class="manual-input-section">
                <h3><i class="fas fa-keyboard"></i> Enter Queue Number or QR Code</h3>
                <p style="color: #6b7280; margin-bottom: 15px; font-size: 0.9rem;">
                    You can enter either the queue number (e.g., DENT-P001) or paste the full QR code hash
                </p>
                <form id="manualVerifyForm">
                    @csrf
                    <div class="input-group">
                        <input 
                            type="text" 
                            id="qrCodeInput" 
                            class="form-control" 
                            placeholder="Enter queue number (DENT-P001) or QR code hash"
                            autocomplete="off"
                        >
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Verify
                        </button>
                    </div>
                </form>
            </div>

            <!-- Camera Scanner (Future Enhancement) -->
            <div class="camera-scanner-placeholder">
                <i class="fas fa-camera" style="font-size: 4rem; color: #d1d5db;"></i>
                <p style="color: #9ca3af; margin-top: 15px;">Camera scanner coming soon</p>
                <p style="color: #6b7280; font-size: 0.9rem;">For now, use queue number or QR hash above</p>
            </div>
        </div>

        <!-- Verification Result -->
        <div id="verificationResult" class="verification-result" style="display: none;">
            <div id="resultContent"></div>
        </div>
    </div>

    <!-- Recent Scans -->
    <div class="recent-scans-card">
        <h3><i class="fas fa-history"></i> Recent Verifications</h3>
        <div id="recentScans" class="recent-scans-list">
            <p class="no-scans">No recent scans</p>
        </div>
    </div>
</div>

<style>
    .container {
        max-width: 900px;
        margin: 0 auto;
        padding: 20px;
    }

    .scanner-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 30px;
        margin-bottom: 30px;
    }

    .scanner-header {
        text-align: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #e5e7eb;
    }

    .scanner-header h2 {
        font-size: 2rem;
        color: #1e40af;
        margin-bottom: 10px;
    }

    .scanner-header p {
        color: #6b7280;
        font-size: 1rem;
    }

    .scanner-body {
        margin-bottom: 30px;
    }

    .manual-input-section {
        margin-bottom: 30px;
    }

    .manual-input-section h3 {
        font-size: 1.2rem;
        color: #374151;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .input-group {
        display: flex;
        gap: 10px;
    }

    .input-group input {
        flex: 1;
        padding: 12px 15px;
        border: 2px solid #d1d5db;
        border-radius: 8px;
        font-size: 1rem;
        transition: border-color 0.3s;
    }

    .input-group input:focus {
        outline: none;
        border-color: #3b82f6;
    }

    .input-group button {
        padding: 12px 24px;
        background: #3b82f6;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.3s;
        min-height: 48px;
        touch-action: manipulation;
        -webkit-tap-highlight-color: transparent;
    }

    .input-group button:hover {
        background: #2563eb;
    }

    .input-group button:active {
        transform: scale(0.98);
    }

    .camera-scanner-placeholder {
        text-align: center;
        padding: 60px 20px;
        background: #f9fafb;
        border: 2px dashed #d1d5db;
        border-radius: 8px;
        margin-top: 20px;
    }

    .verification-result {
        margin-top: 30px;
        padding: 20px;
        border-radius: 8px;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .verification-result.success {
        background: #d1fae5;
        border: 2px solid #10b981;
    }

    .verification-result.error {
        background: #fee2e2;
        border: 2px solid #ef4444;
    }

    .result-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
        font-size: 1.2rem;
        font-weight: bold;
    }

    .result-header.success {
        color: #065f46;
    }

    .result-header.error {
        color: #991b1b;
    }

    .patient-details {
        background: white;
        padding: 15px;
        border-radius: 6px;
        margin-top: 15px;
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

    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: bold;
    }

    .status-waiting {
        background: #fef3c7;
        color: #92400e;
    }

    .status-consulting {
        background: #dbeafe;
        color: #1e40af;
    }

    .status-completed {
        background: #d1fae5;
        color: #065f46;
    }

    .recent-scans-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 30px;
    }

    .recent-scans-card h3 {
        font-size: 1.3rem;
        color: #374151;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .recent-scans-list {
        max-height: 400px;
        overflow-y: auto;
    }

    .scan-item {
        padding: 15px;
        background: #f9fafb;
        border-left: 4px solid #3b82f6;
        border-radius: 6px;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .scan-item .queue-number {
        font-weight: bold;
        font-size: 1.1rem;
        color: #1e40af;
    }

    .scan-item .patient-name {
        color: #374151;
    }

    .scan-item .scan-time {
        color: #9ca3af;
        font-size: 0.85rem;
    }

    .no-scans {
        text-align: center;
        color: #9ca3af;
        padding: 40px 20px;
        font-style: italic;
    }

    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
        .container {
            padding: 10px;
        }

        .scanner-card {
            padding: 20px 15px;
        }

        .scanner-header h2 {
            font-size: 1.5rem;
        }

        .input-group {
            flex-direction: column;
            gap: 10px;
        }

        .input-group button {
            width: 100%;
            padding: 14px 24px;
            font-size: 1rem;
        }

        .input-group input {
            padding: 14px 15px;
            font-size: 1rem;
        }

        .recent-scans-card {
            padding: 20px 15px;
        }

        .scan-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
    }

    @media (max-width: 480px) {
        .scanner-header h2 {
            font-size: 1.3rem;
        }

        .scanner-header p {
            font-size: 0.9rem;
        }

        .camera-scanner-placeholder {
            padding: 40px 15px;
        }

        .manual-input-section h3 {
            font-size: 1rem;
        }
    }
</style>

<script>
    let recentScans = [];

    document.getElementById('manualVerifyForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const qrCode = document.getElementById('qrCodeInput').value.trim();
        
        if (!qrCode) {
            showResult(false, 'Please enter a QR code or queue number');
            return;
        }

        // Show loading
        const resultDiv = document.getElementById('verificationResult');
        resultDiv.style.display = 'block';
        resultDiv.className = 'verification-result';
        resultDiv.innerHTML = '<div style="text-align: center;"><i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #3b82f6;"></i><p style="margin-top: 10px; color: #6b7280;">Verifying...</p></div>';

        try {
            const response = await fetch('{{ route('qr.verify') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ qr_code: qrCode })
            });

            // Check if response is ok
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                showResult(false, errorData.message || `Server error: ${response.status}`);
                console.error('Server response error:', response.status, errorData);
                return;
            }

            const data = await response.json();
            console.log('Verification response:', data);

            if (data.success) {
                showResult(true, data.message || 'Patient verified successfully!', data.queue);
                addToRecentScans(data.queue);
                document.getElementById('qrCodeInput').value = '';
            } else {
                showResult(false, data.message || 'Invalid QR code');
            }
        } catch (error) {
            console.error('Verification error:', error);
            showResult(false, 'Network error. Please check your connection and try again.<br><small>Error: ' + error.message + '</small>');
        }
    });

    function showResult(success, message, queueData = null) {
        const resultDiv = document.getElementById('verificationResult');
        resultDiv.style.display = 'block';
        resultDiv.className = 'verification-result ' + (success ? 'success' : 'error');

        let html = `
            <div class="result-header ${success ? 'success' : 'error'}">
                <i class="fas fa-${success ? 'check-circle' : 'times-circle'}"></i>
                ${message}
            </div>
        `;

        if (success && queueData) {
            html += `
                <div class="patient-details">
                    <div class="detail-row">
                        <span class="detail-label">Queue Number:</span>
                        <span class="detail-value" style="font-weight: bold; color: #1e40af; font-size: 1.2rem;">${queueData.queue_number}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Patient Name:</span>
                        <span class="detail-value">${queueData.patient_name}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Service Type:</span>
                        <span class="detail-value">${queueData.service_type}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Priority:</span>
                        <span class="detail-value">${queueData.priority}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Status:</span>
                        <span class="status-badge status-${queueData.status.toLowerCase()}">${queueData.status}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Arrived At:</span>
                        <span class="detail-value">${queueData.arrived_at || 'N/A'}</span>
                    </div>
                </div>
            `;
        }

        resultDiv.innerHTML = html;
    }

    function addToRecentScans(queueData) {
        const scanItem = {
            queue_number: queueData.queue_number,
            patient_name: queueData.patient_name,
            time: new Date().toLocaleTimeString()
        };

        recentScans.unshift(scanItem);
        if (recentScans.length > 10) recentScans.pop();

        updateRecentScans();
    }

    function updateRecentScans() {
        const recentScansList = document.getElementById('recentScans');
        
        if (recentScans.length === 0) {
            recentScansList.innerHTML = '<p class="no-scans">No recent scans</p>';
            return;
        }

        let html = '';
        recentScans.forEach(scan => {
            html += `
                <div class="scan-item">
                    <div>
                        <div class="queue-number">${scan.queue_number}</div>
                        <div class="patient-name">${scan.patient_name}</div>
                    </div>
                    <div class="scan-time">${scan.time}</div>
                </div>
            `;
        });

        recentScansList.innerHTML = html;
    }

    // Auto-focus on input
    document.getElementById('qrCodeInput').focus();
</script>
@endsection
