<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Queue;
use App\Models\Medicine;
use App\Models\GeneratedReport;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Enhanced Stats
        $stats = [
            'patients_today' => \App\Models\FrontDeskQueue::whereDate('created_at', today())->count(),
            'queue_waiting' => \App\Models\FrontDeskQueue::whereDate('created_at', today())
                ->where('status', 'waiting')
                ->whereIn('workflow_stage', ['registration', 'vitals', 'consultation'])
                ->count(),
            'queue_called' => \App\Models\FrontDeskQueue::whereDate('created_at', today())
                ->where('status', 'called')
                ->count(),
            'queue_in_progress' => \App\Models\FrontDeskQueue::whereDate('created_at', today())
                ->where('status', 'in_progress')
                ->count(),
            'queue_completed' => \App\Models\QueueArchive::whereDate('archived_at', today())
                ->where('status', 'completed')
                ->count(),
            'queue_no_show' => \App\Models\QueueArchive::whereDate('archived_at', today())
                ->where('status', 'no_show')
                ->count(),
            'reports_generated' => GeneratedReport::whereDate('created_at', today())->count(),
        ];

        // Service Performance Today
        $serviceStats = \App\Models\Service::with(['frontDeskQueues' => function($q) {
            $q->whereDate('created_at', today());
        }])->get()->map(function($service) {
            $queues = $service->frontDeskQueues;
            return [
                'name' => $service->name,
                'total' => $queues->count(),
                'waiting' => $queues->where('status', 'waiting')->count(),
                'in_progress' => $queues->where('status', 'in_progress')->count(),
                'completed' => \App\Models\QueueArchive::where('service_id', $service->id)
                    ->whereDate('archived_at', today())
                    ->where('status', 'completed')
                    ->count(),
            ];
        });

        // Priority Distribution Today
        $priorityStats = \App\Models\FrontDeskQueue::whereDate('created_at', today())
            ->selectRaw('priority, count(*) as count')
            ->groupBy('priority')
            ->get()
            ->pluck('count', 'priority');

        // Recent Queue Activity (last 10)
        $recentActivity = \App\Models\FrontDeskQueue::with('service')
            ->whereDate('created_at', today())
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get()
            ->map(function($queue) {
                return [
                    'queue_number' => $queue->queue_number,
                    'patient_name' => $queue->patient_name,
                    'service' => $queue->service->name ?? 'N/A',
                    'status' => $queue->status,
                    'priority' => $queue->priority,
                    'time' => $queue->updated_at->format('h:i A'),
                    'workflow_stage' => $queue->workflow_stage,
                ];
            });

        // Hourly Queue Trend (for graph)
        $hourlyTrend = \App\Models\FrontDeskQueue::whereDate('created_at', today())
            ->selectRaw('HOUR(created_at) as hour, count(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->pluck('count', 'hour');

        // Staff Activity Summary
        $staffActivity = \App\Models\User::where('role', '!=', 'admin')
            ->withCount(['assignedQueues as queues_handled' => function($q) {
                $q->whereDate('created_at', today());
            }])
            ->having('queues_handled', '>', 0)
            ->orderBy('queues_handled', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'stats', 
            'serviceStats', 
            'priorityStats', 
            'recentActivity', 
            'hourlyTrend',
            'staffActivity'
        ));
    }
}
