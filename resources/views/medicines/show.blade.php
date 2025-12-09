@extends('layouts.app')

@section('title', 'Medicine Details - Mabini Health Center')
@section('page-title', 'Medicine Details')

@section('content')
<div class="medicine-details-container">
    <div class="page-header">
        <div class="header-actions">
            <a href="{{ route('medicines.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to Inventory
            </a>
            <a href="{{ route('medicines.edit', $medicine->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i>
                Edit Medicine
            </a>
            <button type="button" class="btn btn-success" onclick="openRestockModal()">
                <i class="fas fa-plus"></i>
                Restock
            </button>
        </div>
    </div>

    <div class="details-grid">
        <!-- Main Information Card -->
        <div class="info-card">
            <div class="card-header">
                <h3><i class="fas fa-pills"></i> Medicine Information</h3>
                <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $medicine->status)) }}">
                    {{ $medicine->status }}
                </span>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <label><i class="fas fa-capsules"></i> Medicine Name:</label>
                    <span class="value">{{ $medicine->name }}</span>
                </div>
                
                @if($medicine->description)
                <div class="info-row">
                    <label><i class="fas fa-info-circle"></i> Description:</label>
                    <span class="value">{{ $medicine->description }}</span>
                </div>
                @endif

                <div class="info-row">
                    <label><i class="fas fa-prescription"></i> Dosage:</label>
                    <span class="value">{{ $medicine->dosage ?? 'Not specified' }}</span>
                </div>

                <div class="info-row">
                    <label><i class="fas fa-tag"></i> Type:</label>
                    <span class="value">{{ $medicine->type ?? 'Not specified' }}</span>
                </div>

                <div class="info-row">
                    <label><i class="fas fa-dollar-sign"></i> Unit Price:</label>
                    <span class="value">₱{{ number_format($medicine->unit_price ?? 0, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Stock Information Card -->
        <div class="info-card">
            <div class="card-header">
                <h3><i class="fas fa-boxes"></i> Stock Information</h3>
            </div>
            <div class="card-body">
                <div class="stock-display">
                    <div class="stock-number status-{{ strtolower(str_replace(' ', '-', $medicine->status)) }}">
                        {{ $medicine->stock }}
                    </div>
                    <div class="stock-label">Units Available</div>
                </div>

                <div class="info-row">
                    <label><i class="fas fa-exclamation-triangle"></i> Reorder Level:</label>
                    <span class="value">{{ $medicine->reorder_level ?? 15 }} units</span>
                </div>

                <div class="info-row">
                    <label><i class="fas fa-calendar-alt"></i> Expiry Date:</label>
                    <span class="value {{ $medicine->is_expired ? 'text-danger' : '' }}">
                        {{ $medicine->expiry_date ? $medicine->expiry_date->format('M d, Y') : 'Not specified' }}
                        @if($medicine->expires_in_days !== null && $medicine->expires_in_days >= 0 && $medicine->expires_in_days <= 30)
                            <span class="text-warning"> ({{ $medicine->expires_in_days }} days left)</span>
                        @elseif($medicine->is_expired)
                            <span class="text-danger"> (Expired)</span>
                        @endif
                    </span>
                </div>

                <div class="info-row">
                    <label><i class="fas fa-clock"></i> Added:</label>
                    <span class="value">{{ $medicine->created_at->format('M d, Y h:i A') }}</span>
                </div>

                <div class="info-row">
                    <label><i class="fas fa-sync"></i> Last Updated:</label>
                    <span class="value">{{ $medicine->updated_at->format('M d, Y h:i A') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Alert Section -->
    @if($medicine->status !== 'In Stock')
    <div class="alert alert-{{ $medicine->status_color }}" style="margin-top: 20px;">
        <i class="fas fa-exclamation-circle"></i>
        <strong>{{ $medicine->status }}:</strong>
        
        @if($medicine->status === 'Out of Stock')
            This medicine is currently out of stock. Please restock immediately.
        @elseif($medicine->status === 'Critical')
            Critical stock level! Only {{ $medicine->stock }} unit(s) remaining.
        @elseif($medicine->status === 'Low Stock')
            Stock is running low. Consider restocking soon.
        @elseif($medicine->status === 'Expired' || $medicine->status === 'Has Expired Batches')
            This medicine has expired batches and should be reviewed.
        @elseif($medicine->status === 'Expiring Soon')
            This medicine has batches expiring soon.
        @endif
    </div>
    @endif

    <!-- Medicine Batches Section -->
    @if($medicine->batches->count() > 0)
    <div class="batches-section" style="margin-top: 20px;">
        <div class="section-header">
            <h3><i class="fas fa-layer-group"></i> Medicine Batches ({{ $medicine->batches->count() }})</h3>
            <span class="total-from-batches">Total from batches: <strong>{{ $medicine->total_stock_from_batches }} units</strong></span>
        </div>
        
        <div class="batches-grid">
            @foreach($medicine->batches()->orderBy('expiry_date', 'asc')->get() as $batch)
            <div class="batch-card status-{{ strtolower(str_replace(' ', '-', $batch->status)) }}">
                <div class="batch-header">
                    <div class="batch-number">
                        <i class="fas fa-barcode"></i>
                        <strong>{{ $batch->batch_number ?? 'N/A' }}</strong>
                    </div>
                    <span class="batch-status badge-{{ $batch->status_color }}">{{ $batch->status }}</span>
                </div>
                
                <div class="batch-body">
                    <div class="batch-detail">
                        <i class="fas fa-boxes"></i>
                        <span>Quantity: <strong>{{ $batch->quantity }} units</strong></span>
                    </div>
                    <div class="batch-detail">
                        <i class="fas fa-calendar-times"></i>
                        <span>Expires: <strong>{{ $batch->expiry_date->format('M d, Y') }}</strong></span>
                        @if($batch->is_expiring_soon && !$batch->is_expired)
                            <span class="expiry-warning">({{ $batch->expires_in_days }} days left)</span>
                        @elseif($batch->is_expired)
                            <span class="expired-text">(Expired)</span>
                        @endif
                    </div>
                    <div class="batch-detail">
                        <i class="fas fa-calendar-check"></i>
                        <span>Received: {{ $batch->received_date->format('M d, Y') }}</span>
                    </div>
                    @if($batch->supplier)
                    <div class="batch-detail">
                        <i class="fas fa-truck"></i>
                        <span>Supplier: {{ $batch->supplier }}</span>
                    </div>
                    @endif
                    @if($batch->notes)
                    <div class="batch-detail">
                        <i class="fas fa-sticky-note"></i>
                        <span>Notes: {{ $batch->notes }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="alert alert-info" style="margin-top: 20px;">
        <i class="fas fa-info-circle"></i>
        <strong>No batches yet:</strong> Click "Restock" to add your first batch with proper tracking.
    </div>
    @endif
</div>

<!-- Restock Modal -->
<div id="restockModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-plus"></i> Restock Medicine</h3>
            <button type="button" class="close-btn" onclick="closeRestockModal()">&times;</button>
        </div>
        
        <form method="POST" action="{{ route('staff.medicine.update-stock', $medicine->id) }}">
            @csrf
            <div class="modal-body">
                <p style="margin-bottom: 15px; padding: 10px; background: #f0fdf4; border-left: 3px solid #10b981; font-size: 14px;">
                    <strong>{{ $medicine->name }}</strong><br>
                    <span style="color: #666;">Current stock: <strong>{{ $medicine->stock }} units</strong></span>
                </p>

                <div class="form-row-compact">
                    <div class="form-group">
                        <label for="batch_number">Batch Number *</label>
                        <input 
                            type="text" 
                            id="batch_number" 
                            name="batch_number" 
                            class="form-control-sm" 
                            placeholder="LOT2025001"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="stock_change">Quantity *</label>
                        <div class="stock-input-group-compact">
                            <button type="button" class="stock-btn-sm" onclick="decrementStock()">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input 
                                type="number" 
                                id="stock_change" 
                                name="stock_change" 
                                class="form-control-sm" 
                                value="10" 
                                min="1" 
                                max="10000"
                                oninput="updateNewStock()"
                                required
                            >
                            <button type="button" class="stock-btn-sm" onclick="incrementStock()">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-row-compact">
                    <div class="form-group">
                        <label for="expiry_date">Expiry Date *</label>
                        <input 
                            type="date" 
                            id="expiry_date" 
                            name="expiry_date" 
                            class="form-control-sm"
                            min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="reason">Reason *</label>
                        <select id="reason" name="reason" class="form-control-sm" required>
                            <option value="New Delivery">New Delivery</option>
                            <option value="Restocked">Restocked</option>
                            <option value="Donation">Donation</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="supplier">Supplier (Optional)</label>
                    <input 
                        type="text" 
                        id="supplier" 
                        name="supplier" 
                        class="form-control-sm" 
                        placeholder="ABC Pharmaceuticals"
                    >
                </div>

                <div class="new-stock-preview-compact">
                    <i class="fas fa-arrow-right"></i>
                    New total: <strong id="newStockDisplay">{{ $medicine->stock + 10 }}</strong> units
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeRestockModal()">Cancel</button>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-check"></i>
                    Add Batch
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.medicine-details-container {
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.page-header {
    margin-bottom: 2rem;
}

.header-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.info-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.card-header {
    background: #f9fafb;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h3 {
    margin: 0;
    font-size: 1.1rem;
    color: #1f2937;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.card-body {
    padding: 1.5rem;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 12px 0;
    border-bottom: 1px solid #f3f4f6;
}

.info-row:last-child {
    border-bottom: none;
}

.info-row label {
    font-weight: 500;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.info-row .value {
    font-weight: 500;
    color: #1f2937;
    text-align: right;
}

.stock-display {
    text-align: center;
    padding: 2rem 0;
    margin-bottom: 1rem;
}

.stock-number {
    font-size: 4rem;
    font-weight: bold;
    line-height: 1;
    margin-bottom: 0.5rem;
}

.stock-number.status-out-of-stock,
.stock-number.status-critical,
.stock-number.status-expired {
    color: #dc2626;
}

.stock-number.status-low-stock,
.stock-number.status-expiring-soon {
    color: #f59e0b;
}

.stock-number.status-in-stock {
    color: #10b981;
}

.stock-label {
    font-size: 1rem;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-badge.status-out-of-stock,
.status-badge.status-critical,
.status-badge.status-expired {
    background-color: #fee2e2;
    color: #dc2626;
}

.status-badge.status-low-stock,
.status-badge.status-expiring-soon {
    background-color: #fef3c7;
    color: #d97706;
}

.status-badge.status-in-stock {
    background-color: #d1fae5;
    color: #059669;
}

.alert {
    padding: 1rem 1.5rem;
    border-radius: 8px;
    border-left: 4px solid;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.alert.alert-danger {
    background-color: #fee2e2;
    border-color: #dc2626;
    color: #991b1b;
}

.alert.alert-warning {
    background-color: #fef3c7;
    border-color: #f59e0b;
    color: #92400e;
}

.alert.alert-success {
    background-color: #d1fae5;
    border-color: #10b981;
    color: #065f46;
}

.alert.alert-info {
    background-color: #dbeafe;
    border-color: #3b82f6;
    color: #1e40af;
}

.alert i {
    margin-top: 2px;
}

/* Batches Section */
.batches-section {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e5e7eb;
}

.section-header h3 {
    margin: 0;
    font-size: 1.25rem;
    color: #1f2937;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.total-from-batches {
    color: #6b7280;
    font-size: 0.875rem;
}

.total-from-batches strong {
    color: #10b981;
    font-size: 1.125rem;
}

.batches-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1rem;
}

.batch-card {
    background: #f9fafb;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.2s;
}

.batch-card:hover {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.batch-card.status-expired {
    border-color: #fca5a5;
    background: #fef2f2;
}

.batch-card.status-expiring-soon {
    border-color: #fcd34d;
    background: #fffbeb;
}

.batch-card.status-good {
    border-color: #86efac;
    background: #f0fdf4;
}

.batch-card.status-depleted {
    border-color: #d1d5db;
    background: #f3f4f6;
    opacity: 0.7;
}

.batch-header {
    padding: 1rem;
    background: white;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.batch-number {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: #374151;
}

.batch-number i {
    color: #6b7280;
}

.batch-status {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-danger {
    background-color: #fee2e2;
    color: #dc2626;
}

.badge-warning {
    background-color: #fef3c7;
    color: #d97706;
}

.badge-success {
    background-color: #d1fae5;
    color: #059669;
}

.badge-secondary {
    background-color: #f3f4f6;
    color: #6b7280;
}

.batch-body {
    padding: 1rem;
}

.batch-detail {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    padding: 0.5rem 0;
    font-size: 0.875rem;
    color: #374151;
}

.batch-detail i {
    color: #6b7280;
    margin-top: 2px;
    flex-shrink: 0;
}

.expiry-warning {
    color: #d97706;
    font-weight: 600;
    margin-left: 0.25rem;
}

.expired-text {
    color: #dc2626;
    font-weight: 600;
    margin-left: 0.25rem;
}

.alert i {
    margin-top: 2px;
}

.text-danger {
    color: #dc2626;
}

.text-warning {
    color: #f59e0b;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.modal.show {
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background-color: white;
    border-radius: 8px;
    width: 90%;
    max-width: 550px;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.modal-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
}

.modal-header h3 {
    margin: 0;
    font-size: 1.125rem;
    color: #1f2937;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.close-btn {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #6b7280;
}

.close-btn:hover {
    color: #1f2937;
}

.modal-body {
    padding: 1.5rem;
    overflow-y: auto;
    flex: 1;
}

.modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    flex-shrink: 0;
    background: #f9fafb;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #374151;
    font-size: 0.875rem;
}

.form-control-sm {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 5px;
    font-size: 14px;
}

.form-row-compact {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
}

.stock-input-group-compact {
    display: flex;
    align-items: center;
    gap: 5px;
}

.stock-btn-sm {
    background-color: #f9fafb;
    border: 1px solid #d1d5db;
    width: 32px;
    height: 32px;
    border-radius: 5px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    font-size: 12px;
}

.stock-btn-sm:hover {
    background-color: #10b981;
    color: white;
    border-color: #10b981;
}

.new-stock-preview-compact {
    padding: 0.75rem;
    background-color: #f0fdf4;
    border: 1px solid #86efac;
    border-radius: 5px;
    text-align: center;
    color: #166534;
    font-size: 13px;
    margin-top: 0.5rem;
}

.new-stock-preview-compact strong {
    font-size: 1.125rem;
    color: #10b981;
}

.form-control {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid #d1d5db;
    border-radius: 5px;
    font-size: 14px;
}

.form-hint {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.75rem;
    color: #6b7280;
}

.stock-input-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.stock-btn {
    background-color: #f9fafb;
    border: 1px solid #d1d5db;
    width: 40px;
    height: 40px;
    border-radius: 5px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.stock-btn:hover {
    background-color: #10b981;
    color: white;
    border-color: #10b981;
}

.new-stock-preview {
    padding: 1rem;
    background-color: #f0fdf4;
    border: 1px solid #86efac;
    border-radius: 5px;
    text-align: center;
    color: #166534;
    font-size: 14px;
}

.new-stock-preview strong {
    font-size: 1.25rem;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
    text-decoration: none;
}

.btn-primary {
    background-color: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background-color: #2563eb;
}

.btn-secondary {
    background-color: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background-color: #4b5563;
}

.btn-success {
    background-color: #10b981;
    color: white;
}

.btn-success:hover {
    background-color: #059669;
}

.btn-warning {
    background-color: #f59e0b;
    color: white;
}

.btn-warning:hover {
    background-color: #d97706;
}

@media (max-width: 768px) {
    .details-grid {
        grid-template-columns: 1fr;
    }
    
    .header-actions {
        flex-direction: column;
    }
    
    .header-actions .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
const currentStock = {{ $medicine->stock }};

function openRestockModal() {
    document.getElementById('restockModal').classList.add('show');
}

function closeRestockModal() {
    document.getElementById('restockModal').classList.remove('show');
}

function incrementStock() {
    const input = document.getElementById('stock_change');
    const currentValue = parseInt(input.value) || 0;
    input.value = currentValue + 10;
    updateNewStock();
}

function decrementStock() {
    const input = document.getElementById('stock_change');
    const currentValue = parseInt(input.value) || 0;
    if (currentValue > 10) {
        input.value = currentValue - 10;
        updateNewStock();
    }
}

function updateNewStock() {
    const stockChange = parseInt(document.getElementById('stock_change').value) || 0;
    const newStock = currentStock + stockChange;
    document.getElementById('newStockDisplay').textContent = newStock;
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('restockModal');
    if (event.target === modal) {
        closeRestockModal();
    }
}
</script>
@endsection
