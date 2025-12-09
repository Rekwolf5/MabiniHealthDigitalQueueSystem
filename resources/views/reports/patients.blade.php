@extends('layouts.app')

@section('title', 'Patient Reports - Mabini Health Center')
@section('page-title', 'Patient Reports')

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
                    <option value="all" {{ $patientData['period'] === 'all' ? 'selected' : '' }}>All Time</option>
                    <option value="weekly" {{ $patientData['period'] === 'weekly' ? 'selected' : '' }}>This Week</option>
                    <option value="monthly" {{ $patientData['period'] === 'monthly' ? 'selected' : '' }}>This Month</option>
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

    <!-- Patient Statistics -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $patientData['total_registered'] }}</h3>
                <p>Total Registered</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $patientData['new_this_month'] }}</h3>
                <p>New ({{ $patientData['period'] }})</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-heartbeat"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $patientData['active_patients'] }}</h3>
                <p>Active Patients</p>
            </div>
        </div>
    </div>

    <!-- Demographics -->
    <div class="reports-grid">
        <div class="report-section">
            <h3>Age Distribution</h3>
            <div class="chart-container">
                @foreach($patientData['by_age_group'] as $ageGroup => $count)
                <div class="chart-item">
                    <div class="chart-label">{{ $ageGroup }} years</div>
                    <div class="chart-bar">
                        <div class="chart-fill" style="width: {{ ($count / $patientData['total_registered']) * 100 }}%"></div>
                    </div>
                    <div class="chart-value">{{ $count }}</div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="report-section">
            <h3>Gender Distribution</h3>
            <div class="chart-container">
                @foreach($patientData['by_gender'] as $gender => $count)
                <div class="chart-item">
                    <div class="chart-label">{{ ucfirst($gender) }}</div>
                    <div class="chart-bar">
                        <div class="chart-fill" style="width: {{ ($count / $patientData['total_registered']) * 100 }}%"></div>
                    </div>
                    <div class="chart-value">{{ $count }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <p style="color: #6b7280; font-size: 12px; margin-top: 20px;">
        Report Period: {{ $patientData['start_date'] }} - {{ $patientData['end_date'] }}
    </p>
</div>

<script>
function updatePeriod() {
    const period = document.getElementById('periodSelect').value;
    window.location.href = `{{ route('reports.patients') }}?period=${period}`;
}

function exportPDF() {
    const period = document.getElementById('periodSelect').value;
    window.location.href = `{{ route('reports.patients') }}?period=${period}&export=pdf`;
}
</script>
@endsection
