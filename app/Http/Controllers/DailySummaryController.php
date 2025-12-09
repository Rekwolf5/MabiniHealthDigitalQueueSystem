<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Queue;
use App\Models\Medicine;
use App\Models\GeneratedReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DailySummaryController extends Controller
{
    public function view(Request $request)
    {
        $date = $request->input('date', today());
        $reportDate = Carbon::parse($date);
        
        $dailyData = $this->gatherSummaryData($reportDate);
        
        // Log report view
        GeneratedReport::create([
            'report_type' => 'daily_summary',
            'report_date' => $reportDate,
            'generated_by' => Auth::id(),
            'format' => 'web',
        ]);
        
        return view('reports.daily-summary', compact('dailyData'));
    }
    
    public function generate(Request $request)
    {
        $date = $request->input('date', today());
        $reportDate = Carbon::parse($date);
        
        $summaryData = $this->gatherSummaryData($reportDate);
        
        // Log report generation
        GeneratedReport::create([
            'report_type' => 'daily_summary',
            'report_date' => $reportDate,
            'generated_by' => Auth::id(),
            'format' => $request->input('format', 'web'),
        ]);
        
        // For now, just return the view (PDF generation can be added later if needed)
        return redirect()->back()->with('success', 'Report generated successfully!');
    }
    
    private function gatherSummaryData($reportDate)
    {
        // Patient statistics
        $newPatients = Patient::whereDate('created_at', $reportDate)->count();
        $totalPatients = Patient::count();
        $totalConsultations = Queue::whereDate('created_at', $reportDate)
            ->where('status', 'Completed')
            ->count();
        
        // Queue statistics
        $totalQueue = Queue::whereDate('created_at', $reportDate)->count();
        $servedToday = Queue::whereDate('created_at', $reportDate)->where('status', 'Completed')->count();
        $waitingQueue = Queue::whereDate('created_at', $reportDate)->whereIn('status', ['Waiting', 'Consulting'])->count();
        
        // Calculate average wait time
        $completedQueues = Queue::whereDate('created_at', $reportDate)
            ->where('status', 'Completed')
            ->whereNotNull('arrived_at')
            ->whereNotNull('started_at')
            ->get();
        
        $averageWaitTime = 'N/A';
        if ($completedQueues->isNotEmpty()) {
            $totalWaitMinutes = 0;
            $count = 0;
            
            foreach ($completedQueues as $queue) {
                $arrivedAt = Carbon::parse($queue->arrived_at);
                $startedAt = Carbon::parse($queue->started_at);
                if ($startedAt->greaterThan($arrivedAt)) {
                    $totalWaitMinutes += $arrivedAt->diffInMinutes($startedAt);
                    $count++;
                }
            }
            
            if ($count > 0) {
                $avgMinutes = round($totalWaitMinutes / $count);
                if ($avgMinutes < 60) {
                    $averageWaitTime = $avgMinutes . ' minutes';
                } else {
                    $hours = floor($avgMinutes / 60);
                    $mins = $avgMinutes % 60;
                    $averageWaitTime = $hours . ' hr ' . $mins . ' min';
                }
            }
        }
        
        // Medicine statistics
        $totalMedicines = Medicine::count();
        
        $lowStockMedicines = Medicine::all()->filter(function ($med) {
            return in_array($med->status, ['Critical', 'Low Stock']);
        });
        
        $criticalMedicines = Medicine::all()->filter(function ($med) {
            return $med->status === 'Critical' || $med->stock == 0;
        });
        
        $expiringMedicines = Medicine::all()->filter(function ($med) {
            if (!$med->expiry_date) return false;
            $daysUntilExpiry = now()->diffInDays($med->expiry_date, false);
            return $daysUntilExpiry >= 0 && $daysUntilExpiry <= 30;
        });
        
        // Service type breakdown
        $serviceBreakdown = [
            'Consultation' => Queue::whereDate('created_at', $reportDate)->where('service_type', 'Consultation')->count(),
            'Check-up' => Queue::whereDate('created_at', $reportDate)->where('service_type', 'Check-up')->count(),
            'Vaccination' => Queue::whereDate('created_at', $reportDate)->where('service_type', 'Vaccination')->count(),
            'Emergency' => Queue::whereDate('created_at', $reportDate)->where('service_type', 'Emergency')->count(),
        ];
        
        // Priority breakdown
        $priorityBreakdown = [
            'Normal' => Queue::whereDate('created_at', $reportDate)->where('priority', 'Regular')->count(),
            'Urgent' => Queue::whereDate('created_at', $reportDate)->where('priority', 'Priority')->count(),
            'Emergency' => 0, // Keep for compatibility
        ];
        
        return [
            'date' => $reportDate->format('F d, Y'),
            'period' => 'daily',
            'start_date' => $reportDate->format('F d, Y'),
            'end_date' => $reportDate->format('F d, Y'),
            'generated_at' => now()->format('F d, Y h:i A'),
            'patients' => [
                'total_consultations' => $totalConsultations,
                'new_patients' => $newPatients,
                'total_registered' => $totalPatients,
            ],
            'queue' => [
                'total_served' => $servedToday,
                'by_priority' => $priorityBreakdown,
                'by_service' => $serviceBreakdown,
            ],
            'medicines' => [
                'total_medicines' => $totalMedicines,
                'low_stock' => $lowStockMedicines->count(),
                'expired' => $expiringMedicines->count(),
            ],
        ];
    }
}
