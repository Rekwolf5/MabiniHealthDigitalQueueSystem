<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\Patient;
use App\Models\Consultation;
use App\Models\Medicine;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DoctorController extends Controller
{
    /**
     * Display the doctor's dashboard
     */
    public function dashboard()
    {
        $doctorId = Auth::id();
        
        // Today's statistics
        $stats = [
            'total_patients_today' => Queue::where('assigned_doctor_id', $doctorId)
                ->whereDate('created_at', today())
                ->count(),
            'completed_today' => Queue::where('assigned_doctor_id', $doctorId)
                ->whereDate('created_at', today())
                ->where('status', 'completed')
                ->count(),
            'pending' => Queue::where('assigned_doctor_id', $doctorId)
                ->where('status', 'consulting')
                ->count(),
            'total_consultations' => Consultation::where('doctor_id', $doctorId)->count(),
        ];
        
        // Current/pending patients
        $currentPatients = Queue::with('patient')
            ->where('assigned_doctor_id', $doctorId)
            ->where('status', 'consulting')
            ->orderBy('arrived_at', 'asc')
            ->get();
        
        // Recent consultations
        $recentConsultations = Consultation::with(['patient', 'queue'])
            ->where('doctor_id', $doctorId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('doctor.dashboard', compact('stats', 'currentPatients', 'recentConsultations'));
    }
    
    /**
     * Show all patients assigned to this doctor
     */
    public function myQueue()
    {
        $doctorId = Auth::id();
        
        // Get all patients assigned to this doctor today
        $myPatients = Queue::with('patient')
            ->where('assigned_doctor_id', $doctorId)
            ->whereDate('created_at', today())
            ->orderBy('arrived_at', 'asc')
            ->get();
        
        return view('doctor.my-queue', compact('myPatients'));
    }
    
    /**
     * Accept an assigned patient
     */
    public function acceptPatient($queueId)
    {
        $queue = Queue::findOrFail($queueId);
        
        // Verify this patient is assigned to the current doctor
        if ($queue->assigned_doctor_id != Auth::id()) {
            return back()->with('error', 'You are not assigned to this patient.');
        }
        
        // Check if already accepted
        if ($queue->doctor_accepted_at) {
            return back()->with('info', 'You have already accepted this patient.');
        }
        
        // Accept the patient
        $queue->doctor_accepted_at = now();
        $queue->save();
        
        // Log activity
        try {
            ActivityLog::log(
                'doctor_accepted_patient',
                'Doctor accepted patient: ' . $queue->patient->full_name . ' (Queue #' . $queue->queue_number . ')',
                $queue->id
            );
        } catch (\Exception $e) {
            // Continue even if logging fails
        }
        
        // Send notification to patient
        if ($queue->patient && $queue->patient->patientAccount) {
            try {
                $queue->patient->patientAccount->notify(
                    new \App\Notifications\QueueStatusNotification($queue, 'doctor_accepted')
                );
            } catch (\Exception $e) {
                // Continue even if notification fails
            }
        }
        
        return back()->with('success', 'Patient accepted successfully!');
    }
    
    /**
     * Reject an assigned patient
     */
    public function rejectPatient(Request $request, $queueId)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);
        
        $queue = Queue::findOrFail($queueId);
        
        // Verify this patient is assigned to the current doctor
        if ($queue->assigned_doctor_id != Auth::id()) {
            return back()->with('error', 'You are not assigned to this patient.');
        }
        
        // Reject the patient - unassign doctor
        $queue->assigned_doctor_id = null;
        $queue->doctor_accepted_at = null;
        $queue->rejection_reason = $request->rejection_reason;
        $queue->rejected_by = Auth::id();
        $queue->rejected_at = now();
        $queue->save();
        
        // Log activity
        try {
            ActivityLog::log(
                'doctor_rejected_patient',
                'Doctor rejected patient: ' . $queue->patient->full_name . ' (Queue #' . $queue->queue_number . ') - Reason: ' . $request->rejection_reason,
                $queue->id
            );
        } catch (\Exception $e) {
            // Continue even if logging fails
        }
        
        return redirect()->route('doctor.my-queue')->with('success', 'Patient rejected. Staff will reassign to another doctor.');
    }
    
    /**
     * Show consultation form for a specific patient
     */
    public function showConsultationForm($queueId)
    {
        $queue = Queue::with(['patient', 'consultation'])->findOrFail($queueId);
        
        // Verify this patient is assigned to the current doctor
        if ($queue->assigned_doctor_id != Auth::id()) {
            return redirect()->route('doctor.dashboard')
                ->with('error', 'You are not assigned to this patient.');
        }
        
        return view('doctor.consultation-form', compact('queue'));
    }
    
    /**
     * Save consultation data
     */
    public function saveConsultation(Request $request, $queueId)
    {
        // Check if this is a quick mode consultation
        $isQuickMode = $request->quick_mode == '1';
        
        $request->validate([
            // Required fields (minimal for hybrid support)
            'chief_complaint' => 'required|string',
            'diagnosis' => 'required|string',
            
            // Optional fields - vital signs (can be skipped in quick mode)
            'blood_pressure' => 'nullable|string',
            'temperature' => 'nullable|numeric',
            'pulse_rate' => 'nullable|integer',
            'weight' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            
            // Optional fields - clinical assessment
            'symptoms' => 'nullable|string',
            'physical_examination' => 'nullable|string',
            'treatment' => 'nullable|string',
            
            // Optional fields - prescription (can provide paper prescription instead)
            'prescribed_medicines' => 'nullable|array',
            'prescribed_medicines.*.medicine_id' => 'required_with:prescribed_medicines|exists:medicines,id',
            'prescribed_medicines.*.quantity' => 'required_with:prescribed_medicines|integer|min:1',
            'prescribed_medicines.*.dosage' => 'nullable|string',
            'prescribed_medicines.*.frequency' => 'nullable|string',
            'prescribed_medicines.*.duration' => 'nullable|string',
            'prescribed_medicines.*.instructions' => 'nullable|string',
            
            // Optional fields - follow-up
            'follow_up_date' => 'nullable|date',
            'doctor_notes' => 'nullable|string',
            'quick_mode' => 'nullable|boolean',
        ]);
        
        DB::beginTransaction();
        try {
            $queue = Queue::findOrFail($queueId);
            
            // Verify this patient is assigned to the current doctor
            if ($queue->assigned_doctor_id != Auth::id()) {
                return redirect()->route('doctor.dashboard')
                    ->with('error', 'You are not assigned to this patient.');
            }
            
            // Enrich prescribed medicines with medicine details for pharmacy
            $enrichedMedicines = null;
            if ($request->prescribed_medicines && is_array($request->prescribed_medicines)) {
                $enrichedMedicines = [];
                foreach ($request->prescribed_medicines as $prescribed) {
                    if (!empty($prescribed['medicine_id'])) {
                        $medicine = Medicine::find($prescribed['medicine_id']);
                        if ($medicine) {
                            $enrichedMedicines[] = [
                                'medicine_id' => $medicine->id,
                                'name' => $medicine->name,
                                'quantity' => $prescribed['quantity'] ?? 1,
                                'unit' => $medicine->unit ?? 'pcs',
                                'dosage' => $prescribed['dosage'] ?? '',
                                'frequency' => $prescribed['frequency'] ?? '',
                                'duration' => $prescribed['duration'] ?? '',
                                'instructions' => $prescribed['instructions'] ?? '',
                            ];
                        }
                    }
                }
            }
            
            // Create or update consultation
            $consultation = Consultation::updateOrCreate(
                ['queue_id' => $queueId],
                [
                    'patient_id' => $queue->patient_id,
                    'doctor_id' => Auth::id(),
                    'chief_complaint' => $request->chief_complaint,
                    'blood_pressure' => $request->blood_pressure,
                    'temperature' => $request->temperature,
                    'pulse_rate' => $request->pulse_rate,
                    'weight' => $request->weight,
                    'height' => $request->height,
                    'symptoms' => $request->symptoms,
                    'physical_examination' => $request->physical_examination,
                    'diagnosis' => $request->diagnosis,
                    'treatment' => $request->treatment,
                    'prescribed_medicines' => $enrichedMedicines,
                    'follow_up_date' => $request->follow_up_date,
                    'doctor_notes' => $request->doctor_notes,
                    'prescription_dispensed' => empty($enrichedMedicines) ? true : false,
                ]
            );
            
            // Update queue status to completed
            $queue->update([
                'status' => 'completed',
                'served_at' => now(),
            ]);
            
            // Log activity with quick mode indicator
            $activityDescription = $isQuickMode 
                ? "Dr. " . Auth::user()->name . " completed QUICK consultation for " . $queue->patient->name
                : "Dr. " . Auth::user()->name . " completed consultation for " . $queue->patient->name;
                
            ActivityLog::log(
                'consultation_completed',
                $activityDescription,
                [
                    'queue_id' => $queueId,
                    'patient_id' => $queue->patient_id,
                    'diagnosis' => $request->diagnosis,
                    'quick_mode' => $isQuickMode,
                ]
            );
            
            DB::commit();
            
            $message = $isQuickMode 
                ? 'Quick consultation saved successfully! Patient marked as completed.'
                : 'Consultation saved successfully! Patient marked as completed.';
            
            return redirect()->route('doctor.dashboard')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error saving consultation: ' . $e->getMessage());
        }
    }
    
    /**
     * Show patient's medical history
     */
    public function patientHistory($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        
        // Get all consultations for this patient
        $consultations = Consultation::with(['queue', 'doctor', 'medicinesDispensed'])
            ->where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('doctor.patient-history', compact('patient', 'consultations'));
    }

    /**
     * Print prescription for a consultation
     */
    public function printPrescription($consultationId)
    {
        $consultation = Consultation::with(['patient', 'doctor', 'queue'])->findOrFail($consultationId);
        
        // Verify this consultation belongs to the current doctor or allow viewing
        // (allowing all doctors to view for practical purposes)
        
        $patient = $consultation->patient;
        
        return view('doctor.print-prescription', compact('consultation', 'patient'));
    }
}
