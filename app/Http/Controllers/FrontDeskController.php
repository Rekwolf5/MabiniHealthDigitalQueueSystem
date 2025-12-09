<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\FrontDeskQueue;
use App\Models\Service;
use Carbon\Carbon;

class FrontDeskController extends Controller
{
    /**
     * Display the front desk queue dashboard
     */
    public function index(Request $request)
    {
        // Get all services
        $services = Service::where('is_active', true)->get();
        
        // Get today's queue with filters - EXCLUDE completed and cancelled from view
        $query = FrontDeskQueue::today()
            ->with('service')
            ->whereNotIn('status', ['completed', 'cancelled'])  // Hide archived queues
            ->orderBy('priority', 'desc')
            ->orderBy('arrived_at');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }
        
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('patient_name', 'like', '%' . $request->search . '%')
                  ->orWhere('queue_number', 'like', '%' . $request->search . '%')
                  ->orWhere('contact_number', 'like', '%' . $request->search . '%');
            });
        }

        $queues = $query->paginate(15)->withQueryString();
        
        // Get statistics (completed/cancelled counted from archive)
        $stats = [
            'total_today' => FrontDeskQueue::today()->whereNotIn('status', ['completed', 'cancelled'])->count(),
            'waiting' => FrontDeskQueue::today()->where('status', 'waiting')->count(),
            'in_progress' => FrontDeskQueue::today()->where('status', 'in_progress')->count(),
            'completed' => \App\Models\QueueArchive::whereDate('arrived_at', Carbon::today())
                ->where('status', 'completed')->count(),
        ];

        return view('front-desk.index', compact('queues', 'services', 'stats'));
    }

    /**
     * Store a new walk-in patient in the queue
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'age' => 'nullable|integer|min:1|max:120',
            'chief_complaint' => 'required|string|max:500',
            'allergies' => 'nullable|string|max:500',
            'service_id' => 'required|exists:services,id', // NOW REQUIRED
            'priority' => 'required|in:normal,senior,pwd,emergency',
            'notes' => 'nullable|string'
        ]);

        // Check service availability
        $service = \App\Models\Service::findOrFail($request->service_id);
        
        if (!$service->isAvailableForNewPatients()) {
            $availabilityStatus = $service->getAvailabilityStatus();
            
            // Allow emergency cases even if service is at capacity (but not if completely unavailable)
            if ($request->priority !== 'emergency' || $availabilityStatus['status'] === 'unavailable') {
                return redirect()->route('front-desk.index')
                    ->withErrors(['service_id' => "Cannot register for {$service->name}: {$availabilityStatus['message']}"])
                    ->withInput();
            }
            
            // If emergency case and service is full/closed, add warning but allow registration
            if ($request->priority === 'emergency' && in_array($availabilityStatus['status'], ['full', 'closed'])) {
                session()->flash('warning', "EMERGENCY CASE: Patient registered despite service constraints ({$availabilityStatus['message']})");
            }
        }

        // Use database transaction to prevent race conditions with queue number generation
        $queue = DB::transaction(function () use ($request) {
            // Set default values for streamlined workflow
            $data = $request->all();
            $data['urgency_level'] = $request->priority === 'emergency' ? 'emergency' : 'routine';
            $data['workflow_stage'] = 'registration';
            $data['arrived_at'] = now();

            $queue = FrontDeskQueue::create($data);

            // Increment service patient count
            if ($queue->service_id) {
                $queue->service->incrementPatientCount();
            }
            
            return $queue;
        });

        // Format the success message with service info
        $serviceName = $queue->service ? $queue->service->name : 'General Service';
        $priorityText = match($queue->priority) {
            'emergency' => ' (EMERGENCY)',
            'senior' => ' (Senior Citizen)',
            'pwd' => ' (PWD)',
            default => ''
        };

        // Check if service is approaching capacity
        $capacityWarning = '';
        if ($queue->service && $queue->service->daily_patient_limit) {
            $remaining = $queue->service->daily_patient_limit - $queue->service->current_patient_count;
            $percentUsed = ($queue->service->current_patient_count / $queue->service->daily_patient_limit) * 100;
            
            // Create notification if at 80% capacity
            if ($percentUsed >= 80 && $percentUsed < 100) {
                \App\Models\Notification::createForAllStaff(
                    'capacity_warning',
                    '⚠️ Service Near Capacity',
                    "{$serviceName} is at {$queue->service->current_patient_count}/{$queue->service->daily_patient_limit} capacity ({$percentUsed}%)",
                    ['service_id' => $queue->service_id, 'capacity_percent' => $percentUsed]
                );
            }
            
            // Create notification if at 100% capacity
            if ($percentUsed >= 100) {
                \App\Models\Notification::createForAllStaff(
                    'capacity_warning',
                    '🚫 Service at Full Capacity',
                    "{$serviceName} has reached maximum daily capacity ({$queue->service->daily_patient_limit} patients)",
                    ['service_id' => $queue->service_id, 'capacity_percent' => $percentUsed]
                );
            }
            
            if ($remaining <= 5 && $remaining > 0) {
                $capacityWarning = " (Warning: Only {$remaining} slots remaining today)";
            }
        }

        // Create notification for priority patients
        if (in_array($queue->priority, ['emergency', 'pregnant'])) {
            $priorityIcon = $queue->priority === 'emergency' ? '🚨' : '🤰';
            $priorityLabel = strtoupper($queue->priority);
            
            \App\Models\Notification::createForAllStaff(
                'priority_alert',
                "{$priorityIcon} {$priorityLabel} Patient",
                "{$priorityLabel} patient added to {$serviceName}: {$queue->patient_name} ({$queue->queue_number})",
                ['service_id' => $queue->service_id, 'queue_id' => $queue->id, 'priority' => $queue->priority]
            );
        }

        return redirect()->route('front-desk.index')
            ->with('success', "Patient {$queue->patient_name} registered for {$serviceName}{$priorityText}. Queue Number: {$queue->queue_number}{$capacityWarning}");
    }

    /**
     * Call the next patient
     */
    public function callNext($id)
    {
        $queue = FrontDeskQueue::findOrFail($id);
        
        if ($queue->status === 'waiting') {
            $queue->markAsCalled();
            return redirect()->route('front-desk.index')
                ->with('success', "Patient {$queue->patient_name} ({$queue->queue_number}) has been called");
        }
        
        return redirect()->route('front-desk.index')
            ->with('error', 'Patient is not in waiting status');
    }

    /**
     * Mark patient as completed
     */
    public function complete($id)
    {
        $queue = FrontDeskQueue::findOrFail($id);
        
        if (in_array($queue->status, ['called', 'in_progress'])) {
            $queue->markAsCompleted();
            return redirect()->route('front-desk.index')
                ->with('success', "Patient {$queue->patient_name} ({$queue->queue_number}) marked as completed");
        }
        
        return redirect()->route('front-desk.index')
            ->with('error', 'Patient cannot be marked as completed from current status');
    }

    /**
     * Cancel a queue entry
     */
    public function cancel($id)
    {
        $queue = FrontDeskQueue::findOrFail($id);
        
        if ($queue->status !== 'completed') {
            DB::transaction(function () use ($queue) {
                // Decrement service patient count if patient was counted
                if ($queue->service_id && in_array($queue->status, ['waiting', 'called', 'in_progress'])) {
                    $queue->service->decrementPatientCount();
                }
                
                // Archive the queue before deleting
                \App\Models\QueueArchive::create([
                    'original_queue_id' => $queue->id,
                    'patient_id' => $queue->patient_id ?? null,  // Nullable for walk-in patients
                    'queue_number' => $queue->queue_number,
                    'patient_name' => $queue->patient_name,
                    'contact_number' => $queue->contact_number,
                    'age' => $queue->age,
                    'chief_complaint' => $queue->chief_complaint,
                    'allergies' => $queue->allergies,
                    'service_id' => $queue->service_id,
                    'priority' => $queue->priority,
                    'urgency_level' => $queue->urgency_level,
                    'status' => 'cancelled',
                    'workflow_stage' => $queue->workflow_stage,
                    'arrived_at' => $queue->arrived_at,
                    'called_at' => $queue->called_at,
                    'completed_at' => now(),
                    'assigned_staff_id' => $queue->assigned_staff_id,
                    'notes' => $queue->notes,
                    'archived_reason' => 'Cancelled by front desk staff',
                    'archived_by' => auth()->id(),
                    'archived_at' => now()
                ]);
                
                // Delete from active queue to free up queue number
                $queue->delete();
            });
            
            return redirect()->route('front-desk.index')
                ->with('success', "Patient {$queue->patient_name} ({$queue->queue_number}) has been cancelled and archived");
        }
        
        return redirect()->route('front-desk.index')
            ->with('error', 'Completed patients cannot be cancelled');
    }

    /**
     * Show the form for creating a new queue entry
     */
    public function create()
    {
        $services = Service::where('is_active', true)->get();
        return view('front-desk.create', compact('services'));
    }

    /**
     * Update queue details
     */
    public function update(Request $request, $id)
    {
        $queue = FrontDeskQueue::findOrFail($id);
        
        $request->validate([
            'patient_name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'service_id' => 'nullable|exists:services,id',
            'priority' => 'required|in:normal,senior,pwd,emergency',
            'notes' => 'nullable|string'
        ]);

        $queue->update($request->only([
            'patient_name', 'contact_number', 'service_id', 'priority', 'notes'
        ]));

        return redirect()->route('front-desk.index')
            ->with('success', "Queue entry for {$queue->patient_name} has been updated");
    }

    /**
     * Get real-time service capacity and estimation
     */
    public function getServiceCapacity($serviceId)
    {
        $service = Service::findOrFail($serviceId);
        
        if (!$service->is_active || !$service->available_today) {
            return response()->json([
                'available' => false,
                'reason' => $service->unavailable_reason ?? 'Service is currently unavailable',
                'available_slots' => 0,
            ]);
        }
        
        $capacity = $service->calculateDynamicCapacity();
        
        return response()->json([
            'available' => $capacity['available_slots'] > 0,
            'available_slots' => $capacity['available_slots'],
            'avg_service_time' => $capacity['avg_service_time'],
            'current_waiting' => $capacity['current_waiting'],
            'estimated_wait_time' => $capacity['estimated_wait_time'],
            'cutoff_time' => $capacity['cutoff_time'],
            'remaining_minutes' => $capacity['remaining_minutes'],
            'daily_limit_remaining' => $capacity['daily_limit_remaining'],
            'reason' => $capacity['reason'] ?? null,
        ]);
    }
}

