@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Medicine Management</h1>
                    <p class="text-muted mb-0">Administrative oversight and approval of medicine inventory</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('medicines.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-pills me-2"></i>View All Medicines
                    </a>
                    <a href="{{ route('medicines.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add New Medicine
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-white-50">Total Medicines</h6>
                            <h2 class="mb-0 mt-2">{{ number_format($medicines->total()) }}</h2>
                        </div>
                        <div>
                            <i class="fas fa-capsules fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-white-50">Out of Stock</h6>
                            <h2 class="mb-0 mt-2">{{ number_format($medicines->where('stock', 0)->count()) }}</h2>
                        </div>
                        <div>
                            <i class="fas fa-exclamation-triangle fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-white-50">Low Stock</h6>
                            <h2 class="mb-0 mt-2">{{ number_format($medicines->filter(function($m) { return $m->stock > 0 && $m->stock <= ($m->reorder_level ?? 15); })->count()) }}</h2>
                        </div>
                        <div>
                            <i class="fas fa-arrow-down fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-white-50">Expiring Soon</h6>
                            @php
                                $expiringSoon = $medicines->filter(function($m) {
                                    return $m->expiry_date && $m->expiry_date->diffInDays(now()) <= 90 && $m->expiry_date->isFuture();
                                })->count();
                            @endphp
                            <h2 class="mb-0 mt-2">{{ number_format($expiringSoon) }}</h2>
                        </div>
                        <div>
                            <i class="fas fa-clock fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.medicine.management') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input 
                            type="text" 
                            class="form-control" 
                            name="search" 
                            placeholder="Search medicines..."
                            value="{{ request('search') }}"
                        >
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="status">
                            <option value="">All Status</option>
                            <option value="in_stock" {{ request('status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                            <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                            <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                            <option value="expiring" {{ request('status') == 'expiring' ? 'selected' : '' }}>Expiring Soon</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="sort">
                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name (A-Z)</option>
                            <option value="stock_asc" {{ request('sort') == 'stock_asc' ? 'selected' : '' }}>Stock (Low to High)</option>
                            <option value="stock_desc" {{ request('sort') == 'stock_desc' ? 'selected' : '' }}>Stock (High to Low)</option>
                            <option value="expiry" {{ request('sort') == 'expiry' ? 'selected' : '' }}>Expiry Date</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Medicine Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Medicine Inventory</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Medicine Name</th>
                            <th>Dosage</th>
                            <th>Type</th>
                            <th style="width: 100px;">Stock</th>
                            <th style="width: 120px;">Reorder Level</th>
                            <th style="width: 100px;">Unit Price</th>
                            <th style="width: 120px;">Expiry Date</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 200px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($medicines as $medicine)
                        <tr>
                            <td class="text-muted">{{ $medicine->id }}</td>
                            <td>
                                <strong>{{ $medicine->name }}</strong>
                            </td>
                            <td>{{ $medicine->dosage }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $medicine->type }}</span>
                            </td>
                            <td>
                                <strong class="text-{{ $medicine->stock == 0 ? 'danger' : ($medicine->stock <= ($medicine->reorder_level ?? 15) ? 'warning' : 'success') }}">
                                    {{ $medicine->stock }}
                                </strong>
                            </td>
                            <td>{{ $medicine->reorder_level ?? 15 }}</td>
                            <td>₱{{ number_format($medicine->unit_price ?? 0, 2) }}</td>
                            <td>
                                @if($medicine->expiry_date)
                                    @php
                                        $daysUntilExpiry = $medicine->expiry_date->diffInDays(now(), false);
                                        $isExpired = $medicine->expiry_date->isPast();
                                        $isExpiringSoon = !$isExpired && $daysUntilExpiry >= -90;
                                    @endphp
                                    <small class="text-{{ $isExpired ? 'danger' : ($isExpiringSoon ? 'warning' : 'muted') }}">
                                        {{ $medicine->expiry_date->format('M d, Y') }}
                                    </small>
                                @else
                                    <small class="text-muted">N/A</small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $status = $medicine->status;
                                    $statusColor = $medicine->status_color;
                                @endphp
                                <span class="badge bg-{{ $statusColor }}">
                                    {{ $status }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('medicines.show', $medicine->id) }}" class="btn btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('medicines.edit', $medicine->id) }}" class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button 
                                        type="button" 
                                        class="btn btn-danger" 
                                        onclick="confirmDelete({{ $medicine->id }}, '{{ $medicine->name }}')"
                                        title="Delete"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <i class="fas fa-pills fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">No medicines found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($medicines->hasPages())
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">
                        Showing {{ $medicines->firstItem() }} to {{ $medicines->lastItem() }} of {{ number_format($medicines->total()) }} entries
                    </small>
                </div>
                <div>
                    {{ $medicines->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Form -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<style>
.card {
    border: none;
    border-radius: 8px;
}

.card-header {
    border-bottom: 1px solid #e9ecef;
    padding: 1rem 1.25rem;
    border-radius: 8px 8px 0 0 !important;
}

.table {
    font-size: 0.9rem;
}

.table thead th {
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
    padding: 0.75rem;
}

.table tbody td {
    padding: 0.75rem;
    vertical-align: middle;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}

.badge {
    font-weight: 500;
    padding: 0.35rem 0.65rem;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.form-control, .form-select {
    border-radius: 6px;
    border: 1px solid #dee2e6;
    padding: 0.5rem 0.75rem;
}

.btn {
    border-radius: 6px;
    font-weight: 500;
}
</style>

<script>
function confirmDelete(medicineId, medicineName) {
    if (confirm(`Are you sure you want to delete "${medicineName}"?\n\nThis action cannot be undone.`)) {
        const form = document.getElementById('deleteForm');
        form.action = `/medicines/${medicineId}`;
        form.submit();
    }
}
</script>
@endsection
