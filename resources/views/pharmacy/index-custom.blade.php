@extends('layouts.app')

@section('content')
<div class="dashboard-grid">
    <!-- Page Header -->
    <div class="dashboard-section" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); color: white; border: none;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">
                    <i class="fas fa-pills"></i> Pharmacy Dashboard
                </h1>
                <p style="opacity: 0.9;">Medicine dispensing, history, and inventory management</p>
            </div>
            <div>
                <a href="{{ route('pharmacy.quick-dispense') }}" 
                   class="btn btn-success" style="background: white; color: #059669; font-weight: 600;">
                    <i class="fas fa-bolt"></i>
                    Quick Dispense
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Statistics Dashboard -->
    <div class="stats-row">
        <div class="stat-card" style="border-left: 4px solid #3b82f6;">
            <div class="stat-icon" style="background: #3b82f6;">
                <i class="fas fa-prescription-bottle-alt"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['pending_prescriptions'] }}</h3>
                <p>Pending Prescriptions</p>
            </div>
        </div>
        
        <div class="stat-card" style="border-left: 4px solid #10b981;">
            <div class="stat-icon" style="background: #10b981;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['dispensed_today'] }}</h3>
                <p>Dispensed Today</p>
            </div>
        </div>
        
        <div class="stat-card" style="border-left: 4px solid #8b5cf6;">
            <div class="stat-icon" style="background: #8b5cf6;">
                <i class="fas fa-coins"></i>
            </div>
            <div class="stat-info">
                <h3 style="font-size: 1.25rem;">₱{{ number_format($stats['total_stock_value'], 2) }}</h3>
                <p>Total Stock Value</p>
            </div>
        </div>
        
        <div class="stat-card" style="border-left: 4px solid #f59e0b;">
            <div class="stat-icon" style="background: #f59e0b;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['low_stock_count'] }}</h3>
                <p>Low Stock Items</p>
            </div>
        </div>
        
        <div class="stat-card" style="border-left: 4px solid #f97316;">
            <div class="stat-icon" style="background: #f97316;">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['expiring_soon_count'] }}</h3>
                <p>Expiring Soon</p>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="dashboard-section" style="padding: 0; overflow: hidden;">
        <div style="display: flex; border-bottom: 2px solid #e5e7eb;">
            <a href="{{ route('pharmacy.index', ['tab' => 'pending']) }}" 
               style="flex: 1; padding: 1rem; text-align: center; text-decoration: none; font-weight: 600; 
                      color: {{ ($tab ?? 'pending') === 'pending' ? '#059669' : '#6b7280' }};
                      border-bottom: {{ ($tab ?? 'pending') === 'pending' ? '3px solid #059669' : '3px solid transparent' }};
                      background: {{ ($tab ?? 'pending') === 'pending' ? '#f0fdf4' : 'white' }};">
                <i class="fas fa-prescription-bottle-alt"></i> 
                Pending Prescriptions
                @if($stats['pending_prescriptions'] > 0)
                    <span style="background: #3b82f6; color: white; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.75rem; margin-left: 0.5rem;">
                        {{ $stats['pending_prescriptions'] }}
                    </span>
                @endif
            </a>
            <a href="{{ route('pharmacy.index', ['tab' => 'history']) }}" 
               style="flex: 1; padding: 1rem; text-align: center; text-decoration: none; font-weight: 600;
                      color: {{ ($tab ?? 'pending') === 'history' ? '#059669' : '#6b7280' }};
                      border-bottom: {{ ($tab ?? 'pending') === 'history' ? '3px solid #059669' : '3px solid transparent' }};
                      background: {{ ($tab ?? 'pending') === 'history' ? '#f0fdf4' : 'white' }};">
                <i class="fas fa-history"></i> 
                Dispensing History
            </a>
            <a href="{{ route('pharmacy.index', ['tab' => 'alerts']) }}" 
               style="flex: 1; padding: 1rem; text-align: center; text-decoration: none; font-weight: 600;
                      color: {{ ($tab ?? 'pending') === 'alerts' ? '#059669' : '#6b7280' }};
                      border-bottom: {{ ($tab ?? 'pending') === 'alerts' ? '3px solid #059669' : '3px solid transparent' }};
                      background: {{ ($tab ?? 'pending') === 'alerts' ? '#f0fdf4' : 'white' }};">
                <i class="fas fa-exclamation-triangle"></i> 
                Stock Alerts
                @if($stats['low_stock_count'] > 0 || $stats['expiring_soon_count'] > 0)
                    <span style="background: #dc2626; color: white; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.75rem; margin-left: 0.5rem;">
                        {{ $stats['low_stock_count'] + $stats['expiring_soon_count'] }}
                    </span>
                @endif
            </a>
        </div>
    </div>

    @if(($tab ?? 'pending') === 'pending')
    <!-- PENDING PRESCRIPTIONS TAB -->
    <div class="data-table-container">
        <table class="data-table">
            <thead>
                <tr style="background: #059669;">
                    <th style="color: white;"><i class="fas fa-hashtag"></i> Queue #</th>
                    <th style="color: white;"><i class="fas fa-user"></i> Patient</th>
                    <th style="color: white;"><i class="fas fa-stethoscope"></i> Service</th>
                    <th style="color: white;"><i class="fas fa-notes-medical"></i> Diagnosis</th>
                    <th style="color: white;"><i class="fas fa-pills"></i> Prescribed Medicines</th>
                    <th style="color: white;"><i class="fas fa-clock"></i> Consultation Time</th>
                    <th style="color: white; text-align: center;"><i class="fas fa-cog"></i> Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($prescriptions as $queue)
                    <tr>
                        <td>
                            <span style="background: #dbeafe; color: #1e40af; padding: 0.5rem; border-radius: 4px; font-weight: 700; font-size: 1rem;">
                                {{ $queue->queue_number }}
                            </span>
                        </td>
                        <td>
                            <div class="patient-name">
                                <i class="fas fa-user-circle"></i>
                                <div>
                                    <strong>{{ $queue->patient->name }}</strong><br>
                                    <small style="color: #6b7280;">ID: {{ $queue->patient->patient_id }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="btn-sm" style="
                                background: {{ $queue->service_type === 'Medical Consultation' ? '#dbeafe' : 
                                   ($queue->service_type === 'Dental Services' ? '#d1fae5' : '#f3e8ff') }};
                                color: {{ $queue->service_type === 'Medical Consultation' ? '#1e40af' : 
                                   ($queue->service_type === 'Dental Services' ? '#065f46' : '#6b21a8') }};
                                border: 1px solid {{ $queue->service_type === 'Medical Consultation' ? '#93c5fd' : 
                                   ($queue->service_type === 'Dental Services' ? '#6ee7b7' : '#d8b4fe') }};">
                                <i class="fas fa-{{ $queue->service_type === 'Medical Consultation' ? 'stethoscope' : 
                                   ($queue->service_type === 'Dental Services' ? 'tooth' : 'heartbeat') }}"></i>
                                {{ $queue->service_type }}
                            </span>
                        </td>
                        <td>
                            <i class="fas fa-file-medical" style="color: #9ca3af;"></i>
                            {{ $queue->consultation->diagnosis ?? 'N/A' }}
                        </td>
                        <td>
                            @php
                                $medicines = json_decode($queue->consultation->prescribed_medicines ?? '[]', true);
                            @endphp
                            @if(is_array($medicines) && count($medicines) > 0)
                                <div style="background: #f9fafb; padding: 0.75rem; border-radius: 4px; border: 1px solid #e5e7eb;">
                                    @foreach($medicines as $med)
                                        <div style="margin-bottom: 0.5rem;">
                                            <i class="fas fa-capsules" style="color: #059669;"></i>
                                            <strong>{{ $med['name'] ?? 'Unknown' }}</strong>
                                            <span style="color: #3b82f6; font-weight: 600;">{{ $med['quantity'] ?? 0 }} {{ $med['unit'] ?? 'pcs' }}</span>
                                            @if(!empty($med['instructions']))
                                                <br><small style="color: #6b7280; margin-left: 1.25rem;">
                                                    <i class="fas fa-info-circle"></i> {{ $med['instructions'] }}
                                                </small>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <span style="color: #9ca3af; font-style: italic;">
                                    <i class="fas fa-times-circle"></i> No medicines prescribed
                                </span>
                            @endif
                        </td>
                        <td>
                            <i class="fas fa-calendar-alt" style="color: #9ca3af;"></i>
                            {{ $queue->consultation->created_at->format('M d, Y') }}<br>
                            <small style="color: #6b7280;">{{ $queue->consultation->created_at->format('h:i A') }}</small>
                        </td>
                        <td style="text-align: center;">
                            @if(is_array($medicines) && count($medicines) > 0)
                                <div style="display: flex; flex-direction: column; gap: 0.5rem; align-items: center;">
                                    <a href="{{ route('pharmacy.dispense.form', $queue->id) }}" 
                                       class="btn btn-success" style="width: 100%; justify-content: center;">
                                        <i class="fas fa-check-circle"></i>
                                        Dispense
                                    </a>
                                    <form action="{{ route('pharmacy.cancel', $queue->id) }}" method="POST" style="width: 100%;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                onclick="return confirm('Are you sure you want to cancel this prescription?')"
                                                class="btn btn-danger" style="width: 100%; justify-content: center;">
                                            <i class="fas fa-times-circle"></i>
                                            Cancel
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span style="color: #9ca3af; font-style: italic;">
                                    <i class="fas fa-ban"></i> No action needed
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 3rem;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                                <div style="background: #f3f4f6; border-radius: 50%; padding: 2rem; width: 120px; height: 120px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-prescription-bottle" style="font-size: 3rem; color: #d1d5db;"></i>
                                </div>
                                <div>
                                    <h3 style="font-size: 1.25rem; font-weight: 700; color: #6b7280; margin-bottom: 0.5rem;">No Pending Prescriptions</h3>
                                    <p style="color: #9ca3af;">All prescriptions have been dispensed or there are no new prescriptions</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($prescriptions->hasPages())
        <div style="margin-top: 1.5rem; display: flex; justify-content: center;">
            {{ $prescriptions->links() }}
        </div>
    @endif

    @elseif(($tab ?? 'pending') === 'history')
    <!-- DISPENSING HISTORY TAB -->
    <div class="dashboard-section">
        <div style="margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #1f2937; margin-bottom: 0.5rem;">
                <i class="fas fa-history" style="color: #059669;"></i> Dispensing History
            </h2>
        </div>
        
        <!-- Filter Form -->
        <form method="GET" action="{{ route('pharmacy.index') }}" style="background: #f9fafb; padding: 1.5rem; border-radius: 8px; border: 1px solid #e5e7eb; margin-bottom: 1.5rem;">
            <input type="hidden" name="tab" value="history">
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                <div class="form-group">
                    <label><i class="fas fa-calendar-alt" style="color: #059669;"></i> Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-calendar-check" style="color: #059669;"></i> Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-user-search" style="color: #059669;"></i> Search Patient</label>
                    <div style="display: flex; gap: 0.5rem;">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Patient name..." style="flex: 1;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </div>
            </div>
        </form>

        @if($history && $history->count() > 0)
        <div class="data-table-container">
            <table class="data-table">
                <thead>
                    <tr style="background: #10b981;">
                        <th style="color: white;"><i class="fas fa-clock"></i> Date/Time</th>
                        <th style="color: white;"><i class="fas fa-user"></i> Patient</th>
                        <th style="color: white;"><i class="fas fa-pills"></i> Medicine</th>
                        <th style="color: white;"><i class="fas fa-barcode"></i> Batch</th>
                        <th style="color: white;"><i class="fas fa-hashtag"></i> Quantity</th>
                        <th style="color: white;"><i class="fas fa-user-md"></i> Dispensed By</th>
                        <th style="color: white; text-align: center;"><i class="fas fa-check-circle"></i> Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($history as $item)
                        <tr>
                            <td>
                                <i class="fas fa-calendar" style="color: #9ca3af;"></i>
                                {{ $item->dispensed_at->format('M d, Y') }}<br>
                                <small style="color: #6b7280;">{{ $item->dispensed_at->format('h:i A') }}</small>
                            </td>
                            <td>
                                @if($item->queue && $item->queue->patient)
                                    <div class="patient-name">
                                        <i class="fas fa-user-circle"></i>
                                        <div>
                                            <strong>{{ $item->queue->patient->full_name }}</strong><br>
                                            <small style="color: #6b7280;">ID: {{ $item->queue->patient->patient_id }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span style="color: #9ca3af; font-style: italic;">
                                        <i class="fas fa-walking"></i> Walk-in Patient
                                    </span>
                                @endif
                            </td>
                            <td>
                                <i class="fas fa-capsules" style="color: #10b981;"></i>
                                <strong>{{ $item->medicine->name ?? 'N/A' }}</strong>
                            </td>
                            <td>
                                <span style="background: #f3f4f6; padding: 0.25rem 0.5rem; border-radius: 4px; font-family: monospace;">
                                    {{ $item->batch->batch_number ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <strong style="color: #3b82f6; font-size: 1.125rem;">{{ $item->quantity }}</strong>
                                <span style="color: #6b7280;">{{ $item->medicine->unit ?? 'pcs' }}</span>
                            </td>
                            <td>
                                <i class="fas fa-user-nurse" style="color: #8b5cf6;"></i>
                                {{ $item->dispensedByUser->name ?? 'N/A' }}
                            </td>
                            <td style="text-align: center;">
                                <span class="btn-sm" style="
                                    background: {{ $item->status === 'dispensed' ? '#d1fae5' : '#fee2e2' }};
                                    color: {{ $item->status === 'dispensed' ? '#065f46' : '#991b1b' }};
                                    border: 1px solid {{ $item->status === 'dispensed' ? '#6ee7b7' : '#fecaca' }};">
                                    <i class="fas fa-{{ $item->status === 'dispensed' ? 'check-circle' : 'times-circle' }}"></i>
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($history->hasPages())
            <div style="margin-top: 1.5rem; display: flex; justify-content: center;">
                {{ $history->appends(['tab' => 'history', 'date_from' => request('date_from'), 'date_to' => request('date_to'), 'search' => request('search')])->links() }}
            </div>
        @endif
        @else
            <div style="text-align: center; padding: 3rem; background: white; border-radius: 8px; border: 1px solid #e5e7eb;">
                <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                    <div style="background: #f3f4f6; border-radius: 50%; padding: 2rem; width: 120px; height: 120px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-inbox" style="font-size: 3rem; color: #d1d5db;"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 700; color: #6b7280; margin-bottom: 0.5rem;">No Dispensing History Found</h3>
                        <p style="color: #9ca3af;">Try adjusting your filter criteria</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @elseif(($tab ?? 'pending') === 'alerts')
    <!-- STOCK ALERTS TAB -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- Expired Medicines Alert -->
        @if($alerts && isset($alerts['expired']) && $alerts['expired']->count() > 0)
        <div class="dashboard-section" style="border-left: 6px solid #dc2626; background: #fef2f2;">
            <div style="margin-bottom: 1rem;">
                <h3 style="font-size: 1.5rem; font-weight: 700; color: #991b1b;">
                    <i class="fas fa-skull-crossbones"></i> EXPIRED MEDICINES
                    <span class="btn-sm btn-danger" style="margin-left: 1rem;">{{ $alerts['expired']->count() }} items</span>
                </h3>
            </div>
            <div class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr style="background: #dc2626;">
                            <th style="color: white;"><i class="fas fa-pills"></i> Medicine</th>
                            <th style="color: white;"><i class="fas fa-barcode"></i> Batch</th>
                            <th style="color: white;"><i class="fas fa-calendar-times"></i> Expiry Date</th>
                            <th style="color: white;"><i class="fas fa-boxes"></i> Quantity</th>
                            <th style="color: white;"><i class="fas fa-tasks"></i> Action Required</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alerts['expired'] as $batch)
                            <tr style="background: #fee2e2;">
                                <td><i class="fas fa-capsules" style="color: #dc2626;"></i> <strong>{{ $batch->medicine->name }}</strong></td>
                                <td><code>{{ $batch->batch_number }}</code></td>
                                <td style="color: #dc2626; font-weight: 700;">{{ $batch->expiry_date->format('M d, Y') }}</td>
                                <td><strong>{{ $batch->quantity }}</strong> {{ $batch->medicine->unit }}</td>
                                <td><span class="btn-sm btn-danger"><i class="fas fa-trash-alt"></i> DISPOSE IMMEDIATELY</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Out of Stock Alert -->
        @if($alerts && isset($alerts['out_of_stock']) && $alerts['out_of_stock']->count() > 0)
        <div class="dashboard-section" style="border-left: 6px solid #dc2626; background: #fef2f2;">
            <div style="margin-bottom: 1rem;">
                <h3 style="font-size: 1.5rem; font-weight: 700; color: #991b1b;">
                    <i class="fas fa-box-open"></i> OUT OF STOCK
                    <span class="btn-sm btn-danger" style="margin-left: 1rem;">{{ $alerts['out_of_stock']->count() }} items</span>
                </h3>
            </div>
            <div class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr style="background: #dc2626;">
                            <th style="color: white;"><i class="fas fa-pills"></i> Medicine</th>
                            <th style="color: white;"><i class="fas fa-ruler"></i> Unit</th>
                            <th style="color: white;"><i class="fas fa-inventory"></i> Current Stock</th>
                            <th style="color: white;"><i class="fas fa-tasks"></i> Action Required</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alerts['out_of_stock'] as $medicine)
                            <tr style="background: #fee2e2;">
                                <td><i class="fas fa-capsules" style="color: #dc2626;"></i> <strong>{{ $medicine->name }}</strong></td>
                                <td>{{ $medicine->unit }}</td>
                                <td><span class="btn-sm btn-danger">0</span></td>
                                <td><span class="btn-sm btn-danger"><i class="fas fa-shopping-cart"></i> RESTOCK URGENTLY</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Low Stock Alert -->
        @if($alerts && isset($alerts['low_stock']) && $alerts['low_stock']->count() > 0)
        <div class="dashboard-section" style="border-left: 6px solid #f59e0b; background: #fffbeb;">
            <div style="margin-bottom: 1rem;">
                <h3 style="font-size: 1.5rem; font-weight: 700; color: #92400e;">
                    <i class="fas fa-exclamation-triangle"></i> LOW STOCK WARNING
                    <span class="btn-sm btn-warning" style="margin-left: 1rem;">{{ $alerts['low_stock']->count() }} items</span>
                </h3>
            </div>
            <div class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr style="background: #f59e0b;">
                            <th style="color: white;"><i class="fas fa-pills"></i> Medicine</th>
                            <th style="color: white;"><i class="fas fa-ruler"></i> Unit</th>
                            <th style="color: white;"><i class="fas fa-inventory"></i> Current Stock</th>
                            <th style="color: white;"><i class="fas fa-chart-line"></i> Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alerts['low_stock'] as $medicine)
                            <tr style="background: #fef3c7;">
                                <td><i class="fas fa-capsules" style="color: #f59e0b;"></i> <strong>{{ $medicine->name }}</strong></td>
                                <td>{{ $medicine->unit }}</td>
                                <td style="font-size: 1.25rem; font-weight: 700; color: {{ $medicine->stock <= 5 ? '#dc2626' : '#f59e0b' }};">
                                    {{ $medicine->stock }}
                                </td>
                                <td>
                                    <span class="btn-sm" style="background: {{ $medicine->stock <= 5 ? '#fee2e2' : '#fef3c7' }}; 
                                                                color: {{ $medicine->stock <= 5 ? '#991b1b' : '#92400e' }};
                                                                border: 1px solid {{ $medicine->stock <= 5 ? '#fecaca' : '#fde68a' }};">
                                        <i class="fas fa-{{ $medicine->stock <= 5 ? 'radiation' : 'battery-quarter' }}"></i>
                                        {{ $medicine->stock <= 5 ? 'CRITICAL LEVEL' : 'LOW STOCK' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Expiring Soon Alert -->
        @if($alerts && isset($alerts['expiring_soon']) && $alerts['expiring_soon']->count() > 0)
        <div class="dashboard-section" style="border-left: 6px solid #f97316; background: #fff7ed;">
            <div style="margin-bottom: 1rem;">
                <h3 style="font-size: 1.5rem; font-weight: 700; color: #9a3412;">
                    <i class="fas fa-clock"></i> EXPIRING SOON
                    <span class="btn-sm" style="background: #f97316; color: white; margin-left: 1rem;">{{ $alerts['expiring_soon']->count() }} batches</span>
                </h3>
                <p style="margin-top: 0.5rem; color: #9a3412;">
                    <i class="fas fa-info-circle"></i> Priority dispensing recommended - Use these batches first (FEFO principle)
                </p>
            </div>
            <div class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr style="background: #f97316;">
                            <th style="color: white;"><i class="fas fa-pills"></i> Medicine</th>
                            <th style="color: white;"><i class="fas fa-barcode"></i> Batch</th>
                            <th style="color: white;"><i class="fas fa-calendar-alt"></i> Expiry Date</th>
                            <th style="color: white;"><i class="fas fa-hourglass-half"></i> Days Left</th>
                            <th style="color: white;"><i class="fas fa-boxes"></i> Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alerts['expiring_soon'] as $batch)
                            @php
                                $daysLeft = now()->diffInDays($batch->expiry_date, false);
                            @endphp
                            <tr style="background: {{ $daysLeft <= 7 ? '#fee2e2' : '#ffedd5' }};">
                                <td><i class="fas fa-capsules" style="color: #f97316;"></i> <strong>{{ $batch->medicine->name }}</strong></td>
                                <td><code>{{ $batch->batch_number }}</code></td>
                                <td style="color: {{ $daysLeft <= 7 ? '#dc2626' : '#f97316' }}; font-weight: 700;">
                                    {{ $batch->expiry_date->format('M d, Y') }}
                                </td>
                                <td>
                                    <span class="btn-sm" style="background: {{ $daysLeft <= 7 ? '#dc2626' : '#f97316' }}; color: white;">
                                        <i class="fas fa-{{ $daysLeft <= 7 ? 'exclamation-triangle' : 'clock' }}"></i>
                                        {{ $daysLeft }} days
                                    </span>
                                </td>
                                <td><strong>{{ $batch->quantity }}</strong> {{ $batch->medicine->unit }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($alerts && $alerts['out_of_stock']->count() === 0 && $alerts['low_stock']->count() === 0 && $alerts['expiring_soon']->count() === 0 && $alerts['expired']->count() === 0)
            <div class="dashboard-section" style="border-left: 6px solid #10b981; background: #f0fdf4; text-align: center; padding: 3rem;">
                <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                    <div style="background: #10b981; border-radius: 50%; padding: 2rem; width: 120px; height: 120px; display: flex; align-items: center; justify-center;">
                        <i class="fas fa-check-circle" style="font-size: 4rem; color: white;"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 2rem; font-weight: 700; color: #065f46; margin-bottom: 0.5rem;">All Clear!</h3>
                        <p style="font-size: 1.125rem; color: #059669;">No stock alerts at this time</p>
                        <p style="color: #10b981; margin-top: 0.5rem;">Inventory is healthy and well-managed 🎉</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
    @endif
</div>
@endsection
