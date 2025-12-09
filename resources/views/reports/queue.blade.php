@extends('layouts.app')

@section('title', 'Queue Reports - Mabini Health Center')
@section('page-title', 'Queue Performance Reports')

@section('content')
<div class="reports-detail">
    <div class="page-header">
        <div class="header-actions">
            <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to Reports
            </a>
            <!-- Added period selector and export buttons -->
            <div class="report-controls" style="display: flex; gap: 10px; align-items: center;">
                <select id="periodSelect" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="daily" {{ $queueData['period'] === 'daily' ? 'selected' : '' }}>Today</option>
                    <option value="weekly" {{ $queueData['period'] === 'weekly' ? 'selected' : '' }}>This Week</option>
                    <option value="monthly" {{ $queueData['period'] === 'monthly' ? 'selected' : '' }}>This Month</option>
                </select>
                <button class="btn btn-primary" onclick="updatePeriod()">
                    <i class="fas fa-refresh"></i>
                    Update
                </button>
                <button class="btn btn-success" onclick="exportPDF()">
                    <i class="fas fa-download"></i>
                    Export PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Queue Statistics -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $queueData['total_served_today'] }}</h3>
                <p>Served ({{ $queueData['period'] }})</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $queueData['average_wait_time'] }}</h3>
                <p>Average Wait</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $queueData['peak_hours'] }}</h3>
                <p>Peak Hours</p>
            </div>
        </div>
    </div>

    <!-- Queue Analytics -->
    <div class="reports-grid">
        <div class="report-section">
            <h3>Priority Distribution</h3>
            <div class="chart-container">
                @foreach($queueData['by_priority'] as $priority => $count)
                <div class="chart-item">
                    <div class="chart-label">{{ $priority }}</div>
                    <div class="chart-bar">
                        <div class="chart-fill priority-{{ strtolower($priority) }}" style="width: {{ ($count / max($queueData['total_served_today'], 1)) * 100 }}%"></div>
                    </div>
                    <div class="chart-value">{{ $count }}</div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="report-section">
            <h3>Service Types</h3>
            <div class="chart-container">
                @foreach($queueData['by_service'] as $service => $count)
                <div class="chart-item">
                    <div class="chart-label">{{ $service }}</div>
                    <div class="chart-bar">
                        <div class="chart-fill" style="width: {{ ($count / max($queueData['total_served_today'], 1)) * 100 }}%"></div>
                    </div>
                    <div class="chart-value">{{ $count }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <p style="color: #6b7280; font-size: 12px; margin-top: 20px;">
        Report Period: {{ $queueData['start_date'] }} - {{ $queueData['end_date'] }}
    </p>
</div>

<script>
function updatePeriod() {
    const period = document.getElementById('periodSelect').value;
    window.location.href = `{{ route('reports.queue') }}?period=${period}`;
}

function exportPDF() {
    const period = document.getElementById('periodSelect').value;
    window.location.href = `{{ route('reports.queue') }}?period=${period}&export=pdf`;
}
</script>
@endsection
