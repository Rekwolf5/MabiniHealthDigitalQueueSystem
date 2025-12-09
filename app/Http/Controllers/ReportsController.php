<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FrontDeskQueue;
use App\Models\Service;
use App\Models\Medicine;
use App\Models\GeneratedReport;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ReportsController extends Controller
{
    public function index()
    {
        // Get today's queue statistics
        $todayQueues = FrontDeskQueue::whereDate('arrived_at', today());
        $thisWeekQueues = FrontDeskQueue::whereBetween('arrived_at', [now()->startOfWeek(), now()->endOfWeek()]);
        $thisMonthQueues = FrontDeskQueue::whereMonth('arrived_at', now()->month);
        
        $reportStats = [
            'total_patients' => $todayQueues->count(),
            'consultations_today' => \App\Models\QueueArchive::whereDate('archived_at', today())
                ->where('status', 'completed')->count(),
            'medicines_dispensed' => 0, // Medicine management removed from system
            'queue_average_wait' => $this->calculateTodayAverageWait(),
            'patients_this_week' => $thisWeekQueues->count(),
            'patients_this_month' => $thisMonthQueues->count(),
            'busiest_service' => $this->getBusiestService(),
            'capacity_utilization' => $this->calculateCapacityUtilization()
        ];

        // Service performance data
        $services = Service::where('is_active', true)->get();
        $serviceStats = [];
        foreach ($services as $service) {
            $todayCount = FrontDeskQueue::whereDate('arrived_at', today())
                ->where('service_id', $service->id)->count();
            $completedCount = \App\Models\QueueArchive::whereDate('archived_at', today())
                ->where('service_id', $service->id)->where('status', 'completed')->count();
                
            $serviceStats[] = [
                'name' => $service->name,
                'today_count' => $todayCount,
                'completed_count' => $completedCount,
                'completion_rate' => $todayCount > 0 ? round(($completedCount / $todayCount) * 100) : 0,
                'current_count' => $service->current_patient_count,
                'limit' => $service->daily_patient_limit,
                'utilization' => $service->daily_patient_limit ? 
                    round(($service->current_patient_count / $service->daily_patient_limit) * 100) : 0
            ];
        }

        // Recent reports - sample data for now
        $recentReports = [
            [
                'title' => 'Daily Patient Report',
                'type' => 'Patients',
                'date' => now()->subDays(1)->format('M d, Y'),
                'status' => 'Completed'
            ],
            [
                'title' => 'Weekly Queue Analysis',
                'type' => 'Queue',
                'date' => now()->subDays(3)->format('M d, Y'),
                'status' => 'Completed'
            ],
            [
                'title' => 'Medicine Inventory',
                'type' => 'Medicine',
                'date' => now()->subDays(7)->format('M d, Y'),
                'status' => 'Completed'
            ]
        ];

        return view('reports.index', compact('reportStats', 'serviceStats', 'recentReports'));
    }
    
    private function calculateTodayAverageWait()
    {
        $completedQueues = FrontDeskQueue::whereDate('arrived_at', today())
            ->where('status', 'completed')
            ->whereNotNull('arrived_at')
            ->whereNotNull('called_at')
            ->get();
        
        if ($completedQueues->isEmpty()) {
            return '0 mins';
        }
        
        $totalWaitMinutes = 0;
        $count = 0;
        
        foreach ($completedQueues as $queue) {
            $arrivedAt = Carbon::parse($queue->arrived_at);
            $calledAt = Carbon::parse($queue->called_at);
            if ($calledAt->greaterThan($arrivedAt)) {
                $totalWaitMinutes += $arrivedAt->diffInMinutes($calledAt);
                $count++;
            }
        }
        
        if ($count === 0) {
            return '0 mins';
        }
        
        $avgMinutes = round($totalWaitMinutes / $count);
        return $avgMinutes . ' mins';
    }
    
    private function getBusiestService()
    {
        $service = Service::withCount([
            'frontDeskQueues' => function($query) {
                $query->whereDate('arrived_at', today());
            }
        ])->orderBy('front_desk_queues_count', 'desc')->first();
        
        return $service ? $service->name : 'No data';
    }
    
    private function calculateCapacityUtilization()
    {
        $services = Service::where('is_active', true)
            ->whereNotNull('daily_patient_limit')
            ->where('daily_patient_limit', '>', 0)
            ->get();
            
        if ($services->isEmpty()) {
            return '0%';
        }
        
        $totalCapacity = $services->sum('daily_patient_limit');
        $totalUsed = $services->sum('current_patient_count');
        
        return $totalCapacity > 0 ? round(($totalUsed / $totalCapacity) * 100) . '%' : '0%';
    }

    public function patients(Request $request)
    {
        $period = $request->query('period', 'daily');
        $startDate = Carbon::now()->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        if ($period === 'weekly') {
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
        } elseif ($period === 'monthly') {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        }

        // Get queue data for the period (representing unique patients)
        $periodQueues = FrontDeskQueue::whereBetween('arrived_at', [$startDate, $endDate]);
        $totalQueues = FrontDeskQueue::count();
        
        $patientData = [
            'total_visits' => $totalQueues,
            'period_visits' => $periodQueues->count(),
            'completed_visits' => $periodQueues->where('status', 'completed')->count(),
            'by_age_group' => [
                '0-18' => FrontDeskQueue::whereBetween('arrived_at', [$startDate, $endDate])->where('age', '<=', 18)->count(),
                '19-35' => FrontDeskQueue::whereBetween('arrived_at', [$startDate, $endDate])->whereBetween('age', [19, 35])->count(),
                '36-50' => FrontDeskQueue::whereBetween('arrived_at', [$startDate, $endDate])->whereBetween('age', [36, 50])->count(),
                '51+' => FrontDeskQueue::whereBetween('arrived_at', [$startDate, $endDate])->where('age', '>', 50)->count(),
                'Unknown' => FrontDeskQueue::whereBetween('arrived_at', [$startDate, $endDate])->whereNull('age')->count(),
            ],
            'by_priority' => [
                'Emergency' => FrontDeskQueue::whereBetween('arrived_at', [$startDate, $endDate])->where('priority', 'emergency')->count(),
                'Senior Citizen' => FrontDeskQueue::whereBetween('arrived_at', [$startDate, $endDate])->where('priority', 'senior')->count(),
                'PWD' => FrontDeskQueue::whereBetween('arrived_at', [$startDate, $endDate])->where('priority', 'pwd')->count(),
                'Regular' => FrontDeskQueue::whereBetween('arrived_at', [$startDate, $endDate])->where('priority', 'normal')->count(),
            ],
            'by_service' => $this->getServiceBreakdown($startDate, $endDate),
            'common_complaints' => $this->getCommonComplaints($startDate, $endDate),
            'period' => $period,
            'start_date' => $startDate->format('M d, Y'),
            'end_date' => $endDate->format('M d, Y'),
            'generated_at' => now()->format('M d, Y h:i A'),
        ];

        if ($request->query('export') === 'pdf') {
            return $this->exportPatientsPDF($patientData);
        }

        return view('reports.patients', compact('patientData'));
    }
    
    private function getServiceBreakdown($startDate, $endDate)
    {
        $services = Service::where('is_active', true)->get();
        $breakdown = [];
        
        foreach ($services as $service) {
            $count = FrontDeskQueue::whereBetween('arrived_at', [$startDate, $endDate])
                ->where('service_id', $service->id)->count();
            $breakdown[$service->name] = $count;
        }
        
        // Add unassigned
        $unassigned = FrontDeskQueue::whereBetween('arrived_at', [$startDate, $endDate])
            ->whereNull('service_id')->count();
        if ($unassigned > 0) {
            $breakdown['Unassigned'] = $unassigned;
        }
        
        return $breakdown;
    }
    
    private function getCommonComplaints($startDate, $endDate)
    {
        return FrontDeskQueue::whereBetween('arrived_at', [$startDate, $endDate])
            ->whereNotNull('chief_complaint')
            ->groupBy('chief_complaint')
            ->selectRaw('chief_complaint, COUNT(*) as count')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->pluck('count', 'chief_complaint')
            ->toArray();
    }

    public function queue(Request $request)
    {
        $period = $request->query('period', 'daily');
        $startDate = Carbon::now()->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        if ($period === 'weekly') {
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
        } elseif ($period === 'monthly') {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        }

        $periodQueues = FrontDeskQueue::whereBetween('arrived_at', [$startDate, $endDate]);
        
        // Generate chart data
        $chartData = [];
        if ($period === 'daily') {
            for ($hour = 8; $hour <= 17; $hour++) {
                $hourStart = Carbon::parse($startDate)->setHour($hour)->setMinute(0)->setSecond(0);
                $hourEnd = Carbon::parse($startDate)->setHour($hour)->setMinute(59)->setSecond(59);
                
                $count = FrontDeskQueue::whereBetween('arrived_at', [$hourStart, $hourEnd])->count();
                $chartData[] = [
                    'label' => $hourStart->format('h A'),
                    'value' => $count
                ];
            }
        } elseif ($period === 'weekly') {
            for ($i = 0; $i < 7; $i++) {
                $day = Carbon::parse($startDate)->addDays($i);
                $dayStart = $day->startOfDay();
                $dayEnd = $day->endOfDay();
                
                $count = FrontDeskQueue::whereBetween('arrived_at', [$dayStart, $dayEnd])->count();
                $chartData[] = [
                    'label' => $day->format('D'),
                    'value' => $count
                ];
            }
        } elseif ($period === 'monthly') {
            $weeksInMonth = ceil($startDate->daysInMonth / 7);
            for ($i = 0; $i < $weeksInMonth; $i++) {
                $weekStart = Carbon::parse($startDate)->addWeeks($i);
                $weekEnd = $weekStart->copy()->endOfWeek();
                
                $count = FrontDeskQueue::whereBetween('arrived_at', [$weekStart, $weekEnd])->count();
                $chartData[] = [
                    'label' => 'Week ' . ($i + 1),
                    'value' => $count
                ];
            }
        }
        
        $queueData = [
            'total_patients' => $periodQueues->count(),
            'completed_patients' => $periodQueues->where('status', 'completed')->count(),
            'cancelled_patients' => $periodQueues->where('status', 'cancelled')->count(),
            'average_wait_time' => $this->calculateAverageWaitTime($startDate, $endDate),
            'peak_hours' => $this->calculatePeakHours($startDate, $endDate),
            'completion_rate' => $this->calculateCompletionRate($startDate, $endDate),
            'by_priority' => [
                'Emergency' => $periodQueues->where('priority', 'emergency')->count(),
                'Senior Citizen' => $periodQueues->where('priority', 'senior')->count(),
                'PWD' => $periodQueues->where('priority', 'pwd')->count(),
                'Regular' => $periodQueues->where('priority', 'normal')->count(),
            ],
            'by_service' => $this->getServiceBreakdown($startDate, $endDate),
            'by_status' => [
                'Completed' => $periodQueues->where('status', 'completed')->count(),
                'In Progress' => $periodQueues->where('status', 'in_progress')->count(),
                'Called' => $periodQueues->where('status', 'called')->count(),
                'Waiting' => $periodQueues->where('status', 'waiting')->count(),
                'Cancelled' => $periodQueues->where('status', 'cancelled')->count(),
            ],
            'chart_data' => $chartData,
            'period' => $period,
            'start_date' => $startDate->format('M d, Y'),
            'end_date' => $endDate->format('M d, Y'),
            'generated_at' => now()->format('M d, Y h:i A'),
        ];

        if ($request->query('export') === 'pdf') {
            return $this->exportQueuePDF($queueData);
        }

        return view('reports.queue', compact('queueData'));
    }

    // Calculate average wait time based on arrived_at and started_at
    private function calculateAverageWaitTime($startDate, $endDate)
    {
        $queues = FrontDeskQueue::whereBetween('arrived_at', [$startDate, $endDate])
            ->whereNotNull('arrived_at')
            ->whereNotNull('called_at')
            ->where('status', 'completed')
            ->get();
        
        if ($queues->isEmpty()) {
            return 'No data yet';
        }
        
        $totalWaitMinutes = 0;
        $count = 0;
        
        foreach ($queues as $queue) {
            $arrivedAt = Carbon::parse($queue->arrived_at);
            $calledAt = Carbon::parse($queue->called_at);
            
            if ($calledAt->greaterThan($arrivedAt)) {
                $waitMinutes = $arrivedAt->diffInMinutes($calledAt);
                $totalWaitMinutes += $waitMinutes;
                $count++;
            }
        }
        
        if ($count === 0) {
            return 'No data yet';
        }
        
        $averageMinutes = round($totalWaitMinutes / $count);
        
        if ($averageMinutes < 60) {
            return $averageMinutes . ' minutes';
        } else {
            $hours = floor($averageMinutes / 60);
            $mins = $averageMinutes % 60;
            return $hours . ' hr ' . $mins . ' min';
        }
    }

    // Update calculatePeakHours to use the date range parameter
    private function calculatePeakHours($startDate, $endDate)
    {
        $hourCounts = [];
        
        for ($hour = 8; $hour <= 17; $hour++) {
            // Use the startDate for the base date instead of always using today
            $hourStart = Carbon::parse($startDate)->setHour($hour)->setMinute(0)->setSecond(0);
            $hourEnd = Carbon::parse($startDate)->setHour($hour)->setMinute(59)->setSecond(59);
            
            $count = FrontDeskQueue::whereBetween('arrived_at', [$hourStart, $hourEnd])->count();
            $hourCounts[$hour] = $count;
        }
        
        if (empty($hourCounts) || max($hourCounts) === 0) {
            return 'No data yet';
        }
        
        $peakHour = array_search(max($hourCounts), $hourCounts);
        return Carbon::parse($startDate)->setHour($peakHour)->format('h A') . ' - ' . 
               Carbon::parse($startDate)->setHour($peakHour + 1)->format('h A');
    }

    public function medicines(Request $request)
    {
        $period = $request->query('period', 'monthly');
        
        $medicines = Medicine::all();
        
        $totalValue = $medicines->sum(function ($med) {
            return $med->stock * ($med->unit_price ?? 0);
        });
        
        // Three-tier stock counting
        $criticalStockCount = $medicines->filter(function ($med) {
            return $med->stock > 0 && $med->stock <= 5;
        })->count();
        
        $lowStockCount = $medicines->filter(function ($med) {
            return $med->stock > 5 && $med->stock <= ($med->reorder_level ?? 15);
        })->count();
        
        $outOfStockCount = $medicines->filter(function ($med) {
            return $med->stock == 0;
        })->count();
        
        $expiredCount = $medicines->filter(function ($med) {
            if (!$med->expiry_date) return false;
            return \Carbon\Carbon::parse($med->expiry_date)->isPast();
        })->count();
        
        $expiringSoonCount = $medicines->filter(function ($med) {
            if (!$med->expiry_date) return false;
            $expiryDate = \Carbon\Carbon::parse($med->expiry_date);
            $daysUntilExpiry = now()->diffInDays($expiryDate, false);
            return $daysUntilExpiry >= 0 && $daysUntilExpiry <= 30;
        })->count();
        
        // Stock levels distribution
        $adequateStock = $medicines->filter(function ($med) {
            return $med->stock > ($med->reorder_level ?? 15);
        })->count();
        
        // Expiry alerts breakdown
        $expiringIn7Days = $medicines->filter(function ($med) {
            if (!$med->expiry_date) return false;
            $expiryDate = \Carbon\Carbon::parse($med->expiry_date);
            $daysUntilExpiry = now()->diffInDays($expiryDate, false);
            return $daysUntilExpiry >= 0 && $daysUntilExpiry <= 7;
        })->count();
        
        $expiringIn30Days = $medicines->filter(function ($med) {
            if (!$med->expiry_date) return false;
            $expiryDate = \Carbon\Carbon::parse($med->expiry_date);
            $daysUntilExpiry = now()->diffInDays($expiryDate, false);
            return $daysUntilExpiry > 7 && $daysUntilExpiry <= 30;
        })->count();
        
        $expiringIn90Days = $medicines->filter(function ($med) {
            if (!$med->expiry_date) return false;
            $expiryDate = \Carbon\Carbon::parse($med->expiry_date);
            $daysUntilExpiry = now()->diffInDays($expiryDate, false);
            return $daysUntilExpiry > 30 && $daysUntilExpiry <= 90;
        })->count();
        
        $medicineData = [
            'total_medicines' => $medicines->count(),
            'total_items' => $medicines->count(),
            'low_stock_items' => $lowStockCount + $criticalStockCount, // Combined
            'critical_stock_items' => $criticalStockCount, // New
            'low_stock_count' => $lowStockCount,
            'expired_items' => $expiredCount,
            'expiring_soon_count' => $expiringSoonCount,
            'total_value' => 'PHP ' . number_format($totalValue, 2),
            'stock_levels' => [
                'Adequate Stock' => $adequateStock,
                'Low Stock' => $lowStockCount,
                'Critical' => $criticalStockCount,
                'Out of Stock' => $outOfStockCount,
            ],
            'expiry_alerts' => [
                'Expiring in 7 Days' => $expiringIn7Days,
                'Expiring in 30 Days' => $expiringIn30Days,
                'Expiring in 90 Days' => $expiringIn90Days,
                'Expired' => $expiredCount,
            ],
            'medicines' => $medicines->map(function ($med) {
                return [
                    'name' => $med->name,
                    'stock' => $med->stock,
                    'unit_price' => $med->unit_price ?? 0,
                    'total_value' => $med->stock * ($med->unit_price ?? 0),
                    'expiry_date' => $med->expiry_date,
                    'reorder_level' => $med->reorder_level ?? 15,
                    'status' => $med->status,
                ];
            }),
            'period' => $period,
            'generated_at' => now()->format('M d, Y h:i A'),
        ];

        if ($request->query('export') === 'pdf') {
            return $this->exportMedicinesPDF($medicineData);
        }

        return view('reports.medicines', compact('medicineData'));
    }

    private function exportPatientsPDF($patientData)
    {
        $pdf = Pdf::loadView('reports.pdf.patients', compact('patientData'))
            ->setPaper('a4')
            ->setOption('margin-top', 15)
            ->setOption('margin-bottom', 15)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10);

        return $pdf->download('patients-report-' . Carbon::now()->format('Y-m-d') . '.pdf');
    }

    private function calculateCompletionRate($startDate, $endDate)
    {
        $totalQueues = FrontDeskQueue::whereBetween('arrived_at', [$startDate, $endDate])->count();
        
        if ($totalQueues === 0) {
            return 0;
        }
        
        $completedQueues = FrontDeskQueue::whereBetween('arrived_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->count();
        
        return round(($completedQueues / $totalQueues) * 100, 1);
    }

    private function exportQueuePDF($queueData)
    {
        $pdf = Pdf::loadView('reports.pdf.queue', compact('queueData'))
            ->setPaper('a4')
            ->setOption('margin-top', 15)
            ->setOption('margin-bottom', 15)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10);

        return $pdf->download('queue-report-' . Carbon::now()->format('Y-m-d') . '.pdf');
    }

    private function exportMedicinesPDF($medicineData)
    {
        $pdf = Pdf::loadView('reports.pdf.medicine', compact('medicineData'));
        return $pdf->download('medicine-report-' . date('Y-m-d') . '.pdf');
    }

    public function generate(Request $request)
    {
        $reportType = $request->input('type', 'daily');
        $category = $request->input('category', 'patients');
        
        return redirect()->route('reports.index')->with('success', 'Report generated successfully!');
    }
}
