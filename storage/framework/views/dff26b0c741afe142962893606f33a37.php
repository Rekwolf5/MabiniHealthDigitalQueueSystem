

<?php $__env->startSection('title', $service->name . ' Queue - Mabini Health Center'); ?>
<?php $__env->startSection('page-title', $service->name . ' Service'); ?>

<?php $__env->startSection('content'); ?>
<div class="dashboard-grid">
    <!-- Service Header -->
    <div class="dashboard-section" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); color: white; border: none;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">
                    <i class="fas fa-hospital"></i> <?php echo e($service->name); ?>

                </h1>
                <p style="opacity: 0.9;"><?php echo e($service->description); ?></p>
            </div>
            <div style="text-align: right;">
                <div style="background: rgba(255,255,255,0.2); padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 0.5rem;">
                    <strong>Capacity:</strong> <?php echo e($stats['current_capacity']); ?>/<?php echo e($stats['max_capacity']); ?> per hour
                </div>
                <div style="background: <?php echo e($stats['availability'] ? 'rgba(16, 185, 129, 0.3)' : 'rgba(239, 68, 68, 0.3)'); ?>; padding: 0.5rem 1rem; border-radius: 4px;">
                    <i class="fas fa-<?php echo e($stats['availability'] ? 'check-circle' : 'exclamation-triangle'); ?>"></i>
                    <?php echo e($stats['availability'] ? 'Available' : 'At Capacity'); ?>

                </div>
            </div>
        </div>
    </div>

    <!-- Service Statistics -->
    <div class="stats-row">
        <div class="stat-card" style="border-left: 4px solid #eab308;">
            <div class="stat-icon" style="background: #eab308;">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo e($stats['waiting']); ?></h3>
                <p>Waiting</p>
            </div>
        </div>
        
        <div class="stat-card" style="border-left: 4px solid #3b82f6;">
            <div class="stat-icon" style="background: #3b82f6;">
                <i class="fas fa-bullhorn"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo e($stats['called']); ?></h3>
                <p>Called</p>
            </div>
        </div>
        
        <div class="stat-card" style="border-left: 4px solid #6366f1;">
            <div class="stat-icon" style="background: #6366f1;">
                <i class="fas fa-user-clock"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo e($stats['in_progress']); ?></h3>
                <p>In Progress</p>
            </div>
        </div>
        
        <div class="stat-card" style="border-left: 4px solid #10b981;">
            <div class="stat-icon" style="background: #10b981;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo e($stats['completed']); ?></h3>
                <p>Completed Today</p>
            </div>
        </div>
        
        <div class="stat-card" style="border-left: 4px solid #f59e0b;">
            <div class="stat-icon" style="background: #f59e0b;">
                <i class="fas fa-stopwatch"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo e($stats['estimated_wait']); ?></h3>
                <p>Est. Wait (min)</p>
            </div>
        </div>
    </div>
    
    <!-- Call Next Patient Button -->
    <div style="margin-bottom: 1.5rem;">
        <form method="POST" action="<?php echo e(route('services.call-next', $service->id)); ?>" style="display: inline;">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.5rem; font-size: 1rem; display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-phone"></i> Call Next Patient
            </button>
        </form>
        <small style="color: #6b7280; margin-left: 1rem;">
            <i class="fas fa-info-circle"></i> System auto-calls next patient when you complete service
        </small>
    </div>

    <!-- Quick Actions -->
    <div class="dashboard-section">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #1f2937; margin: 0;">
                <i class="fas fa-bolt" style="color: #059669;"></i> Quick Actions
            </h2>
        </div>
        
        <div class="quick-actions" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <form action="<?php echo e(route('services.call-next', $service->id)); ?>" method="POST" style="margin: 0;">
                <?php echo csrf_field(); ?>
                <button type="submit" class="action-btn" style="background: #dc2626; color: white; border: none; cursor: pointer; width: 100%;" title="Call the next patient in the waiting queue">
                    <i class="fas fa-bullhorn"></i>
                    <span>Call Next Patient</span>
                </button>
            </form>
            <a href="<?php echo e(route('front-desk.index')); ?>" class="action-btn" style="background: #3b82f6; color: white;" title="View main front desk queue to see unassigned patients">
                <i class="fas fa-users"></i>
                <span>View Front Desk</span>
            </a>
            <button onclick="refreshQueue()" class="action-btn" style="background: #10b981; color: white; border: none; cursor: pointer;" title="Refresh the queue to see latest updates">
                <i class="fas fa-sync-alt"></i>
                <span>Refresh Queue</span>
            </button>
            <a href="<?php echo e(route('services.status', $service->id)); ?>" class="action-btn" style="background: #6366f1; color: white;" title="View detailed service statistics and performance">
                <i class="fas fa-chart-line"></i>
                <span>Service Stats</span>
            </a>
        </div>
    </div>

    <!-- Service Queue -->
    <div class="data-table-container" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
        <table class="data-table" style="min-width: 100%; width: max-content;">
            <thead>
                <tr style="background: #059669;">
                    <th style="color: black !important;"><i class="fas fa-hashtag"></i> Queue #</th>
                    <th style="color: black !important;"><i class="fas fa-user"></i> Patient Name</th>
                    <th style="color: black !important;"><i class="fas fa-exclamation-triangle"></i> Priority</th>
                    <th style="color: black !important;"><i class="fas fa-flag"></i> Status</th>
                    <th style="color: black !important;"><i class="fas fa-clock"></i> Time</th>
                    <th style="color: black !important;"><i class="fas fa-sticky-note"></i> Notes</th>
                    <th style="color: white !important; text-align: center; min-width: 200px; position: sticky; right: 0; background: #059669; z-index: 10;">
                        <i class="fas fa-cog"></i> Actions
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $queues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $queue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <span style="background: #dbeafe; color: #1e40af; padding: 0.5rem; border-radius: 4px; font-weight: 700; font-family: monospace;">
                            <?php echo e($queue->queue_number); ?>

                        </span>
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-user-circle" style="color: #6b7280;"></i>
                            <strong><?php echo e($queue->patient_name); ?></strong>
                        </div>
                    </td>
                    <td>
                        <span class="btn-sm" style="background: 
                            <?php echo e($queue->priority === 'emergency' ? '#fee2e2; color: #991b1b' : 
                               ($queue->priority === 'senior' ? '#f3e8ff; color: #7c3aed' : 
                               ($queue->priority === 'pwd' ? '#dbeafe; color: #1e40af' : 
                               ($queue->priority === 'pregnant' ? '#fce7f3; color: #be185d' : '#f3f4f6; color: #374151')))); ?>">
                            <i class="fas fa-<?php echo e($queue->priority === 'emergency' ? 'exclamation-triangle' : 
                                              ($queue->priority === 'senior' ? 'user-friends' : 
                                              ($queue->priority === 'pwd' ? 'wheelchair' : 
                                              ($queue->priority === 'pregnant' ? 'baby' : 'user')))); ?>"></i>
                            <?php echo e(ucfirst($queue->priority)); ?>

                        </span>
                    </td>
                    <td>
                        <span class="btn-sm" style="background: 
                            <?php echo e($queue->status === 'waiting' ? '#fef3c7; color: #92400e' : 
                               ($queue->status === 'called' ? '#dbeafe; color: #1e40af' : 
                               ($queue->status === 'in_progress' ? '#f3e8ff; color: #7c3aed' : 
                               ($queue->status === 'completed' ? '#dcfce7; color: #166534' : '#fee2e2; color: #991b1b')))); ?>">
                            <i class="fas fa-<?php echo e($queue->status === 'waiting' ? 'clock' : 
                                              ($queue->status === 'called' ? 'phone' : 
                                              ($queue->status === 'in_progress' ? 'user-clock' : 
                                              ($queue->status === 'completed' ? 'check-circle' : 'times-circle')))); ?>"></i>
                            <?php echo e(ucfirst(str_replace('_', ' ', $queue->status))); ?>

                        </span>
                    </td>
                    <td>
                        <div style="font-size: 0.875rem;">
                            <div><strong>Arrived:</strong> <?php echo e($queue->arrived_at->format('h:i A')); ?></div>
                            <?php if($queue->called_at): ?>
                                <div><strong>Called:</strong> <?php echo e($queue->called_at->format('h:i A')); ?></div>
                            <?php endif; ?>
                            <?php if($queue->completed_at): ?>
                                <div><strong>Done:</strong> <?php echo e($queue->completed_at->format('h:i A')); ?></div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td style="max-width: 200px;">
                        <small><?php echo e($queue->notes ? Str::limit($queue->notes, 50) : 'No notes'); ?></small>
                    </td>
                    <td style="position: sticky; right: 0; background: white; box-shadow: -2px 0 4px rgba(0,0,0,0.05); z-index: 5;">
                        <div class="action-buttons" style="justify-content: center; display: flex; flex-wrap: nowrap; gap: 0.25rem; padding: 0.25rem;">
                            <?php if($queue->status === 'called'): ?>
                                <button onclick="announceQueue('<?php echo e($queue->queue_number); ?>', '<?php echo e($queue->patient_name); ?>')" 
                                        class="btn btn-sm" style="background: #dc2626; color: white; min-width: 35px; padding: 0.375rem;" title="Announce Queue Number">
                                    <i class="fas fa-volume-up"></i>
                                </button>
                                
                                <!-- Skip button for when patient is not present -->
                                <form method="POST" action="<?php echo e(route('services.skip', $queue->id)); ?>" style="display: inline; margin: 0;">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-sm" style="background: #f59e0b; color: white; min-width: 35px; padding: 0.375rem;" 
                                            title="Patient Not Present - Skip & Call Next" 
                                            onclick="return confirm('Patient <?php echo e($queue->patient_name); ?> is not present?\n\nThis will:\n• Move patient to end of queue\n• Call next patient automatically\n• Patient can be called again when they return')">
                                        <i class="fas fa-forward"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <?php if($queue->status === 'waiting' || $queue->status === 'called'): ?>
                                <!-- Take Vitals Button (for services that need it like GP, OB-Gyn) -->
                                <?php if($queue->workflow_stage === 'registration' && in_array(strtolower($service->name), ['general practitioner', 'general practice', 'ob-gyn', 'maternal & child health'])): ?>
                                    <form method="POST" action="<?php echo e(route('services.vitals.call', $queue->id)); ?>" style="display: inline; margin: 0;">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm" style="background: #8b5cf6; color: white; min-width: 35px; padding: 0.375rem;" title="Call for Vitals">
                                            <i class="fas fa-heartbeat"></i> Vitals
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <!-- Vitals Complete Button (when patient at vitals station) -->
                                <?php if($queue->workflow_stage === 'vitals' && in_array(strtolower($service->name), ['general practitioner', 'general practice', 'ob-gyn', 'maternal & child health'])): ?>
                                    <form method="POST" action="<?php echo e(route('services.vitals.complete', $queue->id)); ?>" style="display: inline; margin: 0;">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm" style="background: #10b981; color: white; min-width: 35px; padding: 0.375rem;" title="Mark Vitals Complete">
                                            <i class="fas fa-check-circle"></i> Done
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <!-- Start Service Button (only for consultation stage patients) -->
                                <?php if($queue->workflow_stage === 'consultation' && $queue->status === 'called'): ?>
                                    <form method="POST" action="<?php echo e(route('services.start', $queue->id)); ?>" style="display: inline; margin: 0;">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-info" title="Start Service" style="min-width: 35px; padding: 0.375rem;">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php if($queue->status === 'in_progress'): ?>
                                <form method="POST" action="<?php echo e(route('services.complete', $queue->id)); ?>" style="display: inline; margin: 0;">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-sm btn-success" title="Complete Service" style="min-width: 35px; padding: 0.375rem;">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <?php if(in_array($queue->status, ['waiting', 'called', 'in_progress'])): ?>
                                <button onclick="showTransferModal(<?php echo e($queue->id); ?>, '<?php echo e($queue->patient_name); ?>')" 
                                        class="btn btn-sm btn-warning" title="Transfer Patient" style="min-width: 35px; padding: 0.375rem;">
                                    <i class="fas fa-exchange-alt"></i>
                                </button>
                            <?php endif; ?>
                            
                            <!-- No Show button for patients who never returned -->
                            <?php if(in_array($queue->status, ['waiting', 'called'])): ?>
                                <form method="POST" action="<?php echo e(route('services.no-show', $queue->id)); ?>" style="display: inline; margin: 0;">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-sm" style="background: #6b7280; color: white; min-width: 35px; padding: 0.375rem;" 
                                            title="Mark as No Show - Patient Never Returned" 
                                            onclick="return confirm('Mark <?php echo e($queue->patient_name); ?> as NO SHOW?\n\nThis will:\n• Remove patient from queue\n• Archive as no-show\n• Cannot be undone')">
                                        <i class="fas fa-user-times"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <button onclick="showNotesModal(<?php echo e($queue->id); ?>, '<?php echo e(addslashes($queue->notes)); ?>')" 
                                    class="btn btn-sm btn-secondary" title="View/Edit Notes" style="min-width: 35px; padding: 0.375rem;">
                                <i class="fas fa-sticky-note"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 3rem;">
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                            <div style="background: #f3f4f6; border-radius: 50%; padding: 2rem; width: 120px; height: 120px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-hospital" style="font-size: 3rem; color: #d1d5db;"></i>
                            </div>
                            <div>
                                <h3 style="font-size: 1.25rem; font-weight: 700; color: #6b7280; margin-bottom: 0.5rem;">No Patients in <?php echo e($service->name); ?> Queue</h3>
                                <p style="color: #9ca3af;">Patients will appear here when assigned from the front desk</p>
                                <a href="<?php echo e(route('front-desk.index')); ?>" class="btn btn-primary" style="margin-top: 1rem;">
                                    <i class="fas fa-clipboard-list"></i> Go to Front Desk
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if($queues->hasPages()): ?>
    <div style="margin-top: 1.5rem; display: flex; justify-content: center;">
        <?php echo e($queues->links()); ?>

    </div>
    <?php endif; ?>
