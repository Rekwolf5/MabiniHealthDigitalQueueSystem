@extends('layouts.app')

@section('title', 'Admin Dashboard - Mabini Health Center')
@section('page-title', 'Admin Dashboard')

@section('content')
<div class="container">
    <div class="dashboard-header">
        <h2><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h2>
        <p>Welcome back, {{ auth()->user()->name }}!</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card stat-primary">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $totalUsers ?? 0 }}</h3>
                <p>Total Users</p>
            </div>
        </div>

        <div class="stat-card stat-success">
            <div class="stat-icon">
                <i class="fas fa-user-nurse"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $staffCount ?? 0 }}</h3>
                <p>Staff Members</p>
            </div>
        </div>

        <div class="stat-card stat-info">
            <div class="stat-icon">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $adminCount ?? 0 }}</h3>
                <p>Administrators</p>
            </div>
        </div>

        <div class="stat-card stat-warning">
            <div class="stat-icon">
                <i class="fas fa-procedures"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $patientCount ?? 0 }}</h3>
                <p>Registered Patients</p>
            </div>
        </div>
    </div>

    <div class="action-cards">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-user-plus"></i> Quick Actions</h3>
            </div>
            <div class="card-body">
                <div class="action-buttons">
                    <a href="{{ route('admin.staff.create') }}" class="action-btn">
                        <i class="fas fa-user-plus"></i>
                        <span>Create Staff Account</span>
                    </a>
                    <a href="{{ route('admin.users') }}" class="action-btn">
                        <i class="fas fa-users-cog"></i>
                        <span>Manage Users</span>
                    </a>
                    <a href="{{ route('admin.queue.monitor') }}" class="action-btn">
                        <i class="fas fa-list-ol"></i>
                        <span>Monitor Queue</span>
                    </a>
                    <a href="{{ route('front-desk.index') }}" class="action-btn">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Front Desk Queue</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-clock"></i> Recent Activity</h3>
            </div>
            <div class="card-body">
                @if(isset($recentActivity) && count($recentActivity) > 0)
                    <div class="activity-list">
                        @foreach($recentActivity as $activity)
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-circle"></i>
                                </div>
                                <div class="activity-content">
                                    <p><strong>{{ $activity->user_name ?? 'Unknown User' }}</strong> {{ $activity->action ?? 'performed an action' }}</p>
                                    <small>{{ $activity->created_at ? $activity->created_at->diffForHumans() : 'Recently' }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="color: #6b7280; text-align: center; padding: 2rem;">
                        <i class="fas fa-info-circle"></i> No recent activity to display
                    </p>
                @endif
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-chart-line"></i> System Overview</h3>
        </div>
        <div class="card-body">
            <div class="overview-grid">
                <div class="overview-item">
                    <i class="fas fa-calendar-day"></i>
                    <div>
                        <strong>Today's Queue</strong>
                        <p>{{ $todayQueue ?? 0 }} patients</p>
                    </div>
                </div>
                <div class="overview-item">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong>Completed Today</strong>
                        <p>{{ $completedToday ?? 0 }} consultations</p>
                    </div>
                </div>
                <div class="overview-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <strong>Waiting</strong>
                        <p>{{ $waitingQueue ?? 0 }} patients</p>
                    </div>
                </div>
                <div class="overview-item">
                    <i class="fas fa-pills"></i>
                    <div>
                        <strong>Medicine Stock</strong>
                        <p>{{ $medicineCount ?? 0 }} items</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.dashboard-header {
    margin-bottom: 2rem;
}

.dashboard-header h2 {
    color: #1f2937;
    margin: 0 0 0.5rem 0;
    font-size: 2rem;
}

.dashboard-header p {
    color: #6b7280;
    margin: 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

/* Responsive Stats Grid */
@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
}

@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .dashboard-header h2 {
        font-size: 1.5rem;
    }
}

.stat-card {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.stat-icon {
    width: 64px;
    height: 64px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
}

.stat-primary .stat-icon {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stat-success .stat-icon {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.stat-info .stat-icon {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
}

.stat-warning .stat-icon {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.stat-content h3 {
    margin: 0 0 0.25rem 0;
    font-size: 2rem;
    color: #1f2937;
}

.stat-content p {
    margin: 0;
    color: #6b7280;
    font-size: 0.875rem;
}

.action-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

/* Responsive Action Cards */
@media (max-width: 768px) {
    .action-cards {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
}

@media (max-width: 480px) {
    .action-cards {
        grid-template-columns: 1fr;
    }
}

.card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.card-header h3 {
    margin: 0;
    color: #1f2937;
    font-size: 1.25rem;
}

.card-body {
    padding: 1.5rem;
}

.action-buttons {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

/* Responsive Action Buttons */
@media (max-width: 480px) {
    .action-buttons {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
}

.action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1.5rem;
    background: #f9fafb;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    text-decoration: none;
    color: #374151;
    transition: all 0.2s;
}

.action-btn:hover {
    background: #10b981;
    color: white;
    border-color: #10b981;
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);
}

.action-btn i {
    font-size: 2rem;
}

.action-btn span {
    font-weight: 500;
    text-align: center;
}

.activity-list {
    max-height: 300px;
    overflow-y: auto;
}

.activity-item {
    display: flex;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid #e5e7eb;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    color: #10b981;
    padding-top: 0.25rem;
}

.activity-content p {
    margin: 0 0 0.25rem 0;
    color: #1f2937;
}

.activity-content small {
    color: #6b7280;
}

.overview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
}

/* Responsive Overview Grid */
@media (max-width: 768px) {
    .overview-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
}

@media (max-width: 480px) {
    .overview-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .overview-item i {
        font-size: 2rem;
    }
    
    .overview-item p {
        font-size: 1.125rem;
    }
}
</style>
@endsection
