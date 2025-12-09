@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-qrcode"></i> QR Code Scanner
                    </h1>
                    <p class="text-muted mb-0">Scan patient queue tickets for quick check-in</p>
                </div>
                <a href="{{ route('queue.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Queue
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i>
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i>
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Scanner Section -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-camera"></i> QR Code Scanner
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Browser Compatibility Notice -->
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Camera Access Required</strong>
                        <p class="mb-2 mt-2">For the camera scanner to work:</p>
                        <ul class="mb-2" style="font-size: 0.9rem;">
                            <li>Allow camera permission when prompted by your browser</li>
                            <li>Make sure no other apps are using your camera</li>
                            <li>Use <strong>Chrome</strong> or <strong>Edge</strong> for best results</li>
                        </ul>
                        <small class="d-block mt-2 text-muted">
                            <i class="fas fa-lightbulb"></i> If camera doesn't work, use <strong>Manual Entry</strong> below
                        </small>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>

                    <!-- Scanner Status -->
                    <div id="scannerStatus" class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Ready to Scan:</strong> Position QR code in front of camera
                    </div>

                    <!-- Video Element -->
                    <div id="qr-reader" style="width: 100%; max-width: 600px; margin: 0 auto;">
                        <video id="qr-video" style="width: 100%; border: 3px solid #0d6efd; border-radius: 8px;"></video>
                    </div>

                    <!-- Scanner Controls -->
                    <div class="text-center mt-3">
                        <button id="startScanBtn" class="btn btn-success btn-lg">
                            <i class="fas fa-play"></i> Start Scanner
                        </button>
                        <button id="stopScanBtn" class="btn btn-danger btn-lg" style="display: none;">
                            <i class="fas fa-stop"></i> Stop Scanner
                        </button>
                    </div>

                    <!-- Manual Entry Option -->
                    <div class="mt-4">
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-keyboard"></i>
                            <strong>Recommended:</strong> Use Manual Entry if camera access is blocked by your browser
                        </div>
                        <h6 class="fw-bold text-dark mb-3">
                            <i class="fas fa-keyboard"></i> Manual Entry
                            <span class="badge bg-success ms-2">Fastest Method</span>
                            <small class="text-muted d-block mt-1" style="font-size: 0.85rem;">
                                Enter the queue number from the patient's ticket
                            </small>
                        </h6>
                        <form id="manualVerifyForm" class="mt-3">
                            @csrf
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="manualQrCode" class="form-label fw-semibold">
                                        Queue Number or QR Code
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-primary text-white">
                                            <i class="fas fa-hashtag"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control form-control-lg" 
                                               id="manualQrCode" 
                                               name="qr_code"
                                               placeholder="e.g., GP-P001 or DENT-R003"
                                               autocomplete="off"
                                               style="font-size: 1.1rem; font-weight: 600;">
                                    </div>
                                    <small class="text-muted mt-1 d-block">
                                        <i class="fas fa-lightbulb"></i> 
                                        Enter the queue number shown on the patient's ticket
                                    </small>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg w-100" id="manualVerifyBtn">
                                        <i class="fas fa-search"></i> Verify & Check-in
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        <!-- Quick Examples -->
                        <div class="mt-3 p-3 bg-light rounded">
                            <small class="fw-bold text-muted d-block mb-2">Common Queue Number Formats:</small>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-secondary">GP-P001</span>
                                <span class="badge bg-secondary">DENT-R005</span>
                                <span class="badge bg-secondary">PEDIA-P002</span>
                                <span class="badge bg-secondary">OB-P003</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scan Results Section -->
        <div class="col-lg-4">
            <!-- Instructions Card -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle"></i> How to Use
                    </h6>
                </div>
                <div class="card-body">
                    <ol class="mb-0" style="font-size: 0.9rem;">
                        <li class="mb-2">Click <strong>"Start Scanner"</strong> button</li>
                        <li class="mb-2">Allow camera access when prompted</li>
                        <li class="mb-2">Hold patient's ticket QR code to camera</li>
                        <li class="mb-2">System will auto-verify and check-in</li>
                        <li>View scan results on the right</li>
                    </ol>
                </div>
            </div>

            <!-- Recent Scans Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-history"></i> Recent Scans
                    </h6>
                </div>
                <div class="card-body">
                    <div id="recentScans" style="max-height: 400px; overflow-y: auto;">
                        <p class="text-muted text-center">No scans yet</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scan Result Modal -->
    <div class="modal fade" id="scanResultModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" id="modalHeader">
                    <h5 class="modal-title">
                        <i class="fas fa-qrcode"></i> Scan Result
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    <!-- Result content will be inserted here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 8px;
}

