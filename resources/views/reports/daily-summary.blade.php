@extends('layouts.app')

@section('title', 'Daily Summary Report - Mabini Health Center')
@section('page-title', 'Daily Summary Report')

@section('content')
<div class="report-container">
    <div class="report-header">
        <h2>Daily Operations Summary</h2>
        <p>{{ $dailyData['date'] }}</p>
    </div>

    <div class="period-selector">
        <label for="period">Select Period:</label>
        <select id="period" onchange="changePeriod(this.value)">
            <option value="daily" {{ $dailyData['period'] === 'daily' ? 'selected' : '' }}>Today</option>
            <option value="weekly" {{ $dailyData['period'] === 'weekly' ? 'selected' : '' }}>This Week</option>
        </select>
        <a href="{{ request()->fullUrl() }}&export=pdf" class="btn btn-success" target="_blank">
            <i class="fas fa-file-pdf"></i> Download PDF
        </a>
    </div>

    <div class="summary-grid">
        <!-- Patients Section -->
        <div class="summary-section">
            <h3><i class="fas fa-user-injured"></i> Patient Statistics</h3>
            <div class="stats-list">
                <div class="stat-item">
                    <span class="label">Total Consultations:</span>
                    <span class="value">{{ $dailyData['patients']['total_consultations'] }}</span>
                </div>
                <div class="stat-item">
                    <span class="label">New Patients:</span>
                    <span class="value">{{ $dailyData['patients']['new_patients'] }}</span>
                </div>
                <div class="stat-item">
                    <span class="label">Total Registered:</span>
                    <span class="value">{{ $dailyData['patients']['total_registered'] }}</span>
                </div>
            </div>
        </div>

        <!-- Queue Section -->
        <div class="summary-section">
            <h3><i class="fas fa-clipboard-list"></i> Queue Statistics</h3>
            <div class="stats-list">
                <div class="stat-item">
                    <span class="label">Total Served:</span>
                    <span class="value">{{ $dailyData['queue']['total_served'] }}</span>
                </div>
                <div class="subsection">
                    <h4>By Priority:</h4>
                    <div class="sub-stats">
                        <div class="sub-stat">
                            <span>Normal: {{ $dailyData['queue']['by_priority']['Normal'] }}</span>
                        </div>
                        <div class="sub-stat">
                            <span>Urgent: {{ $dailyData['queue']['by_priority']['Urgent'] }}</span>
                        </div>
                        <div class="sub-stat">
                            <span>Emergency: {{ $dailyData['queue']['by_priority']['Emergency'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medicines Section -->
        <div class="summary-section">
            <h3><i class="fas fa-capsules"></i> Medicine Status</h3>
            <div class="stats-list">
                <div class="stat-item">
                    <span class="label">Total Medicines:</span>
                    <span class="value">{{ $dailyData['medicines']['total_medicines'] }}</span>
                </div>
                <div class="stat-item">
                    <span class="label">Low Stock:</span>
                    <span class="value warning">{{ $dailyData['medicines']['low_stock'] }}</span>
                </div>
                <div class="stat-item">
                    <span class="label">Expired:</span>
                    <span class="value danger">{{ $dailyData['medicines']['expired'] }}</span>
                </div>
            </div>
        </div>

        <!-- Service Types -->
        <div class="summary-section">
            <h3><i class="fas fa-stethoscope"></i> Services Provided</h3>
            <div class="stats-list">
                <div class="sub-stats">
                    <div class="sub-stat">
                        <span>Consultation: {{ $dailyData['queue']['by_service']['Consultation'] }}</span>
                    </div>
                    <div class="sub-stat">
                        <span>Check-up: {{ $dailyData['queue']['by_service']['Check-up'] }}</span>
                    </div>
                    <div class="sub-stat">
                        <span>Vaccination: {{ $dailyData['queue']['by_service']['Vaccination'] }}</span>
                    </div>
                    <div class="sub-stat">
                        <span>Emergency: {{ $dailyData['queue']['by_service']['Emergency'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="report-footer">
        <p>Report Period: {{ $dailyData['start_date'] }} to {{ $dailyData['end_date'] }}</p>
        <p>Generated: {{ $dailyData['generated_at'] }}</p>
    </div>
</div>

<style>
.report-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 2rem;
}

.report-header {
    margin-bottom: 2rem;
    text-align: center;
}

.report-header h2 {
    font-size: 1.8rem;
    margin-bottom: 0.5rem;
}

.period-selector {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    align-items: center;
    justify-content: center;
}

.period-selector select {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.summary-section {
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.summary-section h3 {
    color: #10b981;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.stats-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.stat-item {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem;
    background: #f9fafb;
    border-radius: 4px;
}

.stat-item .label {
    font-weight: 500;
    color: #4b5563;
}

.stat-item .value {
    font-weight: bold;
    color: #10b981;
    font-size: 1.2rem;
}

.stat-item .value.warning {
    color: #f59e0b;
}

.stat-item .value.danger {
    color: #ef4444;
}

.subsection h4 {
    color: #4b5563;
    margin-top: 0.5rem;
    font-size: 0.9rem;
}

.sub-stats {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.sub-stat {
    padding: 0.5rem;
    background: #f0fdf4;
    border-left: 3px solid #10b981;
    padding-left: 0.75rem;
    font-size: 0.9rem;
}

.report-footer {
    text-align: center;
    padding-top: 2rem;
    border-top: 1px solid #ddd;
    color: #6b7280;
    font-size: 0.9rem;
}

.btn-success {
    background-color: #10b981;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-success:hover {
    background-color: #059669;
}
</style>

<script>
function changePeriod(period) {
    const url = new URL(window.location);
    url.searchParams.set('period', period);
    window.location = url.toString();
}
</script>
@endsection
