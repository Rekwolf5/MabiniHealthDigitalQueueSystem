<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\FrontDeskQueue;
use App\Models\Service;
use Carbon\Carbon;

class ServiceQueueController extends Controller
{
    /**
     * Display service-specific queue dashboard
     */
    public function dashboard($serviceId)
    {
        $user = auth()->user();
        $service = Service::findOrFail($serviceId);
        
        // Double-check service access (middleware should handle this too)
        if (!$user->canAccessService($serviceId)) {
            abort(403, 'You do not have permission to access this service.');
        }
        
        // Get current queue for this service - EXCLUDE completed and cancelled
        $queues = FrontDeskQueue::where('service_id', $serviceId)
            ->today()
            ->whereNotIn('status', ['completed', 'cancelled'])  // Hide archived queues
            ->orderBy('priority', 'desc')
            ->orderBy('arrived_at')
            ->paginate(20);
        
        // Get service statistics (completed/cancelled are counted separately)
        $stats = [
            'waiting' => FrontDeskQueue::where('service_id', $serviceId)
                ->where('status', 'waiting')->today()->count(),
            'called' => FrontDeskQueue::where('service_id', $serviceId)
                ->where('status', 'called')->today()->count(),
            'in_progress' => FrontDeskQueue::where('service_id', $serviceId)
                ->where('status', 'in_progress')->today()->count(),
            'completed' => \App\Models\QueueArchive::where('service_id', $serviceId)
                ->where('status', 'completed')->whereDate('arrived_at', Carbon::today())->count(),
            'total_today' => FrontDeskQueue::where('service_id', $serviceId)
                ->whereNotIn('status', ['completed', 'cancelled'])->today()->count(),
            'current_capacity' => $service->getCurrentCapacity(),
            'max_capacity' => $service->capacity_per_hour,
            'availability' => $service->isAvailable(),
            'estimated_wait' => $service->getEstimatedWaitTime()
        ];

        return view('services.dashboard', compact('service', 'queues', 'stats'));
    }

    /**
     * Accept patient from front desk to service queue
     */
    public function acceptPatient(Request $request, $queueId)
    {
        $queue = FrontDeskQueue::findOrFail($queueId);
        $serviceId = $request->input('service_id');
        $service = Service::findOrFail($serviceId);

        // Check if service has capacity
        if (!$service->isAvailable()) {
            return redirect()->back()
                ->with('error', "Service {$service->name} is at full capacity for this hour.");
        }

        // Assign patient to service
        $queue->update([
            'service_id' => $serviceId,
            'status' => 'called', // Move to called status when accepted by service
            'called_at' => Carbon::now()
        ]);

        return redirect()->route('services.dashboard', $serviceId)
            ->with('success', "Patient {$queue->patient_name} has been accepted into {$service->name}.");
    }

    /**
     * Start service for patient (in progress)
     */
    public function startService($queueId)
    {
        $queue = FrontDeskQueue::findOrFail($queueId);
        
        $queue->update([
            'status' => 'in_progress',
            'started_at' => Carbon::now()
        ]);

        return redirect()->back()
            ->with('success', "Service started for patient {$queue->patient_name}.");
    }

