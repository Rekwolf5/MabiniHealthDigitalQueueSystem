<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue List - {{ now()->format('F d, Y') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            padding: 2rem;
            background: white;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 3px solid #10b981;
        }

        .header h1 {
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .header .subtitle {
            color: #6b7280;
            font-size: 1.125rem;
        }

        .header .date {
            color: #10b981;
            font-weight: bold;
            font-size: 1.25rem;
            margin-top: 0.5rem;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-box {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
        }

        .stat-box .number {
            font-size: 2rem;
            font-weight: bold;
            color: #10b981;
        }

        .stat-box .label {
            color: #6b7280;
            margin-top: 0.5rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }

        th {
            background: #10b981;
            color: white;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
        }

        td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        tr:nth-child(even) {
            background: #f9fafb;
        }

        .queue-number {
            font-family: monospace;
            font-size: 1.125rem;
            font-weight: bold;
            color: #10b981;
        }

        .priority-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .priority-pwd {
            background: #dbeafe;
            color: #1e40af;
        }

        .priority-pregnant {
            background: #fce7f3;
            color: #9f1239;
        }

        .priority-senior {
            background: #fef3c7;
            color: #92400e;
        }

        .priority-regular {
            background: #e5e7eb;
            color: #374151;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.875rem;
        }

        .status-waiting {
            background: #fef3c7;
            color: #92400e;
        }

        .status-consulting {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }

        .footer {
            margin-top: 3rem;
            padding-top: 1rem;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
        }

        .no-print {
            text-align: center;
            margin-bottom: 1rem;
        }

        .btn-print {
            background: #10b981;
            color: white;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
        }

        .btn-print:hover {
            background: #059669;
        }

        @media print {
            body {
                padding: 1rem;
            }

            .no-print {
                display: none;
            }

            th {
                background: #374151 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .priority-badge,
            .status-badge {
                border: 1px solid #000;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">
            🖨️ Print Queue List
        </button>
    </div>

    <div class="header">
        <h1>Mabini Health Center</h1>
        <div class="subtitle">Patient Queue List</div>
        <div class="date">{{ now()->format('l, F d, Y') }}</div>
    </div>

    <div class="stats">
        <div class="stat-box">
            <div class="number">{{ $queue->count() }}</div>
            <div class="label">Total Patients</div>
        </div>
        <div class="stat-box">
            <div class="number">{{ $queue->where('status', 'Waiting')->count() }}</div>
            <div class="label">Waiting</div>
        </div>
        <div class="stat-box">
            <div class="number">{{ $queue->where('status', 'Consulting')->count() }}</div>
            <div class="label">In Consultation</div>
        </div>
        <div class="stat-box">
            <div class="number">{{ $queue->where('status', 'Completed')->count() }}</div>
            <div class="label">Completed</div>
        </div>
    </div>

    @if($queue->isEmpty())
        <div style="text-align: center; padding: 3rem; color: #6b7280;">
            <p style="font-size: 1.25rem;">No patients in queue today</p>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 12%;">Queue Number</th>
                    <th style="width: 25%;">Patient Name</th>
                    <th style="width: 18%;">Service</th>
                    <th style="width: 12%;">Priority</th>
                    <th style="width: 10%;">Arrived</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 8%;">Signature</th>
                </tr>
            </thead>
            <tbody>
                @foreach($queue as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <span class="queue-number">{{ $item->queue_number }}</span>
                        </td>
                        <td>{{ $item->patient->full_name ?? $item->patient_name }}</td>
                        <td>{{ $item->service_type }}</td>
                        <td>
                            <span class="priority-badge priority-{{ strtolower($item->priority) }}">
                                {{ $item->priority }}
                            </span>
                        </td>
                        <td>{{ $item->arrived_at ? $item->arrived_at->format('h:i A') : '-' }}</td>
                        <td>
                            <span class="status-badge status-{{ strtolower($item->status) }}">
                                {{ $item->status }}
                            </span>
                        </td>
                        <td style="border-bottom: 1px solid #000;"></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        <p><strong>Mabini Health Center</strong></p>
        <p>Printed on: {{ now()->format('F d, Y h:i A') }}</p>
        <p>Printed by: {{ auth()->user()->name }}</p>
    </div>

    <script>
        // Auto-print dialog on page load
        // window.onload = function() {
        //     window.print();
        // }
    </script>
</body>
</html>
