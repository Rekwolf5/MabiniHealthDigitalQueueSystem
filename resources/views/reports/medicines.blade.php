@extends('layouts.app')

@section('title', 'Medicine Reports - Mabini Health Center')
@section('page-title', 'Medicine Inventory Reports')

@section('content')
<div class="reports-detail">
    <div class="page-header">
        <div class="header-actions">
            <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to Reports
            </a>
            <div class="report-controls" style="display: flex; gap: 10px; align-items: center;">
                <button class="btn btn-primary" onclick="exportPDF()">
                    <i class="fas fa-download"></i>
                    Export PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Medicine Statistics -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-pills"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $medicineData['total_medicines'] }}</h3>
                <p>Total Medicines</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $medicineData['low_stock_items'] }}</h3>
                <p>Low Stock Items</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-peso-sign"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $medicineData['total_value'] }}</h3>
                <p>Total Value</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $medicineData['expired_items'] }}</h3>
                <p>Expired Items</p>
            </div>
        </div>
    </div>

    <!-- Medicine Analytics -->
    <div class="reports-grid">
        <div class="report-section">
            <h3>Stock Level Distribution</h3>
            <div class="chart-container">
                @foreach($medicineData['stock_levels'] as $level => $count)
                <div class="chart-item">
                    <div class="chart-label">{{ $level }}</div>
                    <div class="chart-bar">
                        @php
                            $percentage = $medicineData['total_medicines'] > 0 
                                ? ($count / $medicineData['total_medicines']) * 100 
                                : 0;
                        @endphp
                        <div class="chart-fill stock-{{ strtolower(explode(' ', $level)[0]) }}" style="width: {{ $percentage }}%"></div>
                    </div>
                    <div class="chart-value">{{ $count }}</div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="report-section">
            <h3>Expiry Alerts</h3>
            <div class="chart-container">
                @php
                    $maxExpiryCount = max(array_values($medicineData['expiry_alerts']));
                    $maxExpiryCount = $maxExpiryCount > 0 ? $maxExpiryCount : 1;
                @endphp
                @foreach($medicineData['expiry_alerts'] as $period => $count)
                <div class="chart-item">
                    <div class="chart-label">{{ $period }}</div>
                    <div class="chart-bar">
                        @php
                            $percentage = ($count / $maxExpiryCount) * 100;
                        @endphp
                        <div class="chart-fill expiry-alert" style="width: {{ $percentage }}%"></div>
                    </div>
                    <div class="chart-value">{{ $count }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <p style="color: #6b7280; font-size: 12px; margin-top: 20px;">
        Generated at: {{ $medicineData['generated_at'] }}
    </p>
</div>

<script>
function exportPDF() {
    window.location.href = `{{ route('reports.medicines') }}?export=pdf`;
}
</script>
@endsection
