@extends('layouts.app')

@section('title', 'Medicine Inventory - Mabini Health Center')
@section('page-title', 'Medicine Inventory')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h2><i class="fas fa-pills"></i> Medicine Inventory</h2>
            <p>Monitor and manage medicine stock levels</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('staff.queue.management') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Queue
            </a>
            <a href="{{ route('medicines.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Medicine
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Quick Staff Tools Navigation -->
    <div class="quick-tools-card">
        <h4><i class="fas fa-toolbox"></i> Staff Quick Tools</h4>
        <div class="quick-tools-grid">
            <a href="{{ route('staff.queue.management') }}" class="tool-link">
                <i class="fas fa-tasks"></i>
                <span>Queue Management</span>
            </a>
            <a href="{{ route('staff.medicine.inventory') }}" class="tool-link active">
                <i class="fas fa-pills"></i>
                <span>Medicine Inventory</span>
            </a>
            <a href="{{ route('queue.display') }}" class="tool-link">
                <i class="fas fa-tv"></i>
                <span>Queue Display</span>
            </a>
            <a href="{{ route('qr.scanner') }}" class="tool-link">
                <i class="fas fa-qrcode"></i>
                <span>QR Scanner</span>
            </a>
            <a href="{{ route('staff.print.queue') }}" class="tool-link" target="_blank">
                <i class="fas fa-print"></i>
                <span>Print Queue</span>
            </a>
            <a href="{{ route('staff.queue.requests') }}" class="tool-link">
                <i class="fas fa-clipboard-check"></i>
                <span>Queue Requests</span>
            </a>
        </div>
    </div>

    <div class="stats-row">
        <div class="stat-card stat-total">
            <div class="stat-icon">
                <i class="fas fa-pills"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $medicines->count() }}</h3>
                <p>Total Medicines</p>
            </div>
        </div>

        <div class="stat-card stat-low">
            <div class="stat-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $medicines->where('stock', '<=', 25)->count() }}</h3>
                <p>Low Stock</p>
            </div>
        </div>

        <div class="stat-card stat-out">
            <div class="stat-icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $medicines->where('stock', '<=', 0)->count() }}</h3>
                <p>Out of Stock</p>
            </div>
        </div>

        <div class="stat-card stat-good">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $medicines->where('stock', '>', 25)->count() }}</h3>
                <p>Good Stock</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-list"></i> Medicine Inventory</h3>
            <div class="filter-controls">
                <input 
                    type="text" 
                    id="searchInput" 
                    class="form-control" 
                    placeholder="Search medicines..."
                    onkeyup="searchMedicines()"
                >
                <select id="stockFilter" class="form-control" onchange="filterByStock()">
                    <option value="">All Stock Levels</option>
                    <option value="out">Out of Stock</option>
                    <option value="low">Low Stock (≤25)</option>
                    <option value="good">Good Stock (>25)</option>
                </select>
            </div>
        </div>

        <div class="card-body">
            @if($medicines->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-pills" style="font-size: 4rem; color: #d1d5db;"></i>
                    <p style="color: #6b7280; margin-top: 1rem; font-size: 1.125rem;">No medicines in inventory</p>
                    <a href="{{ route('medicines.create') }}" class="btn btn-primary" style="margin-top: 1rem;">
                        <i class="fas fa-plus"></i> Add First Medicine
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table" id="medicineTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Dosage</th>
                                <th>Type</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($medicines as $medicine)
                                <tr data-stock="{{ $medicine->stock }}">
                                    <td>{{ $medicine->id }}</td>
                                    <td>
                                        <div class="medicine-info">
                                            <strong>{{ $medicine->name }}</strong>
                                            @if($medicine->generic_name)
                                                <small>{{ $medicine->generic_name }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $medicine->dosage }}</td>
                                    <td>
                                        <span class="badge badge-type">{{ $medicine->type }}</span>
                                    </td>
                                    <td>
                                        <div class="stock-display">
                                            <span class="stock-number stock-{{ $medicine->stock <= 0 ? 'out' : ($medicine->stock <= 25 ? 'low' : 'good') }}">
                                                {{ $medicine->stock }}
                                            </span>
                                            <small class="text-muted">{{ $medicine->unit ?? 'units' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($medicine->stock <= 0)
                                            <span class="badge badge-danger">
                                                <i class="fas fa-times-circle"></i> Out of Stock
                                            </span>
                                        @elseif($medicine->stock <= 25)
                                            <span class="badge badge-warning">
                                                <i class="fas fa-exclamation-triangle"></i> Low Stock
                                            </span>
                                        @else
                                            <span class="badge badge-success">
                                                <i class="fas fa-check-circle"></i> Good Stock
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button 
                                                type="button" 
                                                class="btn btn-sm btn-primary" 
                                                onclick="openStockModal({{ $medicine->id }}, '{{ $medicine->name }}', {{ $medicine->stock }})"
                                                title="Update Stock"
                                            >
                                                <i class="fas fa-edit"></i> Update
                                            </button>
                                            <a href="{{ route('medicines.show', $medicine->id) }}" class="btn btn-sm btn-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Update Stock Modal -->
<div id="stockModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Update Stock</h3>
            <button type="button" class="close-btn" onclick="closeStockModal()">&times;</button>
        </div>
        <form id="stockForm" method="POST">
            @csrf
            <div class="modal-body">
                <p><strong>Medicine:</strong> <span id="medicineName"></span></p>
                <p><strong>Current Stock:</strong> <span id="currentStock"></span></p>
                
                <div class="form-group">
                    <label for="stock_change">Stock Change</label>
                    <div class="input-group">
                        <button type="button" class="btn btn-secondary" onclick="decrementStock()">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input 
                            type="number" 
                            id="stock_change" 
                            name="stock_change" 
                            class="form-control" 
                            value="0"
                            required
                        >
                        <button type="button" class="btn btn-secondary" onclick="incrementStock()">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <small class="form-text">Use positive numbers to add stock, negative to reduce</small>
                </div>

                <div class="form-group">
                    <label for="reason">Reason <span style="color: red;">*</span></label>
                    <select id="reason" name="reason" class="form-control" required onchange="toggleCustomReason()">
                        <option value="">Select reason</option>
                        <option value="Restocked">Restocked</option>
                        <option value="Dispensed to patient">Dispensed to patient</option>
                        <option value="Expired">Expired</option>
                        <option value="Damaged">Damaged</option>
                        <option value="Lost">Lost</option>
                        <option value="Inventory adjustment">Inventory adjustment</option>
                        <option value="custom">Other (specify)</option>
                    </select>
                </div>

                <div class="form-group" id="customReasonGroup" style="display: none;">
                    <label for="custom_reason">Specify Reason</label>
                    <input 
                        type="text" 
                        id="custom_reason" 
                        class="form-control" 
                        placeholder="Enter custom reason..."
                    >
                </div>

                <div class="new-stock-preview">
                    <strong>New Stock:</strong> 
                    <span id="newStockPreview">0</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeStockModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Stock
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.container {
    padding: 2rem;
    max-width: 1600px;
    margin: 0 auto;
}

.quick-tools-card {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.quick-tools-card h4 {
    margin: 0 0 1rem 0;
    color: #1f2937;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.quick-tools-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
}

.tool-link {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem;
    background: #f9fafb;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    text-decoration: none;
    color: #374151;
    transition: all 0.2s;
    text-align: center;
}

.tool-link:hover {
    background: #10b981;
    color: white;
    border-color: #10b981;
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);
}

.tool-link.active {
    background: #10b981;
    color: white;
    border-color: #10b981;
}

.tool-link i {
    font-size: 1.5rem;
}

.tool-link span {
    font-weight: 500;
    font-size: 0.875rem;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.page-header h2 {
    margin: 0 0 0.5rem 0;
    color: #1f2937;
    font-size: 2rem;
}

.page-header p {
    margin: 0;
    color: #6b7280;
}

.header-actions {
    display: flex;
    gap: 1rem;
}

.stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.stat-icon {
    width: 64px;
    height: 64px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
}

.stat-total .stat-icon {
    background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
}

.stat-low .stat-icon {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.stat-out .stat-icon {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

.stat-good .stat-icon {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.stat-content h3 {
    margin: 0 0 0.25rem 0;
    font-size: 2rem;
    color: #1f2937;
}

.stat-content p {
    margin: 0;
    color: #6b7280;
    font-size: 0.875rem;
}

.card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h3 {
    margin: 0;
    color: #1f2937;
    font-size: 1.25rem;
}

.filter-controls {
    display: flex;
    gap: 1rem;
}

.card-body {
    padding: 1.5rem;
}

.table-responsive {
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th {
    background: #f9fafb;
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    color: #374151;
    border-bottom: 2px solid #e5e7eb;
}

.table td {
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
    color: #1f2937;
}

.table tbody tr:hover {
    background: #f9fafb;
}

.medicine-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.medicine-info small {
    color: #6b7280;
    font-size: 0.8125rem;
}

.stock-display {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
}

.stock-number {
    font-size: 1.25rem;
    font-weight: bold;
}

.stock-out {
    color: #ef4444;
}

.stock-low {
    color: #f59e0b;
}

.stock-good {
    color: #10b981;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.8125rem;
    font-weight: 500;
}

.badge-type {
    background: #e0e7ff;
    color: #3730a3;
}

.badge-success {
    background: #d1fae5;
    color: #065f46;
}

.badge-warning {
    background: #fef3c7;
    color: #92400e;
}

.badge-danger {
    background: #fee2e2;
    color: #991b1b;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.btn-sm {
    padding: 0.375rem 0.625rem;
    font-size: 0.8125rem;
}

.btn-primary {
    background: #10b981;
    color: white;
}

.btn-primary:hover {
    background: #059669;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
}

.btn-info {
    background: #3b82f6;
    color: white;
}

.btn-info:hover {
    background: #2563eb;
}

.form-control {
    padding: 0.625rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.875rem;
}

.form-control:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.alert {
    padding: 1rem;
    border-radius: 6px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #6ee7b7;
}

.alert-danger {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.text-muted {
    color: #9ca3af;
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
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: white;
    margin: 5% auto;
    border-radius: 8px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    color: #1f2937;
}

.close-btn {
    background: none;
    border: none;
    font-size: 2rem;
    color: #6b7280;
    cursor: pointer;
    line-height: 1;
}

.close-btn:hover {
    color: #1f2937;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    padding: 1.5rem;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #374151;
    font-weight: 500;
}

.form-text {
    display: block;
    margin-top: 0.5rem;
    font-size: 0.8125rem;
    color: #6b7280;
}

.input-group {
    display: flex;
    gap: 0.5rem;
}

.input-group .form-control {
    flex: 1;
    text-align: center;
    font-size: 1.125rem;
    font-weight: bold;
}

.input-group .btn {
    width: 40px;
    justify-content: center;
}

.new-stock-preview {
    padding: 1rem;
    background: #f9fafb;
    border-radius: 6px;
    text-align: center;
    font-size: 1.125rem;
    margin-top: 1rem;
}

.new-stock-preview span {
    color: #10b981;
    font-weight: bold;
    font-size: 1.5rem;
}
</style>

<script>
let currentStock = 0;

function searchMedicines() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('medicineTable');
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    }
}

function filterByStock() {
    const filter = document.getElementById('stockFilter').value;
    const rows = document.querySelectorAll('#medicineTable tbody tr');
    
    rows.forEach(row => {
        const stock = parseInt(row.getAttribute('data-stock'));
        let show = true;
        
        if (filter === 'out') show = stock <= 0;
        else if (filter === 'low') show = stock > 0 && stock <= 25;
        else if (filter === 'good') show = stock > 25;
        
        row.style.display = show ? '' : 'none';
    });
}

function openStockModal(medicineId, medicineName, stock) {
    currentStock = stock;
    document.getElementById('medicineName').textContent = medicineName;
    document.getElementById('currentStock').textContent = stock;
    document.getElementById('stockForm').action = `/staff/medicine/${medicineId}/update-stock`;
    document.getElementById('stock_change').value = 0;
    updateStockPreview();
    document.getElementById('stockModal').style.display = 'block';
}

function closeStockModal() {
    document.getElementById('stockModal').style.display = 'none';
    document.getElementById('stockForm').reset();
    document.getElementById('customReasonGroup').style.display = 'none';
}

function incrementStock() {
    const input = document.getElementById('stock_change');
    input.value = parseInt(input.value) + 1;
    updateStockPreview();
}

function decrementStock() {
    const input = document.getElementById('stock_change');
    input.value = parseInt(input.value) - 1;
    updateStockPreview();
}

function updateStockPreview() {
    const change = parseInt(document.getElementById('stock_change').value) || 0;
    const newStock = currentStock + change;
    document.getElementById('newStockPreview').textContent = newStock;
}

function toggleCustomReason() {
    const reason = document.getElementById('reason').value;
    const customGroup = document.getElementById('customReasonGroup');
    customGroup.style.display = reason === 'custom' ? 'block' : 'none';
}

// Update preview when stock_change input changes
document.addEventListener('DOMContentLoaded', function() {
    const stockInput = document.getElementById('stock_change');
    if (stockInput) {
        stockInput.addEventListener('input', updateStockPreview);
    }
});

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('stockModal');
    if (event.target == modal) {
        closeStockModal();
    }
}

// Handle custom reason in form submission
document.getElementById('stockForm')?.addEventListener('submit', function(e) {
    const reasonSelect = document.getElementById('reason');
    const customReason = document.getElementById('custom_reason');
    
    if (reasonSelect.value === 'custom' && customReason.value.trim()) {
        reasonSelect.value = customReason.value.trim();
    }
});
</script>
@endsection
