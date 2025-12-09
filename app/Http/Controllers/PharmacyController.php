<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Queue;
use App\Models\Consultation;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\MedicineDispensed;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PharmacyController extends Controller
{
    /**
     * Show pending prescriptions waiting to be dispensed
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'pending'); // pending, history, alerts
        
        // Get completed consultations with prescriptions that haven't been fully dispensed
        $prescriptions = Queue::with(['patient', 'consultation'])
            ->whereHas('consultation', function($query) {
                $query->where('prescription_dispensed', false)
                      ->whereNotNull('prescribed_medicines')
                      ->where('prescribed_medicines', '!=', '[]')
                      ->where('prescribed_medicines', '!=', 'null');
            })
            ->whereIn('status', ['Completed', 'Consulting'])
            ->orderBy('completed_at', 'desc')
            ->paginate(20);

        $pendingCount = $prescriptions->total();

        // Get all medicines for quick dispense dropdown
        $medicines = Medicine::where('stock', '>', 0)->orderBy('name', 'asc')->get();

        // Dispensing History
        $history = null;
        if ($tab === 'history') {
            $history = MedicineDispensed::with(['medicine', 'batch', 'dispensedByUser'])
                ->orderBy('dispensed_at', 'desc');
            
            // Apply filters if provided
            if ($request->has('date_from')) {
                $history->whereDate('dispensed_at', '>=', $request->date_from);
            }
            if ($request->has('date_to')) {
                $history->whereDate('dispensed_at', '<=', $request->date_to);
            }
            if ($request->has('search')) {
                $searchTerm = $request->search;
                $history->whereHas('queue.patient', function($query) use ($searchTerm) {
                    $query->where('first_name', 'like', "%{$searchTerm}%")
                          ->orWhere('last_name', 'like', "%{$searchTerm}%");
                });
            }
            
            $history = $history->paginate(20);
        }

        // Stock Alerts
        $alerts = null;
        if ($tab === 'alerts') {
            $alerts = [
                'low_stock' => Medicine::where('stock', '>', 0)->where('stock', '<=', 10)->orderBy('stock', 'asc')->get(),
                'out_of_stock' => Medicine::where('stock', '<=', 0)->orderBy('name')->get(),
                'expiring_soon' => MedicineBatch::where('quantity', '>', 0)
                    ->whereDate('expiry_date', '<=', now()->addDays(30))
                    ->orderBy('expiry_date', 'asc')
                    ->with('medicine')
                    ->get(),
                'expired' => MedicineBatch::where('quantity', '>', 0)
                    ->whereDate('expiry_date', '<', now())
                    ->orderBy('expiry_date', 'asc')
                    ->with('medicine')
                    ->get(),
            ];
        }

        // Quick Stats
        $stats = [
            'pending_prescriptions' => $pendingCount,
            'dispensed_today' => MedicineDispensed::whereDate('dispensed_at', today())->count(),
            'total_stock_value' => Medicine::sum(DB::raw('stock * unit_price')),
            'low_stock_count' => Medicine::where('stock', '>', 0)->where('stock', '<=', 10)->count(),
            'expiring_soon_count' => MedicineBatch::where('quantity', '>', 0)
                ->whereDate('expiry_date', '<=', now()->addDays(30))
                ->whereDate('expiry_date', '>=', now())
                ->count(),
        ];

        return view('pharmacy.index', compact('prescriptions', 'pendingCount', 'medicines', 'tab', 'history', 'alerts', 'stats'));
    }

    /**
     * Show quick/direct dispense form (for walk-in patients with paper prescriptions)
     */
    public function showQuickDispense()
    {
        $medicines = Medicine::where('stock', '>', 0)->orderBy('name', 'asc')->get();
        $batches = MedicineBatch::where('quantity', '>', 0)
            ->orderBy('expiry_date', 'asc')
            ->get();

        return view('pharmacy.quick-dispense', compact('medicines', 'batches'));
    }

    /**
     * Process quick/direct dispensing (no queue/consultation required)
     */
    public function quickDispense(Request $request)
    {
        $request->validate([
            'patient_name' => 'required|string|max:255',
            'patient_contact' => 'nullable|string|max:255',
            'prescription_source' => 'required|string|max:255',
            'medicines' => 'required|array|min:1',
            'medicines.*.medicine_id' => 'required|exists:medicines,id',
            'medicines.*.batch_id' => 'required|exists:medicine_batches,id',
            'medicines.*.quantity' => 'required|integer|min:1',
            'medicines.*.instructions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        
        try {
            foreach ($request->medicines as $medicineData) {
                // Get batch
                $batch = MedicineBatch::findOrFail($medicineData['batch_id']);
                $medicine = Medicine::findOrFail($medicineData['medicine_id']);
                
                // Check if enough stock in batch
                if ($batch->quantity < $medicineData['quantity']) {
                    throw new \Exception("Insufficient stock in batch {$batch->batch_number} for {$medicine->name}");
                }

                // Create dispensed record (without consultation/queue)
                MedicineDispensed::create([
                    'consultation_id' => null,
                    'queue_id' => null,
                    'medicine_id' => $medicineData['medicine_id'],
                    'batch_id' => $medicineData['batch_id'],
                    'quantity' => $medicineData['quantity'],
                    'instructions' => $medicineData['instructions'] ?? null,
                    'dispensed_by' => Auth::id(),
                    'dispensed_at' => now(),
                    'status' => 'dispensed',
                ]);

                // Reduce batch quantity (FEFO - First Expiry First Out)
                $batch->decrement('quantity', $medicineData['quantity']);

                // Also update medicine stock for backward compatibility
                $medicine->decrement('stock', $medicineData['quantity']);

                // Log activity
                ActivityLog::log('quick_dispense', 
                    "Quick dispensed {$medicineData['quantity']} units of {$medicine->name} to {$request->patient_name}", [
                    'patient_name' => $request->patient_name,
                    'patient_contact' => $request->patient_contact,
                    'prescription_source' => $request->prescription_source,
                    'medicine_id' => $medicineData['medicine_id'],
                    'batch_id' => $medicineData['batch_id'],
                    'quantity' => $medicineData['quantity'],
                    'batch_number' => $batch->batch_number,
                    'remaining_stock' => $batch->quantity,
                    'notes' => $request->notes,
                ]);
            }

            DB::commit();

            return redirect()->route('pharmacy.index')
                ->with('success', "Medicines dispensed successfully to {$request->patient_name}! (Paper-based/Walk-in)");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error dispensing medicines: ' . $e->getMessage());
        }
    }

    /**
     * Show dispense form for a specific patient/queue
     */
    public function showDispenseForm($queueId)
    {
        $queue = Queue::with(['patient', 'consultation'])->findOrFail($queueId);
        
        if (!$queue->consultation || !$queue->consultation->prescribed_medicines) {
            return back()->with('error', 'No prescription found for this patient.');
        }

        // Parse prescribed medicines (stored as JSON)
        $prescribedMedicines = json_decode($queue->consultation->prescribed_medicines, true);
        
        // Get all medicines
        $medicines = Medicine::where('stock', '>', 0)->orderBy('name', 'asc')->get();

        // Get all available batches ordered by expiry date (FEFO - First Expiry First Out)
        $batches = MedicineBatch::where('quantity', '>', 0)
            ->orderBy('expiry_date', 'asc')
            ->get();

        return view('pharmacy.dispense', compact('queue', 'prescribedMedicines', 'medicines', 'batches'));
    }

    /**
     * Process medicine dispensing
     */
    public function dispense(Request $request, $queueId)
    {
        $request->validate([
            'medicines' => 'required|array|min:1',
            'medicines.*.medicine_id' => 'required|exists:medicines,id',
            'medicines.*.batch_id' => 'required|exists:medicine_batches,id',
            'medicines.*.quantity' => 'required|integer|min:1',
            'medicines.*.instructions' => 'nullable|string',
        ]);

        $queue = Queue::with('consultation')->findOrFail($queueId);
        
        if (!$queue->consultation) {
            return back()->with('error', 'No consultation found for this patient.');
        }

        DB::beginTransaction();
        
        try {
            foreach ($request->medicines as $medicineData) {
                // Get batch
                $batch = MedicineBatch::findOrFail($medicineData['batch_id']);
                
                // Check if enough stock in batch
                if ($batch->quantity < $medicineData['quantity']) {
                    throw new \Exception("Insufficient stock in batch {$batch->batch_number} for {$batch->medicine->name}");
                }

                // Create dispensed record
                $dispensed = MedicineDispensed::create([
                    'consultation_id' => $queue->consultation->id,
                    'queue_id' => $queue->id,
                    'medicine_id' => $medicineData['medicine_id'],
                    'batch_id' => $medicineData['batch_id'],
                    'quantity' => $medicineData['quantity'],
                    'instructions' => $medicineData['instructions'] ?? null,
                    'dispensed_by' => Auth::id(),
                    'dispensed_at' => now(),
                    'status' => 'dispensed',
                ]);

                // Reduce batch quantity (FEFO - First Expiry First Out)
                $batch->decrement('quantity', $medicineData['quantity']);

                // Also update medicine stock for backward compatibility
                $batch->medicine->decrement('stock', $medicineData['quantity']);

                // Log activity
                ActivityLog::log('medicine_dispensed', 
                    "Dispensed {$medicineData['quantity']} units of {$batch->medicine->name} to {$queue->patient->full_name}", [
                    'queue_id' => $queue->id,
                    'patient_id' => $queue->patient_id,
                    'medicine_id' => $medicineData['medicine_id'],
                    'batch_id' => $medicineData['batch_id'],
                    'quantity' => $medicineData['quantity'],
                    'batch_number' => $batch->batch_number,
                    'remaining_stock' => $batch->quantity,
                ]);
            }

            // Mark consultation as prescription_dispensed
            $queue->consultation->update(['prescription_dispensed' => true]);

            DB::commit();

            return redirect()->route('pharmacy.index')
                ->with('success', "Medicines dispensed successfully to {$queue->patient->full_name}!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error dispensing medicines: ' . $e->getMessage());
        }
    }

    /**
     * Get patient prescription details (AJAX)
     */
    public function getPrescription($queueId)
    {
        $queue = Queue::with(['patient', 'consultation'])->find($queueId);
        
        if (!$queue || !$queue->consultation) {
            return response()->json(['error' => 'Consultation not found'], 404);
        }

        $prescribedMedicines = json_decode($queue->consultation->prescribed_medicines, true);
        
        return response()->json([
            'patient' => $queue->patient,
            'queue_number' => $queue->queue_number,
            'consultation' => $queue->consultation,
            'prescribed_medicines' => $prescribedMedicines,
        ]);
    }

    /**
     * Cancel a pending prescription
     */
    public function cancelPrescription($queueId)
    {
        $queue = Queue::with('consultation')->findOrFail($queueId);
        
        if ($queue->consultation) {
            // Mark all pending dispensed records as cancelled
            MedicineDispensed::where('consultation_id', $queue->consultation->id)
                ->where('status', 'pending')
                ->update(['status' => 'cancelled']);
                
            $queue->consultation->update(['prescription_dispensed' => true]);
            
            ActivityLog::log('prescription_cancelled', 
                "Cancelled prescription for {$queue->patient->full_name}", [
                'queue_id' => $queue->id,
                'consultation_id' => $queue->consultation->id,
            ]);
        }

        return back()->with('success', 'Prescription cancelled.');
    }
}

