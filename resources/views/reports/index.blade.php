@extends('layouts.app')

@section('title', 'Reports - Mabini Health Center')
@section('page-title', 'Reports & Analytics')

@section('content')
<div class="reports-container">
    <!-- Report Stats -->
    <div class="stats-row">
        <div class="stat-card patients">
            <div class="stat-icon">
                <i class="fas fa-user-injured"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $reportStats['total_patients'] }}</h3>
                <p>Total Patients</p>
            </div>
        </div>

        <div class="stat-card consultations">
            <div class="stat-icon">
                <i class="fas fa-stethoscope"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $reportStats['consultations_today'] }}</h3>
                <p>Consultations Today</p>
            </div>
        </div>

        <div class="stat-card medicines">
            <div class="stat-icon">
                <i class="fas fa-capsules"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $reportStats['medicines_dispensed'] }}</h3>
                <p>Medicines Dispensed</p>
            </div>
        </div>

        <div class="stat-card queue">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $reportStats['queue_average_wait'] }}</h3>
                <p>Avg Wait Time</p>
            </div>
        </div>
    </div>

    <!-- Report Categories -->
    <div class="reports-grid">
        <div class="report-category">
            <div class="category-header">
                <i class="fas fa-user-injured"></i>
                <h3>Patient Reports</h3>
            </div>
            <div class="category-content">
                <p>Generate patient statistics, demographics, and medical history reports.</p>
                <div class="category-actions">
                    <a href="{{ route('reports.patients') }}" class="btn btn-primary">
                        <i class="fas fa-eye"></i>
                        View Details
                    </a>
                    <button class="btn btn-secondary" onclick="generateReport('patients')">
                        <i class="fas fa-download"></i>
                        Generate
                    </button>
                </div>
            </div>
        </div>

        <div class="report-category">
            <div class="category-header">
                <i class="fas fa-clipboard-list"></i>
                <h3>Queue Reports</h3>
            </div>
            <div class="category-content">
                <p>Analyze queue performance, wait times, and service efficiency metrics.</p>
                <div class="category-actions">
                    <a href="{{ route('reports.queue') }}" class="btn btn-primary">
                        <i class="fas fa-eye"></i>
                        View Details
                    </a>
                    <button class="btn btn-secondary" onclick="generateReport('queue')">
                        <i class="fas fa-download"></i>
                        Generate
                    </button>
                </div>
            </div>
        </div>

        <div class="report-category">
            <div class="category-header">
                <i class="fas fa-capsules"></i>
                <h3>Medicine Reports</h3>
            </div>
            <div class="category-content">
                <p>Track inventory levels, expiry dates, and medicine dispensing patterns.</p>
                <div class="category-actions">
                    <a href="{{ route('reports.medicines') }}" class="btn btn-primary">
                        <i class="fas fa-eye"></i>
                        View Details
                    </a>
                    <button class="btn btn-secondary" onclick="generateReport('medicines')">
                        <i class="fas fa-download"></i>
                        Generate
                    </button>
                </div>
            </div>
        </div>

        <div class="report-category">
            <div class="category-header">
                <i class="fas fa-calendar-day"></i>
                <h3>Daily Summary</h3>
            </div>
            <div class="category-content">
                <p>Complete daily operations summary including all activities and statistics.</p>
                <div class="category-actions">
                    <button class="btn btn-primary" onclick="generateReport('daily')">
                        <i class="fas fa-download"></i>
                        Generate Today
                    </button>
                    <button class="btn btn-secondary" onclick="generateReport('weekly')">
                        <i class="fas fa-download"></i>
                        Weekly
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reports Section - Only show if there are reports -->
    @if(count($recentReports) > 0)
    <div class="dashboard-section">
        <div class="section-header">
            <h2>Recent Reports</h2>
        </div>
        
        <div class="reports-table">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Report Title</th>
                        <th>Type</th>
                        <th>Date Generated</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentReports as $report)
                    <tr>
                        <td>{{ $report['title'] }}</td>
                        <td>
                            <span class="type-badge type-{{ strtolower($report['type']) }}">
                                {{ $report['type'] }}
                            </span>
                        </td>
                        <td>{{ $report['date'] }}</td>
                        <td>
                            <span class="status-badge status-{{ strtolower($report['status']) }}">
                                {{ $report['status'] }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-success">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="dashboard-section">
        <div class="section-header">
            <h2>Recent Reports</h2>
        </div>
        <p style="color: #6b7280; text-align: center; padding: 2rem;">
            No reports generated yet. Start by generating your first report above.
        </p>
    </div>
    @endif

    <!-- Report Shortcuts - New Section -->
    <div class="report-shortcuts">
        <h2>Quick Access Reports</h2>
        <div class="shortcuts-grid">
            <div class="report-card">
                <div class="report-icon">
                    <i class="fas fa-user-injured"></i>
                </div>
                <div class="report-info">
                    <h3>Patient Reports</h3>
                    <p>Statistics and demographics</p>
                </div>
                <div class="report-actions">
                    <a href="{{ route('reports.patients') }}" class="btn btn-primary">
                        <i class="fas fa-file-alt"></i>
                        View Report
                    </a>
                </div>
            </div>

            <div class="report-card">
                <div class="report-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="report-info">
                    <h3>Queue Reports</h3>
                    <p>Performance and wait times</p>
                </div>
                <div class="report-actions">
                    <a href="{{ route('reports.queue') }}" class="btn btn-primary">
                        <i class="fas fa-file-alt"></i>
                        View Report
                    </a>
                </div>
            </div>

            <div class="report-card">
                <div class="report-icon">
                    <i class="fas fa-capsules"></i>
                </div>
                <div class="report-info">
                    <h3>Medicine Reports</h3>
                    <p>Inventory and dispensing</p>
                </div>
                <div class="report-actions">
                    <a href="{{ route('reports.medicines') }}" class="btn btn-primary">
                        <i class="fas fa-file-alt"></i>
                        View Report
                    </a>
                </div>
            </div>

            <div class="report-card">
                <div class="report-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="report-info">
                    <h3>Daily Summary</h3>
                    <p>Complete daily operations report</p>
                </div>
                <div class="report-actions">
                    <a href="{{ route('reports.daily-summary') }}" class="btn btn-primary">
                        <i class="fas fa-file-alt"></i>
                        View Report
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
