<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Queue Report</title>
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
        <h1>Queue Performance Report</h1>
        <p>Mabini Health Center</p>
        <p>Period: {{ $queueData['start_date'] }} - {{ $queueData['end_date'] }}</p>
    </div>

    <div class="stats-grid">
        <div class="stat-item">
            <h3>{{ $queueData['total_served_today'] }}</h3>
            <p>Patients Served</p>
        </div>
        <div class="stat-item">
            <h3>{{ $queueData['average_wait_time'] }}</h3>
            <p>Average Wait</p>
        </div>
        <div class="stat-item">
            <h3>{{ count($queueData['by_priority']) }}</h3>
            <p>Priority Levels</p>
        </div>
    </div>

    <div class="section">
        <h3>Priority Distribution</h3>
        @foreach($queueData['by_priority'] as $priority => $count)
        <div class="chart-item">
            <div class="chart-label">{{ $priority }}: {{ $count }} patients</div>
            <div class="chart-bar">
                <div class="chart-fill" style="width: {{ ($count / max($queueData['total_served_today'], 1)) * 100 }}%"></div>
            </div>
            <div class="chart-value">{{ round(($count / max($queueData['total_served_today'], 1)) * 100) }}%</div>
        </div>
        @endforeach
    </div>

    <div class="section">
        <h3>Service Types</h3>
        @foreach($queueData['by_service'] as $service => $count)
        <div class="chart-item">
            <div class="chart-label">{{ $service }}: {{ $count }} patients</div>
            <div class="chart-bar">
                <div class="chart-fill" style="width: {{ ($count / max($queueData['total_served_today'], 1)) * 100 }}%"></div>
            </div>
            <div class="chart-value">{{ round(($count / max($queueData['total_served_today'], 1)) * 100) }}%</div>
        </div>
        @endforeach
    </div>

    <div class="footer">
        <p>Generated on {{ \Carbon\Carbon::now()->format('M d, Y H:i A') }}</p>
        <p>Mabini Health Center System</p>
    </div>
</body>
</html>
