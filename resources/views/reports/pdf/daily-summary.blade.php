<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #10b981;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            color: #10b981;
            font-size: 28px;
        }
        .header p {
            margin: 10px 0 0 0;
            color: #666;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            background-color: #10b981;
            color: white;
            padding: 10px 15px;
            border-radius: 4px;
            font-weight: bold;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .stats-table tr {
            border-bottom: 1px solid #e5e7eb;
        }
        .stats-table td {
            padding: 12px;
        }
        .stats-table td:first-child {
            font-weight: bold;
            width: 70%;
            color: #4b5563;
        }
        .stats-table td:last-child {
            text-align: right;
            font-weight: bold;
            color: #10b981;
            font-size: 16px;
        }
        .subsection {
            margin: 15px 0;
            padding: 10px;
            background-color: #f9fafb;
            border-left: 4px solid #10b981;
        }
        .subsection-title {
            font-weight: bold;
            color: #4b5563;
            margin-bottom: 8px;
        }
        .sub-stat {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 13px;
        }
        .footer {
            text-align: center;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
            margin-top: 30px;
            color: #999;
            font-size: 12px;
        }
        .highlight-value {
            color: #10b981;
            font-weight: bold;
        }
        .warning-value {
            color: #f59e0b;
            font-weight: bold;
        }
        .danger-value {
            color: #ef4444;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Daily Operations Summary Report</h1>
            <p>{{ $dailyData['date'] }}</p>
        </div>

        <!-- Patients Section -->
        <div class="section">
            <div class="section-title">Patient Statistics</div>
            <table class="stats-table">
                <tr>
                    <td>Total Consultations Today</td>
                    <td class="highlight-value">{{ $dailyData['patients']['total_consultations'] }}</td>
                </tr>
                <tr>
                    <td>New Patients Registered</td>
                    <td class="highlight-value">{{ $dailyData['patients']['new_patients'] }}</td>
                </tr>
                <tr>
                    <td>Total Registered Patients</td>
                    <td class="highlight-value">{{ $dailyData['patients']['total_registered'] }}</td>
                </tr>
            </table>
        </div>

        <!-- Queue Section -->
        <div class="section">
            <div class="section-title">Queue & Services Statistics</div>
            <table class="stats-table">
                <tr>
                    <td>Total Patients Served</td>
                    <td class="highlight-value">{{ $dailyData['queue']['total_served'] }}</td>
                </tr>
            </table>

            <div class="subsection">
                <div class="subsection-title">By Priority Level:</div>
                <div class="sub-stat">
                    <span>Normal Priority</span>
                    <span>{{ $dailyData['queue']['by_priority']['Normal'] }}</span>
                </div>
                <div class="sub-stat">
                    <span>Urgent Priority</span>
                    <span>{{ $dailyData['queue']['by_priority']['Urgent'] }}</span>
                </div>
                <div class="sub-stat">
                    <span>Emergency Priority</span>
                    <span>{{ $dailyData['queue']['by_priority']['Emergency'] }}</span>
                </div>
            </div>

            <div class="subsection">
                <div class="subsection-title">By Service Type:</div>
                <div class="sub-stat">
                    <span>Consultation</span>
                    <span>{{ $dailyData['queue']['by_service']['Consultation'] }}</span>
                </div>
                <div class="sub-stat">
                    <span>Check-up</span>
                    <span>{{ $dailyData['queue']['by_service']['Check-up'] }}</span>
                </div>
                <div class="sub-stat">
                    <span>Vaccination</span>
                    <span>{{ $dailyData['queue']['by_service']['Vaccination'] }}</span>
                </div>
                <div class="sub-stat">
                    <span>Emergency Services</span>
                    <span>{{ $dailyData['queue']['by_service']['Emergency'] }}</span>
                </div>
            </div>
        </div>

        <!-- Medicines Section -->
        <div class="section">
            <div class="section-title">Medicine & Inventory Status</div>
            <table class="stats-table">
                <tr>
                    <td>Total Medicines in Stock</td>
                    <td class="highlight-value">{{ $dailyData['medicines']['total_medicines'] }}</td>
                </tr>
                <tr>
                    <td>Low Stock Items</td>
                    <td class="warning-value">{{ $dailyData['medicines']['low_stock'] }}</td>
                </tr>
                <tr>
                    <td>Expired Items</td>
                    <td class="danger-value">{{ $dailyData['medicines']['expired'] }}</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>Report Generated: {{ $dailyData['generated_at'] }}</p>
            <p>Period: {{ $dailyData['start_date'] }} to {{ $dailyData['end_date'] }}</p>
            <p style="margin-top: 15px; font-style: italic;">Mabini Health Center - Official Report</p>
        </div>
    </div>
</body>
</html>
