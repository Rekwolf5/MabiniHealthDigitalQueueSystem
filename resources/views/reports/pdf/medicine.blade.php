<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Medicine Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: white;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #166534;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #166534;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
            font-size: 12px;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .stat-item {
            display: table-cell;
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
            width: 25%;
        }
        .stat-item h3 {
            margin: 0;
            font-size: 24px;
            color: #166534;
        }
        .stat-item p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 12px;
        }
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .section h3 {
            color: #166534;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .chart-item {
            margin-bottom: 10px;
        }
        .chart-label {
            font-size: 12px;
            margin-bottom: 3px;
        }
        .chart-bar {
            background: #f0f0f0;
            height: 20px;
            position: relative;
            border: 1px solid #ddd;
        }
        .chart-fill {
            background: #166534;
            height: 100%;
            display: inline-block;
        }
        .chart-value {
            font-size: 11px;
            text-align: right;
            margin-top: 2px;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Medicine Inventory Report</h1>
        <p>Mabini Health Center</p>
        <p>Generated: {{ $medicineData['generated_at'] }}</p>
    </div>

    <div class="stats-grid">
        <div class="stat-item">
            <h3>{{ $medicineData['total_medicines'] }}</h3>
            <p>Total Medicines</p>
        </div>
        <div class="stat-item">
            <h3>{{ $medicineData['low_stock_items'] }}</h3>
            <p>Low Stock</p>
        </div>
        <div class="stat-item">
            <h3>{{ $medicineData['expired_items'] }}</h3>
            <p>Expired</p>
        </div>
        <div class="stat-item">
            <h3>{{ $medicineData['total_value'] }}</h3>
            <p>Total Value</p>
        </div>
    </div>

    <div class="section">
        <h3>Stock Level Distribution</h3>
        @foreach($medicineData['stock_levels'] as $level => $count)
        <div class="chart-item">
            <div class="chart-label">{{ $level }}: {{ $count }} items</div>
            <div class="chart-bar">
                <div class="chart-fill" style="width: {{ ($count / max($medicineData['total_medicines'], 1)) * 100 }}%"></div>
            </div>
            <div class="chart-value">{{ round(($count / max($medicineData['total_medicines'], 1)) * 100) }}%</div>
        </div>
        @endforeach
    </div>

    <div class="section">
        <h3>Expiry Status</h3>
        @foreach($medicineData['expiry_alerts'] as $period => $count)
        <div class="chart-item">
            <div class="chart-label">{{ $period }}: {{ $count }} items</div>
            <div class="chart-bar">
                <div class="chart-fill" style="width: {{ ($count / max(array_sum($medicineData['expiry_alerts']), 1)) * 100 }}%"></div>
            </div>
            <div class="chart-value">{{ round(($count / max(array_sum($medicineData['expiry_alerts']), 1)) * 100) }}%</div>
        </div>
        @endforeach
    </div>

    <div class="footer">
        <p>Generated on {{ \Carbon\Carbon::now()->format('M d, Y H:i A') }}</p>
        <p>Mabini Health Center System</p>
    </div>
</body>
</html>
