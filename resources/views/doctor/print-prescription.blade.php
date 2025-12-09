<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription - {{ $patient->name }}</title>
    <style>
        @page {
            size: A5;
            margin: 1cm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #000;
            background: white;
        }

        .prescription {
            max-width: 21cm;
            margin: 0 auto;
            padding: 1.5cm;
            background: white;
        }

        /* Letterhead */
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 5px;
            color: #1e40af;
        }

        .header p {
            font-size: 10pt;
            margin: 2px 0;
        }

        /* Doctor Info */
        .doctor-info {
            text-align: center;
            margin-bottom: 20px;
            font-size: 11pt;
        }

        .doctor-info h2 {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 3px;
        }

        /* Patient Info */
        .patient-info {
            margin-bottom: 20px;
            padding: 10px;
            background: #f9fafb;
            border: 1px solid #d1d5db;
            border-radius: 5px;
        }

        .patient-info table {
            width: 100%;
        }

        .patient-info td {
            padding: 3px 5px;
            font-size: 11pt;
        }

        .patient-info td:first-child {
            font-weight: bold;
            width: 30%;
        }

        /* Prescription Symbol */
        .rx-symbol {
            font-size: 48pt;
            font-weight: bold;
            color: #1e40af;
            margin: 15px 0 10px 20px;
        }

        /* Medicines List */
        .medicines {
            margin-left: 30px;
            margin-bottom: 30px;
            min-height: 200px;
        }

        .medicine-item {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }

        .medicine-name {
            font-weight: bold;
            font-size: 12pt;
            margin-bottom: 3px;
        }

        .medicine-details {
            font-size: 11pt;
            margin-left: 15px;
            line-height: 1.4;
        }

        .medicine-sig {
            font-style: italic;
            color: #4b5563;
            margin-top: 2px;
        }

        /* Diagnosis Section */
        .diagnosis-section {
            margin: 20px 0;
            padding: 10px;
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
        }

        .diagnosis-section strong {
            display: block;
            margin-bottom: 5px;
            color: #92400e;
        }

        /* Notes */
        .notes {
            margin: 20px 0;
            padding: 10px;
            background: #e0f2fe;
            border-left: 4px solid #0284c7;
            font-size: 10pt;
        }

        /* Signature */
        .signature {
            margin-top: 40px;
            text-align: right;
        }

        .signature-line {
            border-top: 2px solid #000;
            width: 250px;
            margin: 40px 0 5px auto;
        }

        .signature-name {
            font-weight: bold;
            font-size: 12pt;
        }

        .signature-title {
            font-size: 10pt;
            color: #4b5563;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #d1d5db;
            text-align: center;
            font-size: 9pt;
            color: #6b7280;
        }

        /* Print Styles */
        @media print {
            body {
                background: white;
            }

            .prescription {
                padding: 0;
                margin: 0;
            }

            .no-print {
                display: none !important;
            }

            .patient-info {
                background: white;
                border: 1px solid #000;
            }

            .diagnosis-section {
                background: white;
                border-left: 3px solid #000;
            }

            .notes {
                background: white;
                border-left: 3px solid #000;
            }
        }

        /* Print Button */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #1e40af;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14pt;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .print-button:hover {
            background: #1e3a8a;
        }
    </style>
</head>
<body>
    <!-- Print Button -->
    <button onclick="window.print()" class="print-button no-print">
        🖨️ Print Prescription
    </button>

    <div class="prescription">
        <!-- Letterhead -->
        <div class="header">
            <h1>BARANGAY MABINI HEALTH CENTER</h1>
            <p>Barangay Mabini, Lipa City, Batangas</p>
            <p>Tel: (043) XXX-XXXX | Email: health@mabini.gov.ph</p>
        </div>

        <!-- Doctor Info -->
        <div class="doctor-info">
            <h2>{{ $consultation->doctor->name }}</h2>
            <p>Licensed Physician</p>
            @if($consultation->doctor->license_number ?? false)
            <p>License No.: {{ $consultation->doctor->license_number }}</p>
            @endif
        </div>

        <!-- Patient Information -->
        <div class="patient-info">
            <table>
                <tr>
                    <td>Patient Name:</td>
                    <td><strong>{{ $patient->name }}</strong></td>
                    <td>Date:</td>
                    <td><strong>{{ $consultation->created_at->format('F d, Y') }}</strong></td>
                </tr>
                <tr>
                    <td>Age/Sex:</td>
                    <td>{{ $patient->age ?? 'N/A' }} years / {{ $patient->gender ?? 'N/A' }}</td>
                    <td>Contact:</td>
                    <td>{{ $patient->contact_number ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Address:</td>
                    <td colspan="3">{{ $patient->address ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <!-- Diagnosis -->
        @if($consultation->diagnosis)
        <div class="diagnosis-section">
            <strong>Diagnosis:</strong>
            {{ $consultation->diagnosis }}
        </div>
        @endif

        <!-- Rx Symbol -->
        <div class="rx-symbol">℞</div>

        <!-- Prescribed Medicines -->
        <div class="medicines">
            @if($consultation->prescribed_medicines && count($consultation->prescribed_medicines) > 0)
                @foreach($consultation->prescribed_medicines as $index => $med)
                    @php
                        $medicine = \App\Models\Medicine::find($med['medicine_id']);
                    @endphp
                    <div class="medicine-item">
                        <div class="medicine-name">
                            {{ $index + 1 }}. {{ $medicine->name ?? 'Unknown Medicine' }}
                            @if(isset($med['dosage']))
                                - {{ $med['dosage'] }}
                            @endif
                        </div>
                        <div class="medicine-details">
                            @if(isset($med['quantity']))
                                <div>Quantity: <strong>{{ $med['quantity'] }}</strong></div>
                            @endif
                            @if(isset($med['frequency']))
                                <div>Frequency: {{ $med['frequency'] }}</div>
                            @endif
                            @if(isset($med['duration']))
                                <div>Duration: {{ $med['duration'] }}</div>
                            @endif
                            @if(isset($med['instructions']))
                                <div class="medicine-sig">Sig: {{ $med['instructions'] }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div style="font-style: italic; color: #6b7280;">
                    [Prescription details to be filled by physician]
                </div>
            @endif
        </div>

        <!-- Treatment Notes -->
        @if($consultation->treatment || $consultation->doctor_notes)
        <div class="notes">
            @if($consultation->treatment)
                <strong>Treatment Plan:</strong>
                <p>{{ $consultation->treatment }}</p>
            @endif
            @if($consultation->doctor_notes)
                <strong>Notes:</strong>
                <p>{{ $consultation->doctor_notes }}</p>
            @endif
        </div>
        @endif

        <!-- Follow-up -->
        @if($consultation->follow_up_date)
        <div style="margin: 15px 0; font-weight: bold; color: #b91c1c;">
            ⚠️ Follow-up Schedule: {{ \Carbon\Carbon::parse($consultation->follow_up_date)->format('F d, Y') }}
        </div>
        @endif

        <!-- Signature -->
        <div class="signature">
            <div class="signature-line"></div>
            <div class="signature-name">{{ $consultation->doctor->name }}</div>
            <div class="signature-title">Physician</div>
            @if($consultation->doctor->license_number ?? false)
            <div class="signature-title">License No.: {{ $consultation->doctor->license_number }}</div>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This prescription is valid for 30 days from the date of issue.</p>
            <p>For inquiries, please contact the health center during office hours.</p>
            <p style="margin-top: 10px; font-style: italic;">Generated on {{ now()->format('F d, Y h:i A') }}</p>
        </div>
    </div>

    <script>
        // Auto-print on load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