.card-header {
    border-radius: 8px 8px 0 0 !important;
    padding: 1rem 1.25rem;
}

#qr-reader {
    position: relative;
}

#qr-video {
    display: block;
}

.scan-item {
    border-left: 4px solid #198754;
    background: #f8f9fa;
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    border-radius: 4px;
}

.scan-item.error {
    border-left-color: #dc3545;
}

.scan-item .time {
    font-size: 0.75rem;
    color: #6c757d;
}

.scan-item .queue-number {
    font-size: 1.1rem;
    font-weight: 700;
    color: #0d6efd;
}

.scan-item .patient-name {
    font-weight: 600;
    color: #212529;
}

/* Mobile Responsive Improvements */
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 0.5rem !important;
        padding-right: 0.5rem !important;
    }

    .card-body {
        padding: 1rem;
    }

    #qr-reader {
        margin: 0 auto;
    }

    #qr-video {
        border-width: 2px;
    }

    .btn-lg {
        min-height: 48px;
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
        touch-action: manipulation;
        -webkit-tap-highlight-color: transparent;
    }

    #startScanBtn, #stopScanBtn {
        width: 100%;
        margin-bottom: 0.5rem;
    }

    #manualVerifyBtn {
        min-height: 50px;
        font-size: 1.05rem;
    }

    .input-group-lg .form-control {
        font-size: 1rem !important;
        min-height: 48px;
    }

    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }

    .d-flex.justify-content-between > a {
        order: 2;
        width: 100%;
        text-align: center;
    }

    .d-flex.justify-content-between > div {
        order: 1;
    }
}

@media (max-width: 480px) {
    h1.h3 {
        font-size: 1.25rem !important;
    }

    .card-header h5 {
        font-size: 1rem;
    }

    .btn-lg {
        padding: 0.65rem 1.25rem;
        font-size: 0.95rem;
    }
}
</style>

<!-- Include jsQR library for QR code scanning -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

<script>
console.log('=== QR SCANNER SCRIPT LOADING ===');

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== DOM LOADED ===');
    
    // Check if jsQR loaded
    if (typeof jsQR === 'undefined') {
        console.error('jsQR library failed to load');
        const statusDiv = document.getElementById('scannerStatus');
        if (statusDiv) {
            statusDiv.className = 'alert alert-danger';
            statusDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> <strong>Scanner Library Failed to Load</strong><br>' +
                                 '<small>Please check your internet connection and refresh the page, or use Manual Entry below.</small>';
        }
    } else {
        console.log('✓ jsQR library loaded successfully');
    }

    initializeScanner();
});