    /**
     * Complete service for patient
     */
    public function completeService($queueId)
    {
        $queue = FrontDeskQueue::findOrFail($queueId);
        $serviceId = $queue->service_id;
        
        // Archive and delete completed queue
        DB::transaction(function () use ($queue) {
            // Archive the completed queue
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
                'status' => 'completed',
                'workflow_stage' => $queue->workflow_stage,
                'arrived_at' => $queue->arrived_at,
                'called_at' => $queue->called_at,
                'completed_at' => now(),
                'assigned_staff_id' => $queue->assigned_staff_id,
                'notes' => $queue->notes,
                'archived_reason' => 'Service completed successfully',
                'archived_by' => auth()->id(),
                'archived_at' => now()
            ]);
            
            // Delete from active queue to free up queue number
            $queue->delete();
        });

        // Automatically call the next patient in queue
        $nextPatient = $this->callNextPatient($serviceId);
        
        $message = "Service completed for patient {$queue->patient_name}. Queue archived.";
        
        if ($nextPatient) {
            $message .= " Next patient called: {$nextPatient->patient_name} (Queue #{$nextPatient->queue_number}).";
        } else {
            $message .= " No more patients waiting in queue.";
        }

        return redirect()->back()
            ->with('success', $message);
    }

    /**
     * Automatically call the next patient in the queue
     */
    protected function callNextPatient($serviceId)
    {
        // Get the next waiting patient who is ready for consultation (vitals already done)
        $nextPatient = FrontDeskQueue::where('service_id', $serviceId)
            ->where('status', 'waiting')
            ->where('workflow_stage', 'consultation') // Only call patients ready for doctor
            ->today()
            ->orderBy('priority', 'desc')
            ->orderBy('arrived_at')
            ->first();

        if ($nextPatient) {
            $nextPatient->update([
                'status' => 'called',
                'called_at' => Carbon::now()
            ]);

            // Here you can add notification logic (SMS, display board, etc.)
            // For example: event(new PatientCalled($nextPatient));
            
            return $nextPatient;
        }

        return null;
    }

    /**
     * Manually call the next patient (staff button)
     * This is used to call the FIRST patient when service starts or for simultaneous queue services
     */
    public function callNext($serviceId)
    {
        $service = Service::findOrFail($serviceId);
        
        // Check access
        if (!auth()->user()->canAccessService($serviceId)) {
            abort(403, 'You do not have permission to access this service.');
        }

        // Check if there's already a patient being called or in progress
        $activePatient = FrontDeskQueue::where('service_id', $serviceId)
            ->whereIn('status', ['called', 'in_progress'])
            ->today()
            ->first();

        // For simultaneous services, allow calling multiple patients
        // You can add logic here to check service type if needed
        
        $nextPatient = $this->callNextPatient($serviceId);
        
        if ($nextPatient) {
            return redirect()->back()
                ->with('success', "Called next patient: {$nextPatient->patient_name} (Queue #{$nextPatient->queue_number}). Click 'Start Service' when patient arrives.");
        }
        
        return redirect()->back()
            ->with('info', 'No patients waiting in queue.');
    }

    /**
     * Transfer patient to another service
     */
    public function transferPatient(Request $request, $queueId)
    {
        $request->validate([
            'new_service_id' => 'required|exists:services,id',
            'transfer_notes' => 'nullable|string'
        ]);

        $queue = FrontDeskQueue::findOrFail($queueId);
        $newService = Service::findOrFail($request->new_service_id);
        $oldService = $queue->service;

        // Check if new service has capacity
        if (!$newService->isAvailable()) {
            return redirect()->back()
                ->with('error', "Cannot transfer to {$newService->name} - service is at full capacity.");
        }

        // Update queue with new service and add transfer notes
        $transferNote = "Transferred from {$oldService->name} to {$newService->name}";
        if ($request->transfer_notes) {
            $transferNote .= " - Note: {$request->transfer_notes}";
        }

        $queue->update([
            'service_id' => $request->new_service_id,
            'status' => 'waiting', // Reset to waiting for new service
            'notes' => $queue->notes ? $queue->notes . "\n" . $transferNote : $transferNote
        ]);

        return redirect()->back()
            ->with('success', "Patient {$queue->patient_name} transferred to {$newService->name}.");
    }

    /**
     * Get available services for transfer
     */
    public function getAvailableServices($currentServiceId)
    {
        $services = Service::where('is_active', true)
            ->where('id', '!=', $currentServiceId)
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'available' => $service->isAvailable(),
                    'current_capacity' => $service->getCurrentCapacity(),
                    'max_capacity' => $service->capacity_per_hour,
                    'estimated_wait' => $service->getEstimatedWaitTime()
                ];
            });

        return response()->json($services);
    }

    /**
     * Call patient for vitals (patient stays in waiting, just goes to vitals station)
     */
    public function callForVitals($queueId)
    {
        $queue = FrontDeskQueue::findOrFail($queueId);
        
        // Keep status as waiting, just move to vitals workflow stage
        $queue->update([
            'workflow_stage' => 'vitals',
            'vitals_taken_at' => Carbon::now()
        ]);
        
        return redirect()->back()
            ->with('success', "Called next patient: {$queue->patient_name} (Queue #{$queue->queue_number}). Please proceed to vitals station.");
    }
    
    /**
     * Mark vitals as complete (patient ready for doctor)
     */
    public function completeVitals($queueId)
    {
        $queue = FrontDeskQueue::findOrFail($queueId);
        
        // Move to consultation workflow - patient now ready for doctor
        $queue->update([
            'workflow_stage' => 'consultation'
        ]);
        
        return redirect()->back()
            ->with('success', "Vitals completed for {$queue->patient_name} (Queue #{$queue->queue_number}). Patient ready for consultation.");
    }
    
    /**
     * Store patient vital signs
     */
    public function storeVitals(Request $request)
    {
        $request->validate([
            'queue_id' => 'required|exists:front_desk_queues,id',
            'blood_pressure' => 'nullable|string',
            'temperature' => 'nullable|numeric',
            'pulse_rate' => 'nullable|integer',
            'respiratory_rate' => 'nullable|integer',
            'weight' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'vitals_notes' => 'nullable|string'
        ]);
        
        $queue = FrontDeskQueue::findOrFail($request->queue_id);
        
        // Format vitals data
        $vitalsData = [];
        if ($request->blood_pressure) $vitalsData[] = "BP: {$request->blood_pressure}";
        if ($request->temperature) $vitalsData[] = "Temp: {$request->temperature}°C";
        if ($request->pulse_rate) $vitalsData[] = "Pulse: {$request->pulse_rate} bpm";
        if ($request->respiratory_rate) $vitalsData[] = "RR: {$request->respiratory_rate}";
        if ($request->weight) $vitalsData[] = "Weight: {$request->weight} kg";
        if ($request->height) $vitalsData[] = "Height: {$request->height} cm";
        
        $vitalsText = implode(', ', $vitalsData);
        
        // Add to notes
        $noteText = "[" . now()->format('h:i A') . "] VITALS: " . $vitalsText;
        if ($request->vitals_notes) {
            $noteText .= " - " . $request->vitals_notes;
        }
        
        $queue->update([
            'notes' => $queue->notes ? $queue->notes . "\n" . $noteText : $noteText,
            'vitals_taken_at' => now(),
            'workflow_stage' => 'vitals_taken'
        ]);
        
        return redirect()->back()
            ->with('success', "Vital signs recorded for {$queue->patient_name}. Patient ready for consultation.");
    }

    /**
     * Skip patient who is not present - move to end of queue
     */
    public function skipPatient($queueId)
    {
        $queue = FrontDeskQueue::findOrFail($queueId);
        $serviceId = $queue->service_id;
        
        // Reset to waiting and update timestamp to move to end
        $queue->update([
            'status' => 'waiting',
            'called_at' => null,
            'arrived_at' => now(), // Update time to move to end of queue
            'notes' => $queue->notes . "\n[" . now()->format('h:i A') . "] Patient skipped - not present when called"
        ]);
        
        // Automatically call the next patient
        $nextPatient = $this->callNextPatient($serviceId);
        
        $message = "Patient {$queue->patient_name} skipped (not present). Moved to end of queue.";
        
        if ($nextPatient) {
            $message .= " Next patient called: {$nextPatient->patient_name} (Queue #{$nextPatient->queue_number}).";
        } else {
            $message .= " No more patients waiting in queue.";
        }
        
        return redirect()->back()
            ->with('success', $message);
    }
    
    /**
     * Mark patient as no-show (never returned)
     */
    public function markNoShow($queueId)
    {
        $queue = FrontDeskQueue::findOrFail($queueId);
        $serviceId = $queue->service_id;
        
        // Archive as no-show and remove from active queue
        DB::transaction(function () use ($queue) {
            // Archive the no-show queue
            \App\Models\QueueArchive::create([
                'original_queue_id' => $queue->id,
                'patient_id' => $queue->patient_id ?? null,
                'service_id' => $queue->service_id,
                'queue_number' => $queue->queue_number,
                'patient_name' => $queue->patient_name,
                'contact_number' => $queue->contact_number,
                'age' => $queue->age,
                'chief_complaint' => $queue->chief_complaint,
                'allergies' => $queue->allergies,
                'priority' => $queue->priority,
                'urgency_level' => $queue->urgency_level,
                'status' => 'no_show',
                'workflow_stage' => $queue->workflow_stage,
                'arrived_at' => $queue->arrived_at,
                'called_at' => $queue->called_at,
                'completed_at' => now(),
                'assigned_staff_id' => $queue->assigned_staff_id,
                'notes' => $queue->notes . "\n[" . now()->format('h:i A') . "] Marked as NO SHOW - patient never returned",
                'archived_reason' => 'Patient marked as no-show - never returned after being called',
                'archived_by' => auth()->id(),
                'archived_at' => now()
            ]);
            
            // Decrement service patient count
            if ($queue->service) {
                $queue->service->decrementPatientCount();
            }
            
            // Delete from active queue
            $queue->delete();
        });
        
        // Automatically call the next patient
        $this->callNextPatient($serviceId);
        
        return redirect()->back()
            ->with('warning', "Patient {$queue->patient_name} marked as NO SHOW and archived. Next patient called.");
    }

    /**
     * Get service queue status for monitoring
     */
    public function getServiceStatus($serviceId)
    {
        $service = Service::findOrFail($serviceId);
        
        $status = [
            'service_name' => $service->name,
            'is_available' => $service->isAvailable(),
            'current_capacity' => $service->getCurrentCapacity(),
            'max_capacity' => $service->capacity_per_hour,
            'estimated_wait' => $service->getEstimatedWaitTime(),
            'queue_counts' => [
                'waiting' => FrontDeskQueue::where('service_id', $serviceId)
                    ->where('status', 'waiting')->today()->count(),
                'in_progress' => FrontDeskQueue::where('service_id', $serviceId)
                    ->where('status', 'in_progress')->today()->count(),
                'completed' => FrontDeskQueue::where('service_id', $serviceId)
                    ->where('status', 'completed')->today()->count()
            ]
        ];

        return response()->json($status);
    }
}
