<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Queue;
use App\Models\Patient;
use App\Models\QueueCounter;
use App\Models\User;
use Illuminate\Support\Str;
use App\Notifications\QueueStatusNotification;
use App\Notifications\DoctorAssignedNotification;

class QueueController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters
        $doctorId = $request->get('doctor_id');
        $showUnassigned = $request->get('unassigned') === '1';

        // Get all doctors for filter dropdown
        $doctors = User::where('role', 'doctor')->orderBy('name')->get();

        // SQLite-compatible ordering - get all queue entries for today
        $query = Queue::with(['patient', 'assignedDoctor'])
            ->whereDate('created_at', today());

        // Apply filters
        if ($showUnassigned) {
            $query->whereNull('assigned_doctor_id');
        } elseif ($doctorId) {
            $query->where('assigned_doctor_id', $doctorId);
        }

        $queue = $query->orderBy('arrived_at', 'asc')
            ->get()
            ->sortBy(function ($item) {
                // Custom priority sorting: Emergency = 1, Urgent = 2, Normal = 3
                $priorities = ['Emergency' => 1, 'Urgent' => 2, 'Normal' => 3];
                return $priorities[$item->priority] ?? 4;
            })
            ->values(); // Reset array keys

        return view('queue.index', compact('queue', 'doctors', 'doctorId', 'showUnassigned'));
    }

    public function add()
    {
        $patients = Patient::orderBy('first_name')->get();
        return view('queue.add', compact('patients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_name' => 'required|string|max:255',
            'priority' => 'required|in:Priority,Regular',
            'patient_category' => 'nullable|in:PWD,Pregnant,Senior,Regular', // For record keeping
            'service_type' => 'required|in:Consultation and Treatment,Circumcision,Incision and Drainage,Laboratory Services,Prenatal Care,Normal Delivery,Post-natal Care,Newborn Screening,Family Planning,Immunization Program,Dental Services,Dengue Program,Non-Communicable Diseases,Sanitation Inspection',
            'notes' => 'nullable|string',
            'assigned_doctor_id' => 'nullable|exists:users,id',
        ]);

        try {
            // Flexible patient name matching - normalize input
            $inputName = trim(preg_replace('/\s+/', ' ', strtolower($validated['patient_name'])));
            
            // Remove periods and extra spaces for better matching
            $inputNameNormalized = str_replace('.', '', $inputName);
            
            $patient = Patient::all()->first(function ($p) use ($inputName, $inputNameNormalized) {
                // Try matching with just first + last name
                $fullName = trim(preg_replace('/\s+/', ' ', strtolower($p->first_name . ' ' . $p->last_name)));
                
                // Also try with middle name included
                $fullNameWithMiddle = $p->middle_name 
                    ? trim(preg_replace('/\s+/', ' ', strtolower($p->first_name . ' ' . $p->middle_name . ' ' . $p->last_name)))
                    : $fullName;
                
                // Normalize by removing periods for comparison
                $fullNameNormalized = str_replace('.', '', $fullName);
                $fullNameWithMiddleNormalized = str_replace('.', '', $fullNameWithMiddle);
                
                // Check if input matches any variation
                return strpos($fullName, $inputName) !== false 
                    || strpos($fullNameWithMiddle, $inputName) !== false
                    || strpos($fullNameNormalized, $inputNameNormalized) !== false
                    || strpos($fullNameWithMiddleNormalized, $inputNameNormalized) !== false;
            });

            if (!$patient) {
                return back()->withInput()->with('error', 'Patient not found. Please register the patient first or check the spelling.');
            }

            // Generate queue number with service prefix
            $priority = $validated['priority'];
            $serviceType = $validated['service_type'];
            
            // Get service prefix (e.g., DENT, LAB, PRE)
            $servicePrefixes = config('services_config.service_prefixes');
            $servicePrefix = $servicePrefixes[$serviceType] ?? 'GEN';
            
            // Determine priority prefix
            $priorityPrefix = '';
            
            // Priority Lane (PWD, Pregnant, Senior - all same priority)
            if ($priority === 'Priority') {
                $priorityPrefix = 'P'; // Priority Lane
                $todayCount = QueueCounter::getNextNumber($serviceType, 'P');
            } 
            // Regular Lane
            else {
                $priorityPrefix = 'R'; // Regular Lane
                $todayCount = QueueCounter::getNextNumber($serviceType, 'R');
            }
            
            // Generate queue number: SERVICE-PRIORITY###
            // Examples: DENT-P001, LAB-R003, PRE-P012
            $queueNumber = $servicePrefix . '-' . $priorityPrefix . str_pad($todayCount, 3, '0', STR_PAD_LEFT);

            // Generate QR code data and verification token
            $verificationToken = Str::random(32);
            $qrCodeData = hash('sha256', $queueNumber . '-' . $verificationToken . '-' . now()->timestamp);

            $queue = Queue::create([
                'patient_id' => $patient->id,
                'queue_number' => $queueNumber,
                'qr_code' => $qrCodeData,
                'verification_token' => $verificationToken,
                'priority' => $validated['priority'], // Priority or Regular
                'patient_category' => $validated['patient_category'] ?? 'Regular', // PWD/Pregnant/Senior/Regular for records
                'service_type' => $validated['service_type'],
                'notes' => $validated['notes'],
                'status' => 'Waiting',
                'arrived_at' => now(),
                'assigned_doctor_id' => $validated['assigned_doctor_id'] ?? null,
            ]);

            // Send notification to assigned doctor if one was selected
            if (!empty($validated['assigned_doctor_id'])) {
                $doctor = User::find($validated['assigned_doctor_id']);
                if ($doctor) {
                    $doctor->notify(new DoctorAssignedNotification($queue));
                }
            }

            // Redirect to print ticket page
            return redirect()->route('queue.ticket', $queue->id)->with('success', 'Patient added to queue successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error adding to queue: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $queue = Queue::findOrFail($id);
            $newStatus = $request->status;
            
            // Validate the transition using the model's state machine
            if (!$queue->canTransitionTo($newStatus)) {
                $errorMessage = "Invalid status transition from '{$queue->status}' to '{$newStatus}'.";
                $allowedStatuses = $queue->getAllowedNextStatuses();
                
                // Check if AJAX request
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                        'allowed_statuses' => $allowedStatuses
                    ], 422);
                }
                
                return back()->with('error', $errorMessage . ' Allowed: ' . implode(', ', $allowedStatuses));
            }

            // Perform the transition (model boot method will handle timestamps)
            $queue->status = $newStatus;
            $queue->save();

            // Send notification to patient if they have an account
            if ($queue->patient && $queue->patient->patientAccount) {
                $queue->patient->patientAccount->notify(
                    new QueueStatusNotification($queue, $newStatus)
                );
            }

            // Check if AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Queue status updated successfully',
                    'queue' => $queue
                ]);
            }

            // Regular form submission - redirect back with success message
            return redirect()->route('queue.index')->with('success', "Queue #{$queue->queue_number} status updated to {$newStatus}!");

        } catch (\InvalidArgumentException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }
            
            return back()->with('error', $e->getMessage());
            
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update queue status: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Failed to update queue status: ' . $e->getMessage());
        }
    }

    public function display()
    {
        // Get all active services with their queues
        $services = \App\Models\Service::where('is_active', true)
            ->with(['frontDeskQueues' => function($query) {
                $query->whereDate('arrived_at', today())
                      ->whereIn('status', ['waiting', 'called', 'in_progress'])
                      ->orderBy('priority', 'desc')
                      ->orderBy('arrived_at', 'asc');
            }])
            ->get();

        return view('queue.display', compact('services'));
    }

    // Recall Skipped Patients
    public function recall($id)
    {
        $queue = Queue::findOrFail($id);

        if (in_array($queue->status, ['Skipped', 'No Show'])) {
            $queue->update(['status' => 'Waiting']);
            return back()->with('info', 'Patient #' . $queue->queue_number . ' has been recalled to the queue.');
        }

        return back()->with('warning', 'Only skipped or no-show patients can be recalled.');
    }

    // Patient Queue Request Methods
    public function showPatientRequestForm()
    {
        return view('patient.queue.request');
    }

    public function submitPatientQueueRequest(Request $request)
    {
        $validated = $request->validate([
            'requested_date' => 'required|date|after_or_equal:today|before_or_equal:' . today()->addDays(7)->format('Y-m-d'),
            'service_type' => 'required|in:Consultation and Treatment,Circumcision,Incision and Drainage,Laboratory Services,Prenatal Care,Normal Delivery,Post-natal Care,Newborn Screening,Family Planning,Immunization Program,Dental Services,Dengue Program,Non-Communicable Diseases,Sanitation Inspection',
            'priority' => 'required|in:PWD,Pregnant,Senior,Regular',
            'pwd_id' => 'nullable|string|max:50',
            'senior_id' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $patientAccount = auth('patient')->user();
            $patientId = $patientAccount->patient_id;

            // Check for existing pending or approved request
            $existingRequest = Queue::where('patient_id', $patientId)
                ->whereIn('approval_status', ['pending', 'approved'])
                ->whereDate('requested_date', '>=', today())
                ->exists();

            if ($existingRequest) {
                return back()->withInput()->with('error', 'You already have a pending queue request. Please wait for approval or cancel your existing request.');
            }

            // Generate queue number with service prefix
            $priority = $validated['priority'];
            $serviceType = $validated['service_type'];
            
            // Get service prefix (e.g., DENT, LAB, PRE)
            $servicePrefixes = config('services_config.service_prefixes');
            $servicePrefix = $servicePrefixes[$serviceType] ?? 'GEN';
            
            // Determine priority prefix
            $priorityPrefix = '';
            
            // Priority Lane (PWD, Pregnant, Senior)
            if (in_array($priority, ['PWD', 'Pregnant', 'Senior'])) {
                $priorityPrefix = 'P'; // Priority Lane
                $todayCount = QueueCounter::getNextNumber($serviceType, 'P');
            } 
            // Regular Lane
            else {
                $priorityPrefix = 'R'; // Regular
                $todayCount = QueueCounter::getNextNumber($serviceType, 'R');
            }
            
            // Generate queue number: SERVICE-PRIORITY###
            $queueNumber = $servicePrefix . '-' . $priorityPrefix . str_pad($todayCount, 3, '0', STR_PAD_LEFT);

            // Generate QR code data and verification token
            $verificationToken = Str::random(32);
            $qrCodeData = hash('sha256', $queueNumber . '-' . $verificationToken . '-' . now()->timestamp);

            // Determine actual priority and patient category for database
            $actualPriority = in_array($priority, ['PWD', 'Pregnant', 'Senior']) ? 'Priority' : 'Regular';
            $patientCategory = $priority; // PWD, Pregnant, Senior, or Regular

            $queue = Queue::create([
                'queue_number' => $queueNumber,
                'qr_code' => $qrCodeData,
                'verification_token' => $verificationToken,
                'patient_id' => $patientId,
                'service_type' => $validated['service_type'],
                'priority' => $actualPriority, // Priority or Regular (for database)
                'patient_category' => $patientCategory, // PWD, Pregnant, Senior, or Regular (for record keeping)
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
                'requested_date' => $validated['requested_date'],
                'requested_at' => now(),
                'approval_status' => 'pending',
                'pwd_id' => $validated['pwd_id'] ?? null,
                'senior_id' => $validated['senior_id'] ?? null,
            ]);

            return redirect()->route('patient.ticket', $queue->id)->with('success', 'Your queue request has been submitted for ' . \Carbon\Carbon::parse($validated['requested_date'])->format('F d, Y') . '! You will be notified once approved.');
            
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Queue submission failed', [
                'patient_id' => $patientAccount->patient_id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withInput()->with('error', 'Unable to submit queue request. Please try again or contact staff for assistance.');
        }
    }

    // Staff - View Pending Patient Requests
    public function pendingRequests(Request $request)
    {
        // Check if user is staff or admin
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['staff', 'admin'])) {
            abort(403, 'Unauthorized access');
        }

        // Get date filter
        $dateFilter = $request->get('date', 'all');
        
        $query = Queue::with(['patient.patientAccount', 'reviewer'])
            ->where(function($q) {
                $q->whereIn('approval_status', ['pending', 'approved', 'rejected', 'expired'])
                  ->orWhere(function($q2) {
                      $q2->where('status', 'Pending')
                         ->whereNull('approval_status');
                  });
            });

        // Apply date filtering based on requested_date
        switch ($dateFilter) {
            case 'today':
                $query->whereDate('requested_date', today());
                break;
            case 'tomorrow':
                $query->whereDate('requested_date', today()->addDay());
                break;
            case 'this-week':
                $query->whereBetween('requested_date', [today(), today()->addWeek()]);
                break;
            default:
                // Show all, no date filter
                break;
        }

        $requests = $query->orderByRaw("
                CASE approval_status
                    WHEN 'pending' THEN 1
                    WHEN 'approved' THEN 2
                    WHEN 'rejected' THEN 3
                    WHEN 'expired' THEN 4
                END
            ")
            ->orderBy('requested_date', 'asc')
            ->orderBy('requested_at', 'asc')
            ->get();

        return view('staff.queue.requests', compact('requests'));
    }

    // Staff - Approve Patient Request
    public function approveRequest(Request $request, $id)
    {
        $queue = Queue::findOrFail($id);
        
        // Update approval status
        $queue->approval_status = 'approved';
        $queue->reviewed_at = now();
        $queue->reviewed_by = auth()->id();
        $queue->staff_notes = $request->input('staff_notes');
        
        // Change status from Pending to Waiting so patient appears in queue
        if (strtolower($queue->status) === 'pending') {
            $queue->status = 'Waiting';
            $queue->arrived_at = $queue->arrived_at ?? now();
        }
        
        $queue->save();

        // Send notification to patient
        if ($queue->patient && $queue->patient->patientAccount) {
            $queue->patient->patientAccount->notify(
                new QueueStatusNotification($queue, 'approved')
            );
        }

        $dateStr = $queue->requested_date ? $queue->requested_date->format('F d, Y') : 'today';
        return back()->with('success', "Request approved for {$dateStr}! Patient can now download their QR code.");
    }

    // Show printable ticket with QR code
    public function showTicket($id)
    {
        $queue = Queue::with('patient')->findOrFail($id);
        return view('queue.ticket', compact('queue'));
    }

    // QR Scanner page for staff
    public function showScanner()
    {
        return view('staff.qr-scanner');
    }

    // Verify QR code and Check-in Patient
    public function verifyQr(Request $request)
    {
        try {
            $request->validate([
                'qr_code' => 'required|string',
            ]);

            $input = trim($request->qr_code);

            // Try to find by QR code hash first
            $queue = Queue::with('patient')
                ->where('qr_code', $input)
                ->whereDate('created_at', today())
                ->first();

            // If not found, try to find by queue number
            if (!$queue) {
                $queue = Queue::with('patient')
                    ->where('queue_number', $input)
                    ->whereDate('created_at', today())
                    ->first();
            }

            if (!$queue) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR code or queue number not found for today.'
                ], 404);
            }

            // Check if patient exists
            if (!$queue->patient) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient information not found for this queue entry.'
                ], 404);
            }

            // Check if queue is already completed or cancelled
            if (in_array(strtolower($queue->status), ['completed', 'cancelled', 'no show', 'unattended'])) {
                return response()->json([
                    'success' => false,
                    'message' => "This queue is already {$queue->status}. Cannot check-in."
                ], 400);
            }

            // Update queue status to Waiting if it's Pending
            $statusChanged = false;
            if (strtolower($queue->status) === 'pending') {
                try {
                    $queue->status = 'Waiting';
                    if (!$queue->arrived_at) {
                        $queue->arrived_at = now();
                    }
                    $queue->save();
                    $statusChanged = true;

                    // Send notification to patient
                    if ($queue->patient->patientAccount) {
                        try {
                            $queue->patient->patientAccount->notify(
                                new QueueStatusNotification($queue, 'approved')
                            );
                        } catch (\Exception $e) {
                            \Log::warning('Notification failed during QR check-in', ['error' => $e->getMessage()]);
                        }
                    }

                    // Log activity (wrapped in try-catch to prevent blocking)
                    try {
                        \App\Models\ActivityLog::log('queue_checkin', "Patient checked in via QR scan: {$queue->queue_number}", [
                            'queue_id' => $queue->id,
                            'patient_id' => $queue->patient_id,
                            'method' => 'QR_SCAN'
                        ]);
                    } catch (\Exception $e) {
                        // Log error but don't fail the check-in
                        \Log::warning('ActivityLog failed during QR check-in', ['error' => $e->getMessage()]);
                    }
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to update queue status: ' . $e->getMessage()
                    ], 500);
                }
            }

            // Determine appropriate message
            $message = $statusChanged 
                ? 'Patient verified and checked in successfully' 
                : 'Patient already checked in - Queue is ' . $queue->status;

            return response()->json([
                'success' => true,
                'message' => $message,
                'queue' => [
                    'id' => $queue->id,
                    'queue_number' => $queue->queue_number,
                    'patient_name' => $queue->patient->full_name ?? 'Walk-in Patient',
                    'patient_id' => $queue->patient->patient_id ?? 'N/A',
                    'service_type' => $queue->service_type,
                    'priority' => $queue->priority,
                    'status' => $queue->status,
                    'arrived_at' => $queue->arrived_at?->format('h:i A'),
                    'contact' => $queue->patient->contact ?? 'N/A',
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
        } catch (\Exception $e) {
            \Log::error('QR Verification Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during verification. Please try again.'
            ], 500);
        }
    }

    // QR Scanner Interface
    public function scanner()
    {
        return view('queue.scanner');
    }

    // Staff - Reject Patient Request
    public function rejectRequest(Request $request, $id)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
            'staff_notes' => 'nullable|string|max:1000'
        ]);
        
        $queue = Queue::findOrFail($id);
        
        // Update approval and status
        $queue->approval_status = 'rejected';
        $queue->status = 'Cancelled';
        $queue->reviewed_at = now();
        $queue->reviewed_by = auth()->id();
        $queue->rejection_reason = $validated['rejection_reason'];
        $queue->staff_notes = $validated['staff_notes'];
        
        $queue->save();

        // Send notification to patient with rejection reason
        if ($queue->patient && $queue->patient->patientAccount) {
            $queue->patient->patientAccount->notify(
                new QueueStatusNotification($queue, 'rejected')
            );
        }

        return back()->with('success', 'Request rejected and patient has been notified.');
    }

    // Staff - Assign Doctor to Patient
    public function assignDoctor(Request $request, $id)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id'
        ]);

        $queue = Queue::findOrFail($id);
        $oldDoctorId = $queue->assigned_doctor_id;
        
        $queue->assigned_doctor_id = $request->doctor_id;
        $queue->doctor_accepted_at = null; // Reset acceptance when reassigning
        $queue->save();

        // Send notification to the assigned doctor
        $doctor = User::find($request->doctor_id);
        if ($doctor) {
            $doctor->notify(new DoctorAssignedNotification($queue));
        }

        $action = $oldDoctorId ? 'reassigned' : 'assigned';
        return back()->with('success', "Patient successfully {$action} to Dr. {$doctor->name}");
    }

    /**
     * Show priority queue request form for cut-off patients
     */
    public function showPriorityRequestForm(Request $request)
    {
        $fromCutoffQueueId = $request->get('from_cutoff');
        
        // Verify this is a valid cut-off queue
        $cutoffQueue = null;
        if ($fromCutoffQueueId) {
            $cutoffQueue = Queue::where('id', $fromCutoffQueueId)
                ->where('patient_id', auth('patient')->user()->patient_id)
                ->where('status', 'Unattended')
                ->whereDate('created_at', '>=', today()->subDays(2)) // Only last 2 days
                ->first();
        }

        // Check if patient already used their priority today
        $existingPriority = Queue::where('patient_id', auth('patient')->user()->patient_id)
            ->where('is_cutoff_priority', true)
            ->whereDate('created_at', today())
            ->exists();

        if ($existingPriority) {
            return redirect()->route('patient.queue.request')
                ->with('warning', 'You have already used your priority queue for today. Please request a regular queue.');
        }

        return view('patient.queue.request-priority', compact('cutoffQueue'));
    }

    /**
     * Submit priority queue request for cut-off patients
     */
    public function submitPriorityQueueRequest(Request $request)
    {
        $validated = $request->validate([
            'service_type' => 'required|in:Consultation and Treatment,Circumcision,Incision and Drainage,Laboratory Services,Prenatal Care,Normal Delivery,Post-natal Care,Newborn Screening,Family Planning,Immunization Program,Dental Services,Dengue Program,Non-Communicable Diseases,Sanitation Inspection',
            'notes' => 'nullable|string|max:500',
            'from_cutoff_queue' => 'nullable|exists:queue,id',
        ]);

        try {
            $patientAccount = auth('patient')->user();
            $patientId = $patientAccount->patient_id;

            // Check if already used priority today
            $existingPriority = Queue::where('patient_id', $patientId)
                ->where('is_cutoff_priority', true)
                ->whereDate('created_at', today())
                ->exists();

            if ($existingPriority) {
                return back()->with('error', 'You have already used your priority queue for today.');
            }

            // Verify cut-off queue if provided
            $cutoffQueue = null;
            if ($request->from_cutoff_queue) {
                $cutoffQueue = Queue::where('id', $request->from_cutoff_queue)
                    ->where('patient_id', $patientId)
                    ->where('status', 'Unattended')
                    ->first();

                if (!$cutoffQueue) {
                    return back()->with('error', 'Invalid cut-off queue reference.');
                }
            }

            $serviceType = $validated['service_type'];
            
            // Get service prefix
            $servicePrefixes = config('services_config.service_prefixes', []);
            $servicePrefix = $servicePrefixes[$serviceType] ?? 'GEN';
            
            // Priority queue always uses 'P' prefix
            $priorityPrefix = 'P';
            $todayCount = QueueCounter::getNextNumber($serviceType, 'P');
            
            // Generate queue number: SERVICE-P###
            $queueNumber = $servicePrefix . '-' . $priorityPrefix . str_pad($todayCount, 3, '0', STR_PAD_LEFT);

            // Generate QR code and verification token
            $verificationToken = Str::random(32);
            $qrCodeData = hash('sha256', $queueNumber . '-' . $verificationToken . '-' . now()->timestamp);

            $queue = Queue::create([
                'queue_number' => $queueNumber,
                'qr_code' => $qrCodeData,
                'verification_token' => $verificationToken,
                'patient_id' => $patientId,
                'service_type' => $serviceType,
                'priority' => 'Priority',
                'priority_reason' => 'Cut-off from previous day',
                'patient_category' => 'Cutoff Priority',
                'is_cutoff_priority' => true,
                'cutoff_priority_expires' => today(),
                'notes' => $validated['notes'] ?? 'Priority queue due to previous day cut-off',
                'status' => 'pending',
            ]);

            // Log the activity
            \App\Models\ActivityLog::create([
                'user_id' => $patientId,
                'user_type' => 'patient',
                'action' => 'queue_priority_request',
                'description' => "Patient requested priority queue due to cut-off (Original queue: {$cutoffQueue->queue_number})",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->route('patient.ticket', $queue->id)
                ->with('success', 'Your PRIORITY queue request has been submitted! You will be served before regular patients.');
            
        } catch (\Exception $e) {
            \Log::error('Priority queue submission failed', [
                'patient_id' => $patientAccount->patient_id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withInput()->with('error', 'Unable to submit priority queue request. Please try again.');
        }
    }

    /**
     * Centralized Announcement Display
     * Shows a dedicated page for announcing all called patients from all services
     */
    public function announcements()
    {
        return view('queue.announcements');
    }

    /**
     * API: Get pending announcements from all services
     * Returns newly called patients that haven't been announced yet
     */
    public function getPendingAnnouncements(Request $request)
    {
        // Get the last announcement ID that was processed
        $lastAnnouncedId = $request->get('last_id', 0);

        // Get all queues that have been called but not yet announced
        // Only get queues that were called in the last 5 minutes to avoid old data
        $announcements = \App\Models\FrontDeskQueue::with(['service'])
            ->where('id', '>', $lastAnnouncedId)
            ->where('status', 'called')
            ->where('called_at', '>=', now()->subMinutes(5))
            ->orderBy('called_at', 'asc')
            ->get()
            ->map(function ($queue) {
                return [
                    'id' => $queue->id,
                    'queue_number' => $queue->queue_number,
                    'patient_name' => $queue->patient_name ?? 'Unknown Patient',
                    'service_name' => $queue->service->name ?? 'Unknown Service',
                    'called_at' => $queue->called_at->format('h:i A'),
                    'priority' => $queue->priority ?? 'normal',
                ];
            });

        return response()->json([
            'success' => true,
            'announcements' => $announcements,
            'count' => $announcements->count(),
        ]);
    }
}