function initializeScanner() {
    console.log('=== INITIALIZING SCANNER ===');
    
    let video = document.getElementById('qr-video');
    let canvas = document.createElement('canvas');
    let canvasContext = canvas.getContext('2d');
    let scanning = false;
    let stream = null;

    const startBtn = document.getElementById('startScanBtn');
    const stopBtn = document.getElementById('stopScanBtn');
    const statusDiv = document.getElementById('scannerStatus');
    
    console.log('Video element:', video);
    console.log('Start button:', startBtn);
    console.log('Stop button:', stopBtn);
    
    if (!startBtn) {
        console.error('Start button not found!');
        return;
    }
    
    console.log('✓ All elements found, attaching event listeners...');

    // Start Scanner
    startBtn.addEventListener('click', async function() {
        console.log('=== START SCANNER CLICKED ===');
        
        // Check if accessing via IP address (not localhost)
        const hostname = window.location.hostname;
        const isIpAddress = /^(\d{1,3}\.){3}\d{1,3}$/.test(hostname);
        const isHttps = window.location.protocol === 'https:';
        
        console.log('Hostname:', hostname);
        console.log('Is IP Address:', isIpAddress);
        console.log('Is HTTPS:', isHttps);
        
        if (isIpAddress && !isHttps) {
            console.log('Blocking due to IP + HTTP');
            updateStatus('danger', 'Browser Security Restriction', 
            '<strong>Camera access is blocked because you\'re using an IP address with HTTP</strong><br><br>' +
            '<strong>Solution Options:</strong><br>' +
            '1. <strong>Use Manual Entry below</strong> (Recommended - Works immediately)<br>' +
            '2. Access via <code>http://localhost:8000</code> instead of the IP address<br>' +
            '3. For Chrome: Visit <code>chrome://flags/#unsafely-treat-insecure-origin-as-secure</code><br>' +
            '   - Add <code>http://' + hostname + ':8000</code> to the list<br>' +
            '   - Restart Chrome<br><br>' +
            '<small class="text-muted"><i class="fas fa-info-circle"></i> This is a browser security feature - camera only works on HTTPS or localhost</small>'
        );
        return;
    }
    
    try {
        // Request camera access
        stream = await navigator.mediaDevices.getUserMedia({ 
            video: { facingMode: 'environment' } // Use back camera on mobile
        });
        
        video.srcObject = stream;
        video.setAttribute('playsinline', true); // Required for iOS
        video.play();
        
        scanning = true;
        startBtn.style.display = 'none';
        stopBtn.style.display = 'inline-block';
        
        updateStatus('info', 'Scanner Active', 'Point camera at QR code');
        
        // Start scanning loop
        requestAnimationFrame(tick);
        
    } catch (err) {
        console.error('Camera access error:', err);
        let errorMsg = 'Could not access camera. ';
        
        // Provide specific guidance based on error
        if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
            errorMsg += '<br><strong>Camera Permission Denied</strong><br>' +
                       '<small>1. Click the camera icon 🎥 in your browser address bar<br>' +
                       '2. Select "Allow" to grant camera access<br>' +
                       '3. Refresh the page and try again</small>';
        } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
            errorMsg += '<br><strong>No Camera Found</strong><br>' +
                       '<small>Please connect a webcam or use manual entry below</small>';
        } else if (err.name === 'NotReadableError' || err.name === 'TrackStartError') {
            errorMsg += '<br><strong>Camera Already in Use</strong><br>' +
                       '<small>Close other apps using your camera (Zoom, Teams, etc.) and try again</small>';
        } else {
            errorMsg += '<br><small>Please use Manual Entry below or check browser camera permissions</small>';
        }
        
        updateStatus('danger', 'Camera Error', errorMsg);
    }
});

    // Stop Scanner
    stopBtn.addEventListener('click', function() {
        stopScanning();
    });

    // Helper function to stop scanning
    function stopScanning() {
        scanning = false;
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        video.srcObject = null;
        startBtn.style.display = 'inline-block';
        stopBtn.style.display = 'none';
        updateStatus('info', 'Scanner Stopped', 'Click "Start Scanner" to resume');
    }

    // Scanning loop
    function tick() {
        if (!scanning) return;
        
        // Check if jsQR is available
        if (typeof jsQR === 'undefined') {
            console.error('jsQR library not loaded');
            updateStatus('danger', 'Scanner Error', 'QR scanning library not loaded. Please refresh the page or use Manual Entry below.');
            stopScanning();
            return;
        }
        
        if (video.readyState === video.HAVE_ENOUGH_DATA) {
            try {
                canvas.height = video.videoHeight;
                canvas.width = video.videoWidth;
                canvasContext.drawImage(video, 0, 0, canvas.width, canvas.height);
                
                let imageData = canvasContext.getImageData(0, 0, canvas.width, canvas.height);
                let code = jsQR(imageData.data, imageData.width, imageData.height, {
                    inversionAttempts: 'dontInvert',
                });
                
                if (code) {
                    // QR Code detected!
                    console.log('QR Code detected:', code.data);
                    updateStatus('success', 'QR Code Detected', 'Verifying...');
                    verifyQRCode(code.data);
                    stopScanning(); // Stop after successful scan
                    return;
                }
            } catch (error) {
                console.error('Scanning error:', error);
                updateStatus('danger', 'Scanning Error', error.message + '<br><small>Please use Manual Entry below.</small>');
                stopScanning();
                return;
            }
        }
        
        requestAnimationFrame(tick);
    }

    // Verify QR Code
    async function verifyQRCode(qrData) {
        console.log('=== VERIFICATION STARTED ===');
        console.log('QR Code Data:', qrData);
        
        // Use multiple URL formats to ensure it works
        const urlFromBlade = '{{ route("queue.verify-qr") }}';
        const urlDirect = '/qr-verify';
        const urlFull = window.location.origin + '/qr-verify';
        
        console.log('Route URL (Blade):', urlFromBlade);
        console.log('Route URL (Direct):', urlDirect);
        console.log('Route URL (Full):', urlFull);
        console.log('Window Location:', window.location.href);
        
        // Use the direct URL since standalone test works
        const verifyUrl = urlDirect;
        console.log('Using URL:', verifyUrl);
        
        try {
            // Check CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('CSRF token not found!');
                alert('ERROR: CSRF token missing. Please refresh the page.');
                throw new Error('CSRF token not found in page');
            }
            console.log('✓ CSRF Token found:', csrfToken.content.substring(0, 20) + '...');
            
            // Prepare request data
            const requestData = { qr_code: qrData };
            console.log('Request Data:', requestData);
            
            // Make request
            console.log('Sending POST request to:', verifyUrl);
            const response = await fetch(verifyUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(requestData),
                credentials: 'same-origin'
            });
            
            console.log('✓ Response received');
            console.log('Response Status:', response.status, response.statusText);
            console.log('Response OK:', response.ok);
            
            // Get response text first
            const responseText = await response.text();
            console.log('Response Text (raw):', responseText.substring(0, 500));
            
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            console.log('Content-Type:', contentType);
            
            let result;
            try {
                result = JSON.parse(responseText);
                console.log('✓ JSON parsed successfully');
                console.log('Response Data:', result);
            } catch (jsonError) {
                console.error('JSON Parse Error:', jsonError);
                console.error('Response was:', responseText);
                alert('ERROR: Server returned invalid response. Check console.');
                throw new Error('Invalid JSON response from server');
            }
            
            // Handle response
            if (result.success) {
                console.log('✓ SUCCESS - Patient verified!');
                console.log('Queue data:', result.queue);
                showSuccessResult(result.queue);
                addToRecentScans(result.queue, true);
                updateStatus('success', 'Check-in Successful', `Patient ${result.queue.queue_number} verified`);
                
                // Auto-stop scanner after successful scan
                if (scanning) {
                    stopScanning();
                }
            } else {
                console.log('✗ FAILED - Verification failed');
                console.log('Error message:', result.message);
                showErrorResult(result.message);
                addToRecentScans(null, false, result.message);
                updateStatus('danger', 'Verification Failed', result.message);
            }
            
        } catch (error) {
            console.error('=== VERIFICATION ERROR ===');
            console.error('Error Type:', error.name);
            console.error('Error Message:', error.message);
            console.error('Error Stack:', error.stack);
            
            let errorMessage = 'Network error. Please check your connection and try again.';
            if (error.message.includes('CSRF')) {
                errorMessage = 'Security token error. Please refresh the page.';
            } else if (error.message.includes('JSON')) {
                errorMessage = 'Server error. Check console for details.';
            } else if (error.message.includes('Failed to fetch')) {
                errorMessage = 'Cannot connect to server. Is it running?';
            }
            
            alert('VERIFICATION ERROR: ' + errorMessage + '\n\nCheck browser console (F12) for details.');
            showErrorResult(errorMessage);
            updateStatus('danger', 'Error', errorMessage);
        }
        
        console.log('=== VERIFICATION ENDED ===');
    }

    // Show success result in modal
    function showSuccessResult(queue) {
        const modalHeader = document.getElementById('modalHeader');
        const modalBody = document.getElementById('modalBody');
        
        modalHeader.className = 'modal-header bg-success text-white';
        modalHeader.querySelector('.modal-title').innerHTML = '<i class="fas fa-check-circle"></i> Check-in Successful';
        
        modalBody.innerHTML = `
            <div class="text-center mb-3">
                <div class="mb-3">
                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                </div>
                <h4 class="text-success">Patient Verified!</h4>
            </div>
            <hr>
            <div class="row">
                <div class="col-6">
                    <strong>Queue Number:</strong>
                </div>
                <div class="col-6">
                    <span class="badge bg-primary" style="font-size: 1.1rem;">${queue.queue_number}</span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-6">
                    <strong>Patient Name:</strong>
                </div>
                <div class="col-6">
                    ${queue.patient_name}
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-6">
                    <strong>Patient ID:</strong>
                </div>
                <div class="col-6">
                    ${queue.patient_id}
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-6">
                    <strong>Service:</strong>
                </div>
                <div class="col-6">
                    <span class="badge bg-info">${queue.service_type}</span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-6">
                    <strong>Priority:</strong>
                </div>
                <div class="col-6">
                    <span class="badge bg-${queue.priority === 'Priority' ? 'warning' : 'secondary'}">${queue.priority}</span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-6">
                    <strong>Status:</strong>
                </div>
                <div class="col-6">
                    <span class="badge bg-success">${queue.status}</span>
                </div>
            </div>
            ${queue.arrived_at ? `
            <div class="row mt-2">
                <div class="col-6">
                    <strong>Arrived At:</strong>
                </div>
                <div class="col-6">
                    ${queue.arrived_at}
                </div>
            </div>
            ` : ''}
        `;
        
        const modal = new bootstrap.Modal(document.getElementById('scanResultModal'));
        modal.show();
    }

    // Show error result in modal
    function showErrorResult(message) {
        const modalHeader = document.getElementById('modalHeader');
        const modalBody = document.getElementById('modalBody');
        
        modalHeader.className = 'modal-header bg-danger text-white';
        modalHeader.querySelector('.modal-title').innerHTML = '<i class="fas fa-times-circle"></i> Verification Failed';
        
        modalBody.innerHTML = `
            <div class="text-center mb-3">
                <div class="mb-3">
                    <i class="fas fa-times-circle text-danger" style="font-size: 4rem;"></i>
                </div>
                <h5 class="text-danger">Verification Failed</h5>
                <p class="text-muted">${message}</p>
            </div>
        `;
        
        const modal = new bootstrap.Modal(document.getElementById('scanResultModal'));
        modal.show();
    }

    // Update status display
    function updateStatus(type, title, message) {
        statusDiv.className = `alert alert-${type}`;
        statusDiv.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : 'info-circle'}"></i>
            <strong>${title}:</strong> ${message}
        `;
    }

    // Add to recent scans list
    function addToRecentScans(queue, success, errorMsg = null) {
        const recentScansDiv = document.getElementById('recentScans');
        
        // Clear "no scans" message
        if (recentScansDiv.querySelector('.text-muted')) {
            recentScansDiv.innerHTML = '';
        }
        
        const scanItem = document.createElement('div');
        scanItem.className = `scan-item ${success ? '' : 'error'}`;
        
        const now = new Date().toLocaleTimeString();
        
        if (success) {
            scanItem.innerHTML = `
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="queue-number">${queue.queue_number}</div>
                        <div class="patient-name">${queue.patient_name}</div>
                        <div class="time"><i class="fas fa-clock"></i> ${now}</div>
                    </div>
                    <i class="fas fa-check-circle text-success" style="font-size: 1.5rem;"></i>
                </div>
            `;
        } else {
            scanItem.innerHTML = `
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div style="color: #dc3545; font-weight: 600;">Failed Scan</div>
                        <div style="font-size: 0.85rem;">${errorMsg}</div>
                        <div class="time"><i class="fas fa-clock"></i> ${now}</div>
                    </div>
                    <i class="fas fa-times-circle text-danger" style="font-size: 1.5rem;"></i>
                </div>
            `;
        }
        
        recentScansDiv.insertBefore(scanItem, recentScansDiv.firstChild);
        
        // Keep only last 10 scans
        while (recentScansDiv.children.length > 10) {
            recentScansDiv.removeChild(recentScansDiv.lastChild);
        }
    }
    
    // Manual verification form
    document.getElementById('manualVerifyForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        console.log('=== MANUAL FORM SUBMITTED ===');
        
        const qrCodeInput = document.getElementById('manualQrCode');
        const submitBtn = document.getElementById('manualVerifyBtn');
        const qrCode = qrCodeInput.value.trim().toUpperCase();
        
        console.log('Input value:', qrCode);
        console.log('Input length:', qrCode.length);
        
        if (!qrCode) {
            alert('Please enter a queue number or QR code');
            updateStatus('danger', 'Input Required', 'Please enter a queue number or QR code');
            qrCodeInput.focus();
            return;
        }
        
        // Disable button and show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
        updateStatus('info', 'Verifying', `Checking: ${qrCode}`);
        
        console.log('Calling verifyQRCode with:', qrCode);
        
        try {
            await verifyQRCode(qrCode);
            console.log('✓ Verification completed');
            // Clear input on success
            qrCodeInput.value = '';
        } catch (error) {
            console.error('Manual verification error:', error);
        } finally {
            // Re-enable button
            console.log('Re-enabling submit button');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-search"></i> Verify & Check-in';
        }
        
        console.log('=== MANUAL FORM SUBMISSION ENDED ===');
    });

    // Auto-focus manual input and convert to uppercase
    document.getElementById('manualQrCode').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });

    // Add Enter key support
    document.getElementById('manualQrCode').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('manualVerifyForm').dispatchEvent(new Event('submit'));
        }
    });
    
    console.log('✓ Scanner initialized successfully');
    
    // Log page load info
    console.log('=== QR SCANNER PAGE LOADED ===');
    console.log('Current URL:', window.location.href);
    console.log('Current Origin:', window.location.origin);
    console.log('Verify endpoint (Blade):', '{{ route("queue.verify-qr") }}');
    console.log('Verify endpoint (Direct):', '/qr-verify');
    console.log('Verify endpoint (Full):', window.location.origin + '/qr-verify');
    console.log('CSRF token present:', !!document.querySelector('meta[name="csrf-token"]'));
    if (document.querySelector('meta[name="csrf-token"]')) {
        console.log('CSRF token value:', document.querySelector('meta[name="csrf-token"]').content.substring(0, 20) + '...');
    }
    console.log('Ready to scan!');
} // End of initializeScanner function

</script>
@endsection
