<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class ServiceManagementController extends Controller
{
    /**
     * Show service management dashboard
     */
    public function index()
    {
        $services = Service::with('updatedByStaff')->get();
        
        return view('service-management.index', compact('services'));
    }

    /**
     * Update service availability settings
     */
    public function updateAvailability(Request $request, $serviceId)
    {
        $request->validate([
            'available_today' => 'boolean',
            'unavailable_reason' => 'nullable|string|max:255',
            'daily_patient_limit' => 'nullable|integer|min:1|max:200',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
        ]);

        $service = Service::findOrFail($serviceId);
        
        // Validate that both times are provided together or both empty
        if (($request->filled('start_time') && !$request->filled('end_time')) || 
            (!$request->filled('start_time') && $request->filled('end_time'))) {
            return back()->withErrors(['end_time' => 'Both start time and end time must be provided together.']);
        }
        
        // Validate that end_time is after start_time
        if ($request->filled('start_time') && $request->filled('end_time') && 
            $request->start_time >= $request->end_time) {
            return back()->withErrors(['end_time' => 'End time must be after start time.']);
        }

        $settings = $request->only([
            'available_today',
            'unavailable_reason',
            'daily_patient_limit',
        ]);
        
        // Only include times if both are provided
        if ($request->filled('start_time') && $request->filled('end_time')) {
            $settings['start_time'] = $request->start_time;
            $settings['end_time'] = $request->end_time;
        }

        // If service is being set as unavailable, clear the reason if it's being set as available
        if ($request->boolean('available_today')) {
            $settings['unavailable_reason'] = null;
        }

        $service->updateAvailabilitySettings($settings, Auth::id());

        $statusMessage = $request->boolean('available_today') 
            ? "Service '{$service->name}' has been set as AVAILABLE"
            : "Service '{$service->name}' has been set as UNAVAILABLE";

        // Create notification for all staff
        if ($request->boolean('available_today')) {
            \App\Models\Notification::createForAllStaff(
                'service_status',
                '🟢 Service Available',
                "{$service->name} is now available for patients",
                ['service_id' => $service->id, 'status' => 'available']
            );
        } else {
            \App\Models\Notification::createForAllStaff(
                'service_status',
                '🔴 Service Unavailable',
                "{$service->name} is currently unavailable" . ($settings['unavailable_reason'] ? ": {$settings['unavailable_reason']}" : ""),
                ['service_id' => $service->id, 'status' => 'unavailable', 'reason' => $settings['unavailable_reason'] ?? null]
            );
        }

        return redirect()->route('service-management.index')
            ->with('success', $statusMessage);
    }

    /**
     * Reset daily counters for all services
     */
    public function resetDailyCounters()
    {
        $services = Service::all();
        
        foreach ($services as $service) {
            $service->resetDailyCounters();
        }

        return redirect()->route('service-management.index')
            ->with('success', 'Daily counters reset for all services.');
    }

    /**
     * Quick toggle service availability
     */
    public function quickToggle(Request $request, $serviceId)
    {
        $service = Service::findOrFail($serviceId);
        
        $newStatus = !$service->available_today;
        $reason = $request->input('reason');

        $settings = [
            'available_today' => $newStatus,
            'unavailable_reason' => $newStatus ? null : $reason,
        ];

        $service->updateAvailabilitySettings($settings, Auth::id());

        // Create notification for all staff
        if ($newStatus) {
            \App\Models\Notification::createForAllStaff(
                'service_status',
                '🟢 Service Available',
                "{$service->name} is now available for patients",
                ['service_id' => $service->id, 'status' => 'available']
            );
        } else {
            \App\Models\Notification::createForAllStaff(
                'service_status',
                '🔴 Service Unavailable',
                "{$service->name} is currently unavailable" . ($reason ? ": {$reason}" : ""),
                ['service_id' => $service->id, 'status' => 'unavailable', 'reason' => $reason]
            );
        }

        $statusText = $newStatus ? 'AVAILABLE' : 'UNAVAILABLE';
        
        return response()->json([
            'success' => true,
            'message' => "Service '{$service->name}' is now {$statusText}",
            'status' => $service->getAvailabilityStatus()
        ]);
    }

    /**
     * Get service statistics
     */
    public function getServiceStats($serviceId)
    {
        $service = Service::findOrFail($serviceId);
        $stats = $service->getTodayStats();
        $availability = $service->getAvailabilityStatus();

        return response()->json([
            'stats' => $stats,
            'availability' => $availability,
            'current_count' => $service->current_patient_count,
            'limit' => $service->daily_patient_limit
        ]);
    }
}
