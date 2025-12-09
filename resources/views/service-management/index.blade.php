<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Management - Mabini Health Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .status-available { color: #10b981; }
        .status-unavailable { color: #ef4444; }
        .status-full { color: #f59e0b; }
        .status-closed { color: #6b7280; }
        .service-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        .service-card.available { border-left-color: #10b981; }
        .service-card.unavailable { border-left-color: #ef4444; }
        .service-card.full { border-left-color: #f59e0b; }
        .service-card.closed { border-left-color: #6b7280; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <img src="{{ asset('images/health-center-logo.png') }}" alt="Logo" class="h-8 w-8">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Service Management</h1>
                        <p class="text-gray-600">Daily Availability & Capacity Control</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">{{ auth()->user()->name }}</span>
                    <a href="{{ route('staff.dashboard') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Quick Actions -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Quick Actions</h2>
            <div class="flex flex-wrap gap-4">
                <button onclick="resetAllCounters()" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-refresh mr-2"></i>Reset Daily Counters
                </button>
                <button onclick="toggleAllAvailable()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-check-circle mr-2"></i>Set All Available
                </button>
                <button onclick="refreshStats()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-sync mr-2"></i>Refresh Statistics
                </button>
            </div>
        </div>

        <!-- Services Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($services as $service)
                @php
                    $availability = $service->getAvailabilityStatus();
                    $stats = $service->getTodayStats();
                    
                    // Auto-fix: sync current_patient_count with actual today's count
                    if ($service->current_patient_count != $stats['total_patients']) {
                        $service->update(['current_patient_count' => $stats['total_patients']]);
                    }
                @endphp
                <div class="service-card {{ $availability['status'] }} bg-white rounded-lg shadow-lg p-6">
                    <!-- Service Header -->
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">{{ $service->name }}</h3>
                            <p class="text-gray-600 text-sm">{{ $service->description }}</p>
                        </div>
                        <span class="status-{{ $availability['status'] }} text-2xl">
                            @if($availability['status'] === 'available')
                                <i class="fas fa-check-circle"></i>
                            @elseif($availability['status'] === 'full')
                                <i class="fas fa-exclamation-triangle"></i>
                            @elseif($availability['status'] === 'unavailable')
                                <i class="fas fa-times-circle"></i>
                            @else
                                <i class="fas fa-clock"></i>
                            @endif
                        </span>
                    </div>

                    <!-- Status Message -->
                    <div class="mb-4 p-3 rounded-lg bg-gray-50">
                        <p class="status-{{ $availability['status'] }} font-semibold">
                            {{ $availability['message'] }}
                        </p>
                    </div>

                    <!-- Statistics -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ $service->current_patient_count }}</div>
                            <div class="text-sm text-gray-600">Current Count</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">
                                {{ $service->daily_patient_limit ?: '∞' }}
                            </div>
                            <div class="text-sm text-gray-600">Daily Limit</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $stats['waiting'] }}</div>
                            <div class="text-sm text-gray-600">Active Queue</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $stats['completed'] }}</div>
                            <div class="text-sm text-gray-600">Completed</div>
                        </div>
                    </div>

                    <!-- Operating Hours -->
                    @if($service->start_time && $service->end_time)
                        <div class="mb-4 text-sm text-gray-600">
                            <i class="fas fa-clock mr-2"></i>
                            {{ $service->start_time->format('g:i A') }} - {{ $service->end_time->format('g:i A') }}
                        </div>
                    @endif

                    <!-- Quick Toggle -->
                    <div class="flex space-x-2 mb-4">
                        <button onclick="quickToggle({{ $service->id }}, '{{ $service->available_today ? 'disable' : 'enable' }}')"
                                class="flex-1 {{ $service->available_today ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white px-3 py-2 rounded-lg text-sm">
                            @if($service->available_today)
                                <i class="fas fa-pause mr-1"></i>Disable
                            @else
                                <i class="fas fa-play mr-1"></i>Enable
                            @endif
                        </button>
                        <button onclick="showSettingsModal({{ $service->id }})"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm">
                            <i class="fas fa-cog mr-1"></i>Settings
                        </button>
                    </div>

                    <!-- Last Updated -->
                    @if($service->settings_updated_date && $service->updatedByStaff)
                        <div class="text-xs text-gray-500">
                            Updated {{ $service->settings_updated_date->format('M j') }} by {{ $service->updatedByStaff->name }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Settings Modal (populated by JavaScript) -->
    <div id="settingsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Service Settings</h3>
                <button onclick="hideSettingsModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="settingsContent">
                <!-- Content populated by JavaScript -->
            </div>
        </div>
    </div>

    <script>
        let currentServiceId = null;

        function quickToggle(serviceId, action) {
            const reason = action === 'disable' ? prompt('Reason for disabling service (optional):') : '';
            
            if (action === 'disable' && reason === null) return; // User cancelled
            
            fetch(`/service-management/${serviceId}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ reason: reason })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }

        function showSettingsModal(serviceId) {
            currentServiceId = serviceId;
            const modal = document.getElementById('settingsModal');
            const content = document.getElementById('settingsContent');
            
            // Get service data
            const serviceCard = document.querySelector(`[onclick*="${serviceId}"]`).closest('.service-card');
            const serviceName = serviceCard.querySelector('h3').textContent;
            
            content.innerHTML = `
                <form action="/service-management/${serviceId}/availability" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    
                    <div class="mb-4">
                        <h4 class="font-semibold text-gray-900 mb-2">${serviceName}</h4>
                    </div>
                    
                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="available_today" value="1" class="mr-2">
                            Service Available Today
                        </label>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Unavailable Reason (if not available)
                        </label>
                        <input type="text" name="unavailable_reason" class="w-full border rounded-lg px-3 py-2"
                               placeholder="e.g., No doctor available, Equipment maintenance">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Daily Patient Limit
                        </label>
                        <input type="number" name="daily_patient_limit" class="w-full border rounded-lg px-3 py-2"
                               min="1" max="200" placeholder="Leave blank for no limit">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                            <input type="time" name="start_time" class="w-full border rounded-lg px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                            <input type="time" name="end_time" class="w-full border rounded-lg px-3 py-2">
                        </div>
                    </div>
                    
                    <div class="flex space-x-3">
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg">
                            Save Settings
                        </button>
                        <button type="button" onclick="hideSettingsModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 rounded-lg">
                            Cancel
                        </button>
                    </div>
                </form>
            `;
            
            modal.classList.remove('hidden');
        }

        function hideSettingsModal() {
            document.getElementById('settingsModal').classList.add('hidden');
        }

        function resetAllCounters() {
            if (confirm('Reset daily counters for all services? This will set all patient counts to 0.')) {
                fetch('/service-management/reset-counters', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(() => location.reload());
            }
        }

        function refreshStats() {
            location.reload();
        }

        // Auto-refresh every 60 seconds
        setInterval(refreshStats, 60000);
    </script>
</body>
</html>