</div>

<!-- Transfer Patient Modal -->
<div id="transferModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
    <div class="modal-content" style="background-color: #fefefe; margin: 5% auto; padding: 20px; border: none; border-radius: 8px; width: 90%; max-width: 500px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; color: #1f2937;"><i class="fas fa-exchange-alt"></i> Transfer Patient</h3>
            <button onclick="hideTransferModal()" style="background: none; border: none; font-size: 1.5rem; color: #6b7280; cursor: pointer;">&times;</button>
        </div>
        
        <form method="POST" id="transferForm">
            <?php echo csrf_field(); ?>
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Transfer to Service:</label>
                <select id="new_service_id" name="new_service_id" required style="width: 100%;">
                    <option value="">Select Service...</option>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Transfer Notes:</label>
                <textarea id="transfer_notes" name="transfer_notes" rows="3" style="width: 100%;" placeholder="Reason for transfer..."></textarea>
            </div>
            
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="hideTransferModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-exchange-alt"></i> Transfer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Voice announcement function
function announceQueue(queueNumber, patientName) {
    // Check if browser supports speech synthesis
    if ('speechSynthesis' in window) {
        // Cancel any ongoing speech
        window.speechSynthesis.cancel();
        
        // Get service name from the page
        const serviceName = '<?php echo e($service->name); ?>';
        
        // Create announcement text
        const announcement = `Queue number ${queueNumber}. ${patientName}. Please proceed to ${serviceName}.`;
        
        // Function to speak with voice selection
        function speakAnnouncement() {
            // Create speech utterance
            const utterance = new SpeechSynthesisUtterance(announcement);
            utterance.lang = 'en-US';
            utterance.rate = 0.9; // Slightly slower for clarity
            utterance.pitch = 1.1; // Slightly higher pitch for female sound
            utterance.volume = 1;
            
            // Get available voices
            const voices = window.speechSynthesis.getVoices();
            
            // Try to select a female voice (prioritize Microsoft Zira, Google Female voices)
            const femaleVoice = voices.find(voice => 
                voice.name.toLowerCase().includes('zira') ||
                voice.name.toLowerCase().includes('female') ||
                voice.name.toLowerCase().includes('samantha') ||
                voice.name.toLowerCase().includes('karen') ||
                (voice.name.toLowerCase().includes('google') && voice.name.toLowerCase().includes('us'))
            );
            
            if (femaleVoice) {
                utterance.voice = femaleVoice;
                console.log('Using voice:', femaleVoice.name);
            } else {
                console.log('No female voice found, using default. Available voices:', voices.map(v => v.name));
            }
            
            // Speak the announcement
            window.speechSynthesis.speak(utterance);
            
            // Repeat announcement after 3 seconds
            setTimeout(() => {
                const utterance2 = new SpeechSynthesisUtterance(announcement);
                utterance2.lang = 'en-US';
                utterance2.rate = 0.9;
                utterance2.pitch = 1.1;
                utterance2.volume = 1;
                if (femaleVoice) {
                    utterance2.voice = femaleVoice;
                }
                window.speechSynthesis.speak(utterance2);
            }, 3000);
        }
        
        // Wait for voices to load if necessary
        const voices = window.speechSynthesis.getVoices();
        if (voices.length === 0) {
            // Voices not loaded yet, wait for them
            window.speechSynthesis.onvoiceschanged = function() {
                speakAnnouncement();
                window.speechSynthesis.onvoiceschanged = null; // Remove listener after first use
            };
        } else {
            // Voices already loaded
            speakAnnouncement();
        }
    } else {
        console.warn('Speech synthesis not supported in this browser');
    }
}

