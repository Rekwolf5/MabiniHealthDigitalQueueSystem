@extends('layouts.app')

@section('title', 'Medicine Inventory - Mabini Health Center')
@section('page-title', 'Medicine Inventory')

@section('content')
<div class="page-header">
    <!-- Added search bar section above the medicines grid -->
    <div class="search-section" style="margin-bottom: 20px;">
        <form method="GET" action="{{ route('medicines.index') }}" style="display: flex; gap: 10px; align-items: center;">
            <input 
                type="text" 
                name="search" 
                placeholder="Search by medicine name, dosage, or type..." 
                value="{{ request('search') }}"
                style="flex: 1; padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
            >
            <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">
                <i class="fas fa-search"></i>
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('medicines.index') }}" class="btn btn-secondary" style="padding: 10px 15px;">
                    <i class="fas fa-times"></i>
                    Clear
                </a>
            @endif
        </form>
    </div>

    <div class="header-actions">
        <a href="{{ route('medicines.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Add Medicine
        </a>
    </div>
</div>

<div class="medicines-grid">
    @forelse($medicines as $medicine)
    <div class="medicine-card status-{{ strtolower(str_replace(' ', '-', $medicine['status'])) }}">
        <div class="medicine-header">
            <h4>{{ $medicine['name'] }}</h4>
            <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $medicine['status'])) }}">
                {{ $medicine['status'] }}
            </span>
        </div>
        
        <div class="medicine-details">
            <div class="detail-item">
                <i class="fas fa-boxes"></i>
                <span>Stock: {{ $medicine['stock'] }}</span>
            </div>
            <div class="detail-item">
                <i class="fas fa-calendar-alt"></i>
                <span>Expires: {{ $medicine['expiry_date'] }}</span>
            </div>
        </div>

        <div class="medicine-actions">
            <a href="{{ route('medicines.show', $medicine['id']) }}" class="btn btn-sm btn-info" title="View Details">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('medicines.edit', $medicine['id']) }}" class="btn btn-sm btn-warning" title="Edit Medicine">
                <i class="fas fa-edit"></i>
            </a>
            <button class="btn btn-sm btn-success" onclick="openRestockModal({{ $medicine['id'] }}, '{{ $medicine['name'] }}', {{ $medicine['stock'] }})" title="Restock">
                <i class="fas fa-plus"></i>
                Restock
            </button>
        </div>
    </div>
    @empty
    <!-- Show message when no medicines match search -->
    <div style="grid-column: 1 / -1; padding: 40px; text-align: center; color: #666;">
        <i class="fas fa-search" style="font-size: 48px; margin-bottom: 10px; opacity: 0.5;"></i>
        <p style="font-size: 16px; margin-top: 10px;">
            @if(request('search'))
                No medicines found matching "{{ request('search') }}"
            @else
                No medicines in inventory yet
            @endif
        </p>
    </div>
    @endempty
</div>

<!-- Restock Modal -->
<div id="restockModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-plus"></i> Restock Medicine</h3>
            <button type="button" class="close-btn" onclick="closeRestockModal()">&times;</button>
        </div>
        
        <form id="restockForm" method="POST" action="">
            @csrf
            <div class="modal-body">
                <p style="margin-bottom: 15px; padding: 10px; background: #f0fdf4; border-left: 3px solid #10b981; font-size: 14px;">
                    <strong id="medicineName"></strong><br>
                    <span style="color: #666;">Current stock: <strong id="currentStock"></strong> units</span>
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
                    New total: <strong id="newStockDisplay">0</strong> units
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
</style>

<script>
let modalCurrentStock = 0;

function openRestockModal(medicineId, medicineName, currentStock) {
    modalCurrentStock = currentStock;
    
    document.getElementById('medicineName').textContent = medicineName;
    document.getElementById('currentStock').textContent = currentStock;
    document.getElementById('stock_change').value = 1;
    
    // Set form action URL
    const form = document.getElementById('restockForm');
    form.action = `/staff/medicine/${medicineId}/update-stock`;
    
    updateNewStock();
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
    const newStock = modalCurrentStock + stockChange;
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
