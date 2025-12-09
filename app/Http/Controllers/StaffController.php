<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Queue;
use App\Models\Patient;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\MedicalRecord;
use App\Models\Consultation;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    public function __construct()
    {
        // Remove the middleware calls from constructor
        // These will be handled in routes instead
    }

    public function queueManagement(Request $request)
    {
        $tab = $request->get('tab', 'queue'); // queue, requests, search
        
        // Queue Management Tab Data
        $queue = \App\Models\FrontDeskQueue::with('service')
            ->whereDate('created_at', today())
            ->orderBy('arrived_at', 'asc')
            ->get()
            ->sortBy(function ($item) {
                $priorities = ['emergency' => 0, 'pwd' => 1, 'pregnant' => 2, 'senior' => 3, 'normal' => 4];
                return $priorities[$item->priority] ?? 4;
            })
            ->values();

        // Pending Requests Tab Data (Queue requests are handled differently now)
        $pendingRequests = collect(); // No pending approval system in current workflow

        // Statistics
        $stats = [
            'total_today' => \App\Models\FrontDeskQueue::whereDate('created_at', today())->count(),
            'waiting' => \App\Models\FrontDeskQueue::where('status', 'waiting')->whereDate('created_at', today())->count(),
            'consulting' => \App\Models\FrontDeskQueue::where('status', 'in_progress')->whereDate('created_at', today())->count(),
            'completed' => \App\Models\QueueArchive::whereDate('archived_at', today())->where('status', 'completed')->count(),
            'pending' => $pendingRequests->count(),
            'average_wait_time' => $this->calculateAverageWaitTime(),
            'service_breakdown' => $this->getServiceBreakdown(),
        ];

        // Search results (if search query provided)
        $searchResults = null;
        if ($request->has('search') && $tab === 'search') {
            $searchTerm = $request->get('search');
            $searchResults = \App\Models\FrontDeskQueue::where(function($query) use ($searchTerm) {
                    $query->where('patient_name', 'like', "%{$searchTerm}%")
                          ->orWhere('contact_number', 'like', "%{$searchTerm}%")
                          ->orWhere('queue_number', 'like', "%{$searchTerm}%");
                })
                ->whereDate('created_at', today())
                ->with('service')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        return view('staff.queue-management', compact('queue', 'tab', 'pendingRequests', 'stats', 'searchResults'));
    }

    private function calculateAverageWaitTime()
    {
        $completed = \App\Models\QueueArchive::whereDate('archived_at', today())
            ->whereNotNull('called_at')
            ->whereNotNull('arrived_at')
            ->get();

        if ($completed->isEmpty()) {
            return 0;
        }

        $totalMinutes = $completed->sum(function($queue) {
            $arrived = \Carbon\Carbon::parse($queue->arrived_at);
            $called = \Carbon\Carbon::parse($queue->called_at);
            return $arrived->diffInMinutes($called);
        });

        return round($totalMinutes / $completed->count());
    }

    private function getServiceBreakdown()
    {
        return \App\Models\FrontDeskQueue::whereDate('front_desk_queues.created_at', today())
            ->join('services', 'front_desk_queues.service_id', '=', 'services.id')
            ->selectRaw('services.name as service_name, COUNT(*) as count')
            ->groupBy('services.name')
            ->get()
            ->pluck('count', 'service_name')
            ->toArray();
    }

    public function callNext(Request $request, $id)
    {
        $queue = Queue::findOrFail($id);
        
        if ($queue->status === 'Waiting') {
            $queue->update([
                'status' => 'Consulting',
                'started_at' => now(),
            ]);

            ActivityLog::log('queue_start', "Started consultation for {$queue->patient->full_name}", [
                'queue_id' => $queue->id,
                'patient_id' => $queue->patient_id,
            ]);

            return back()->with('success', 'Patient called for consultation.');
        }

        return back()->with('error', 'Cannot call this patient.');
    }

    public function markServed(Request $request, $id)
    {
        $request->validate([
            'diagnosis' => 'nullable|string|max:500',
            'treatment' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'symptoms' => 'nullable|string|max:1000',
            'prescribed_medicines' => 'nullable|array',
            'prescribed_medicines.*.medicine_id' => 'required_with:prescribed_medicines|exists:medicines,id',
            'prescribed_medicines.*.name' => 'required_with:prescribed_medicines|string',
            'prescribed_medicines.*.quantity' => 'required_with:prescribed_medicines|numeric|min:1',
            'prescribed_medicines.*.unit' => 'nullable|string',
            'prescribed_medicines.*.instructions' => 'nullable|string|max:500',
        ]);

        $queue = Queue::findOrFail($id);
        
        if ($queue->status === 'Consulting') {
            $queue->update([
                'status' => 'Completed',
                'completed_at' => now(),
                'notes' => $request->notes,
            ]);

            // Create consultation record with prescription data
            if ($request->diagnosis || $request->treatment || $request->prescribed_medicines) {
                Consultation::create([
                    'patient_id' => $queue->patient_id,
                    'queue_id' => $queue->id,
                    'diagnosis' => $request->diagnosis,
                    'symptoms' => $request->symptoms,
                    'treatment' => $request->treatment,
                    'prescription' => $request->treatment, // For backward compatibility
                    'prescribed_medicines' => !empty($request->prescribed_medicines) ? json_encode($request->prescribed_medicines) : null,
                    'prescription_dispensed' => false,
                    'notes' => $request->notes,
                ]);
            }

            // Create medical record if diagnosis/treatment provided (for backward compatibility)
            if ($request->diagnosis || $request->treatment) {
                MedicalRecord::create([
                    'patient_id' => $queue->patient_id,
                    'diagnosis' => $request->diagnosis,
                    'treatment' => $request->treatment,
                    'notes' => $request->notes,
                    'visit_date' => today(),
                ]);
            }

            ActivityLog::log('queue_complete', "Completed consultation for {$queue->patient->full_name}", [
                'queue_id' => $queue->id,
                'patient_id' => $queue->patient_id,
                'diagnosis' => $request->diagnosis,
                'prescribed_medicines_count' => !empty($request->prescribed_medicines) ? count($request->prescribed_medicines) : 0,
            ]);

            return back()->with('success', 'Patient marked as served.' . 
                (!empty($request->prescribed_medicines) ? ' Prescription sent to pharmacy.' : ''));
        }

        return back()->with('error', 'Cannot mark this patient as served.');
    }

    public function markNoShow(Request $request, $id)
    {
        $queue = Queue::findOrFail($id);
        
        $queue->update([
            'status' => 'No Show',
            'notes' => 'Patient did not show up for appointment',
        ]);

        ActivityLog::log('queue_no_show', "Marked {$queue->patient->full_name} as no-show", [
            'queue_id' => $queue->id,
            'patient_id' => $queue->patient_id,
        ]);

        return back()->with('success', 'Patient marked as no-show.');
    }

    public function patientHistory($id)
    {
        $patient = Patient::findOrFail($id);
        $medicalHistory = MedicalRecord::where('patient_id', $id)
            ->orderBy('visit_date', 'desc')
            ->paginate(10);

        return view('staff.patient-history', compact('patient', 'medicalHistory'));
    }

    public function printQueueList()
    {
        $queue = Queue::with('patient')
            ->whereDate('created_at', today())
            ->orderBy('arrived_at', 'asc')
            ->get()
            ->sortBy(function ($item) {
                $priorities = ['Emergency' => 1, 'Urgent' => 2, 'Normal' => 3];
                return $priorities[$item->priority] ?? 4;
            })
            ->values();

        return view('staff.print-queue', compact('queue'));
    }

    public function medicineInventory()
    {
        $medicines = Medicine::orderBy('name')->get();
        return view('staff.medicine-inventory', compact('medicines'));
    }

    public function updateMedicineStock(Request $request, $id)
    {
        $request->validate([
            'stock_change' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'expiry_date' => 'required|date|after:today',
            'batch_number' => 'nullable|string|max:100',
            'supplier' => 'nullable|string|max:255',
        ]);

        $medicine = Medicine::findOrFail($id);
        
        // Create a new batch
        $batch = MedicineBatch::create([
            'medicine_id' => $medicine->id,
            'batch_number' => $request->batch_number,
            'quantity' => $request->stock_change,
            'expiry_date' => $request->expiry_date,
            'received_date' => now(),
            'supplier' => $request->supplier,
            'notes' => $request->reason,
        ]);

        // Also update the legacy stock field for backward compatibility
        $oldStock = $medicine->stock;
        $newStock = $oldStock + $request->stock_change;
        $medicine->update(['stock' => $newStock]);

        ActivityLog::log('medicine_stock_update', "Added {$request->stock_change} units of {$medicine->name} (Batch: {$batch->batch_number})", [
            'medicine_id' => $medicine->id,
            'batch_id' => $batch->id,
            'old_stock' => $oldStock,
            'new_stock' => $newStock,
            'quantity_added' => $request->stock_change,
            'batch_number' => $batch->batch_number,
            'expiry_date' => $batch->expiry_date->format('Y-m-d'),
            'reason' => $request->reason,
        ]);

        return back()->with('success', "Medicine restocked successfully! Batch #{$batch->batch_number} added.");
    }

    // Show vital signs form for staff
    public function showVitalSignsForm($queueId)
    {
        $queue = Queue::with('patient', 'consultation')->findOrFail($queueId);
        return view('staff.vital-signs', compact('queue'));
    }

    // Save vital signs
    public function saveVitalSigns(Request $request, $queueId)
    {
        $validated = $request->validate([
            'blood_pressure' => 'nullable|string|max:20',
            'temperature' => 'nullable|numeric|min:30|max:45',
            'pulse_rate' => 'nullable|integer|min:30|max:200',
            'weight' => 'nullable|numeric|min:1|max:500',
            'height' => 'nullable|numeric|min:30|max:300',
        ]);

        $queue = Queue::findOrFail($queueId);

        // Create or update consultation record with vital signs
        $consultation = Consultation::updateOrCreate(
            ['queue_id' => $queue->id],
            [
                'patient_id' => $queue->patient_id,
                'doctor_id' => $queue->assigned_doctor_id,
                'blood_pressure' => $validated['blood_pressure'],
                'temperature' => $validated['temperature'],
                'pulse_rate' => $validated['pulse_rate'],
                'weight' => $validated['weight'],
                'height' => $validated['height'],
            ]
        );

        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity_type' => 'vital_signs_recorded',
            'description' => 'Recorded vital signs for ' . $queue->patient->name . ' (Queue: ' . $queue->queue_number . ')',
            'related_type' => 'consultation',
            'related_id' => $consultation->id,
        ]);

        return back()->with('success', 'Vital signs recorded successfully!');
    }
}