// Check for newly called patients on page load
document.addEventListener('DOMContentLoaded', function() {
    // Get success message if patient was just called
    const successMessage = document.querySelector('.alert-success');
    if (successMessage) {
        const messageText = successMessage.textContent;
        
        // Check for either manual call or auto-call after complete/skip/no-show
        if (messageText.includes('Called next patient') || messageText.includes('Next patient called')) {
            // Try multiple message formats:
            // Format 1: "Called next patient: NAME (Queue #XXX)"
            // Format 2: "Next patient called: NAME (Queue #XXX)"
            const match1 = messageText.match(/patient called:\s*([^(]+)\s*\(Queue #(\S+)\)/);
            const match2 = messageText.match(/patient:\s*([^(]+)\s*\(Queue #(\S+)\)/);
            
            let patientName, queueNumber;
            if (match1) {
                patientName = match1[1].trim();
                queueNumber = match1[2];
            } else if (match2) {
                patientName = match2[1].trim();
                queueNumber = match2[2];
            }
            
            if (patientName && queueNumber) {
                announceQueue(queueNumber, patientName);
            }
        }
    }
});

// Auto-refresh every 30 seconds
setInterval(function() {
    if (!document.getElementById('transferModal').style.display.includes('block')) {
        location.reload();
    }
}, 30000);

// Refresh queue function
function refreshQueue() {
    location.reload();
}

// Transfer modal functions
function showTransferModal(queueId, patientName) {
    document.getElementById('transferForm').action = `/services/transfer/${queueId || ''}`;
    loadAvailableServices();
    document.getElementById('transferModal').style.display = 'block';
}

function hideTransferModal() {
    document.getElementById('transferModal').style.display = 'none';
    document.getElementById('transferForm').reset();
}

function loadAvailableServices() {
    fetch(`/services/available/<?php echo e($service->id); ?>`)
        .then(response => response.json())
        .then(services => {
            const select = document.getElementById('new_service_id');
            select.innerHTML = '<option value="">Select Service...</option>';
            
            services.forEach(service => {
                const option = document.createElement('option');
                option.value = service.id;
                option.textContent = `${service.name} (${service.current_capacity}/${service.max_capacity})`;
                option.disabled = !service.available;
                if (!service.available) {
                    option.textContent += ' - At Capacity';
                }
                select.appendChild(option);
            });
        })
        .catch(error => console.error('Error loading services:', error));
}

// Close modal when clicking outside
window.onclick = function(event) {
    const transferModal = document.getElementById('transferModal');
    const vitalsModal = document.getElementById('vitalsModal');
    if (event.target === transferModal) {
        hideTransferModal();
    }
    if (event.target === vitalsModal) {
        hideVitalsModal();
    }
}

// Vitals Modal Functions
function showVitalsModal(queueId, patientName) {
    document.getElementById('vitals_queue_id').value = queueId;
    document.getElementById('vitals_patient_name').textContent = patientName;
    document.getElementById('vitalsModal').style.display = 'block';
}

function hideVitalsModal() {
    document.getElementById('vitalsModal').style.display = 'none';
    document.getElementById('vitalsForm').reset();
}
</script>

<!-- Vitals Modal -->
<div id="vitalsModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
    <div class="modal-content" style="background-color: #fefefe; margin: 2% auto; padding: 20px; border: none; border-radius: 8px; width: 90%; max-width: 600px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; color: #1f2937;"><i class="fas fa-heartbeat" style="color: #8b5cf6;"></i> Take Vital Signs</h3>
            <button onclick="hideVitalsModal()" style="background: none; border: none; font-size: 1.5rem; color: #6b7280; cursor: pointer;">&times;</button>
        </div>
        
        <p style="color: #6b7280; margin-bottom: 1rem;">Patient: <strong id="vitals_patient_name"></strong></p>
        
        <form method="POST" id="vitalsForm" action="<?php echo e(route('services.vitals.store')); ?>">
            <?php echo csrf_field(); ?>
            <input type="hidden" id="vitals_queue_id" name="queue_id">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Blood Pressure</label>
                    <input type="text" name="blood_pressure" placeholder="120/80" style="width: 100%;" pattern="[0-9]{2,3}/[0-9]{2,3}">
                    <small style="color: #6b7280;">Format: 120/80</small>
                </div>
                
                <div class="form-group">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Temperature (°C)</label>
                    <input type="number" name="temperature" placeholder="36.5" step="0.1" min="30" max="45" style="width: 100%;">
                </div>
                
                <div class="form-group">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Pulse Rate (bpm)</label>
                    <input type="number" name="pulse_rate" placeholder="72" min="40" max="200" style="width: 100%;">
                </div>
                
                <div class="form-group">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Respiratory Rate</label>
                    <input type="number" name="respiratory_rate" placeholder="16" min="8" max="40" style="width: 100%;">
                </div>
                
                <div class="form-group">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Weight (kg)</label>
                    <input type="number" name="weight" placeholder="65" step="0.1" min="1" max="300" style="width: 100%;">
                </div>
                
                <div class="form-group">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Height (cm)</label>
                    <input type="number" name="height" placeholder="170" step="0.1" min="50" max="250" style="width: 100%;">
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Additional Notes</label>
                <textarea name="vitals_notes" rows="2" style="width: 100%;" placeholder="Any observations or concerns..."></textarea>
            </div>
            
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="hideVitalsModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary" style="background: #8b5cf6;">
                    <i class="fas fa-save"></i> Save Vitals
                </button>
            </div>
        </form>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ramir\Desktop\Health-Center-Queue-System-main\resources\views/services/dashboard.blade.php ENDPATH**/ ?>