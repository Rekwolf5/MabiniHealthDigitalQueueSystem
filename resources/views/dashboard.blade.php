@extends('layouts.app')

@section('title', 'Admin Dashboard - Mabini Health Center')
@section('page-title', 'System Overview Dashboard')

@section('content')
<style>
.dashboard-overview {
    padding: 20px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border-left: 4px solid #059669;
    transition: transform 0.2s;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.stat-card.patients { border-left-color: #059669; }
.stat-card.waiting { border-left-color: #f59e0b; }
.stat-card.called { border-left-color: #3b82f6; }
.stat-card.in-progress { border-left-color: #8b5cf6; }
.stat-card.completed { border-left-color: #10b981; }
.stat-card.no-show { border-left-color: #ef4444; }
.stat-card.medicines { border-left-color: #ec4899; }
.stat-card.reports { border-left-color: #6366f1; }

.stat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
}

.stat-card.patients .stat-icon { background: #059669; }
.stat-card.waiting .stat-icon { background: #f59e0b; }
.stat-card.called .stat-icon { background: #3b82f6; }
.stat-card.in-progress .stat-icon { background: #8b5cf6; }
.stat-card.completed .stat-icon { background: #10b981; }
.stat-card.no-show .stat-icon { background: #ef4444; }
.stat-card.medicines .stat-icon { background: #ec4899; }
.stat-card.reports .stat-icon { background: #6366f1; }

.stat-value {
    font-size: 32px;
    font-weight: bold;
    color: #1f2937;
    margin: 10px 0 5px 0;
}

.stat-label {
    font-size: 14px;
    color: #6b7280;
    font-weight: 500;
}

.dashboard-sections {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.section-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.section-title {
    font-size: 18px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-title i {
    color: #059669;
}

.service-item {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr 1fr;
    gap: 15px;
    padding: 15px;
    border-bottom: 1px solid #e5e7eb;
    align-items: center;
}

.service-item:last-child {
    border-bottom: none;
}

.service-name {
    font-weight: 600;
    color: #1f2937;
}

.service-stat {
    text-align: center;
}

.service-stat-value {
    font-size: 20px;
    font-weight: bold;
    display: block;
}

.service-stat-label {
    font-size: 11px;
    color: #6b7280;
    text-transform: uppercase;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 12px;
    border-bottom: 1px solid #f3f4f6;
    transition: background 0.2s;
}

.activity-item:hover {
    background: #f9fafb;
}

.activity-time {
    font-size: 12px;
    color: #6b7280;
    min-width: 60px;
}

.activity-content {
    flex: 1;
}

.activity-queue {
    font-weight: 600;
    color: #059669;
}

.activity-patient {
    color: #1f2937;
    font-weight: 500;
}

.activity-service {
    color: #6b7280;
    font-size: 13px;
}

.status-badge {
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-waiting { background: #fef3c7; color: #92400e; }
.status-called { background: #dbeafe; color: #1e40af; }
.status-in_progress { background: #ede9fe; color: #5b21b6; }

.priority-badge {
    padding: 2px 8px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 600;
}

.priority-emergency { background: #fee2e2; color: #991b1b; }
.priority-senior { background: #f3e8ff; color: #7c3aed; }
.priority-pwd { background: #dbeafe; color: #1e40af; }
.priority-pregnant { background: #fce7f3; color: #be185d; }
.priority-normal { background: #f3f4f6; color: #374151; }

.medicine-item {
    display: flex;
    justify-content: space-between;
    padding: 10px;
    border-bottom: 1px solid #f3f4f6;
}

.medicine-name {
    font-weight: 600;
    color: #1f2937;
}

.medicine-stock {
    color: #ef4444;
    font-weight: 600;
}

.staff-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    border-bottom: 1px solid #f3f4f6;
}

.staff-name {
    font-weight: 600;
    color: #1f2937;
}

.staff-count {
    background: #059669;
    color: white;
    padding: 4px 12px;
    border-radius: 12px;
    font-weight: 600;
}

.priority-distribution {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.priority-item {
    flex: 1;
    min-width: 100px;
    padding: 10px;
    border-radius: 8px;
    text-align: center;
}

.priority-count {
    font-size: 24px;
    font-weight: bold;
    display: block;
}

.priority-label {
    font-size: 12px;
    color: #6b7280;
}

.refresh-notice {
    background: #f0fdf4;
    border: 1px solid #86efac;
    color: #166534;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.full-width {
    grid-column: 1 / -1;
}

@media (max-width: 968px) {
    .dashboard-sections {
        grid-template-columns: 1fr;
    }
    
    .service-item {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    
    .service-stat {
        text-align: left;
    }
}
</style>

<div class="dashboard-overview">
    <div class="refresh-notice">
        <i class="fas fa-sync-alt"></i>
        <span>Dashboard auto-refreshes every 30 seconds to show real-time data</span>
    </div>

    <!-- Main Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card patients">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stat-value">{{ $stats['patients_today'] }}</div>
            <div class="stat-label">Patients Today</div>
        </div>

        <div class="stat-card waiting">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="stat-value">{{ $stats['queue_waiting'] }}</div>
            <div class="stat-label">Waiting</div>
        </div>

        <div class="stat-card called">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-bell"></i>
                </div>
            </div>
            <div class="stat-value">{{ $stats['queue_called'] }}</div>
            <div class="stat-label">Called</div>
        </div>

        <div class="stat-card in-progress">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-user-md"></i>
                </div>
            </div>
            <div class="stat-value">{{ $stats['queue_in_progress'] }}</div>
            <div class="stat-label">In Progress</div>
        </div>

        <div class="stat-card completed">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="stat-value">{{ $stats['queue_completed'] }}</div>
            <div class="stat-label">Completed</div>
        </div>

        <div class="stat-card no-show">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-user-times"></i>
                </div>
            </div>
            <div class="stat-value">{{ $stats['queue_no_show'] }}</div>
            <div class="stat-label">No Show</div>
        </div>

        <div class="stat-card reports">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
            </div>
            <div class="stat-value">{{ $stats['reports_generated'] }}</div>
            <div class="stat-label">Reports Today</div>
        </div>
    </div>

    <!-- Service Performance Section -->
    <div class="section-card" style="margin-bottom: 20px;">
        <div class="section-title">
            <i class="fas fa-hospital"></i>
            Service Performance Today
        </div>
        
        <div class="service-item" style="background: #f9fafb; font-weight: 600; font-size: 12px; color: #6b7280;">
            <div>SERVICE</div>
            <div class="service-stat">TOTAL</div>
            <div class="service-stat">WAITING</div>
            <div class="service-stat">IN PROGRESS</div>
            <div class="service-stat">COMPLETED</div>
        </div>
        
        @foreach($serviceStats as $service)
        <div class="service-item">
            <div class="service-name">{{ $service['name'] }}</div>
            <div class="service-stat">
                <span class="service-stat-value" style="color: #1f2937;">{{ $service['total'] }}</span>
            </div>
            <div class="service-stat">
                <span class="service-stat-value" style="color: #f59e0b;">{{ $service['waiting'] }}</span>
            </div>
            <div class="service-stat">
                <span class="service-stat-value" style="color: #8b5cf6;">{{ $service['in_progress'] }}</span>
            </div>
            <div class="service-stat">
                <span class="service-stat-value" style="color: #10b981;">{{ $service['completed'] }}</span>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Priority Distribution & Activity -->
    <div class="dashboard-sections">
        <!-- Recent Activity -->
        <div class="section-card">
            <div class="section-title">
                <i class="fas fa-stream"></i>
                Real-Time Queue Activity
            </div>
            
            @if($recentActivity->isEmpty())
                <p style="color: #6b7280; text-align: center; padding: 20px;">No activity yet today</p>
            @else
                @foreach($recentActivity as $activity)
                <div class="activity-item">
                    <div class="activity-time">{{ $activity['time'] }}</div>
                    <div class="activity-content">
                        <div>
                            <span class="activity-queue">{{ $activity['queue_number'] }}</span>
                            <span class="activity-patient">{{ $activity['patient_name'] }}</span>
                        </div>
                        <div class="activity-service">{{ $activity['service'] }}</div>
                    </div>
                    <span class="status-badge status-{{ $activity['status'] }}">{{ $activity['status'] }}</span>
                    <span class="priority-badge priority-{{ $activity['priority'] }}">{{ ucfirst($activity['priority']) }}</span>
                </div>
                @endforeach
            @endif
        </div>

        <!-- Side Panel -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <!-- Priority Distribution -->
            <div class="section-card">
                <div class="section-title">
                    <i class="fas fa-flag"></i>
                    Priority Distribution
                </div>
                
                <div class="priority-distribution">
                    <div class="priority-item" style="background: #fee2e2;">
                        <span class="priority-count" style="color: #991b1b;">{{ $priorityStats['emergency'] ?? 0 }}</span>
                        <span class="priority-label">Emergency</span>
                    </div>
                    <div class="priority-item" style="background: #f3e8ff;">
                        <span class="priority-count" style="color: #7c3aed;">{{ $priorityStats['senior'] ?? 0 }}</span>
                        <span class="priority-label">Senior</span>
                    </div>
                    <div class="priority-item" style="background: #dbeafe;">
                        <span class="priority-count" style="color: #1e40af;">{{ $priorityStats['pwd'] ?? 0 }}</span>
                        <span class="priority-label">PWD</span>
                    </div>
                    <div class="priority-item" style="background: #fce7f3;">
                        <span class="priority-count" style="color: #be185d;">{{ $priorityStats['pregnant'] ?? 0 }}</span>
                        <span class="priority-label">Pregnant</span>
                    </div>
                    <div class="priority-item" style="background: #f3f4f6;">
                        <span class="priority-count" style="color: #374151;">{{ $priorityStats['normal'] ?? 0 }}</span>
                        <span class="priority-label">Regular</span>
                    </div>
                </div>
            </div>

            <!-- Top Performing Staff -->
            @if($staffActivity->isNotEmpty())
            <div class="section-card">
                <div class="section-title">
                    <i class="fas fa-user-md"></i>
                    Top Staff Today
                </div>
                
                @foreach($staffActivity as $staff)
                <div class="staff-item">
                    <span class="staff-name">{{ $staff->name }}</span>
                    <span class="staff-count">{{ $staff->queues_handled }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

<script>
// Auto-refresh dashboard every 30 seconds
setTimeout(function() {
    location.reload();
}, 30000);
</script>
@endsection
