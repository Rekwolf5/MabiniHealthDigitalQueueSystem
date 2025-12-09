@extends('layouts.app')

@section('title', 'Edit Medicine - Mabini Health Center')
@section('page-title', 'Edit Medicine')

@section('content')
<div class="form-container">
    @if ($errors->any())
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <ul style="margin: 0; padding-left: 1rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('medicines.update', $medicine->id) }}" method="POST" class="medicine-form">
        @csrf
        @method('PUT')
        
        <div class="form-section">
            <h3>Medicine Information</h3>
            
            <div class="form-group">
                <label for="name">Medicine Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $medicine->name) }}" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="dosage">Dosage</label>
                    <input type="text" id="dosage" name="dosage" value="{{ old('dosage', $medicine->dosage) }}" placeholder="e.g., 500mg">
                </div>
                <div class="form-group">
                    <label for="type">Type</label>
                    <select id="type" name="type">
                        <option value="">Select Type</option>
                        <option value="Tablet" {{ old('type', $medicine->type) == 'Tablet' ? 'selected' : '' }}>Tablet</option>
                        <option value="Capsule" {{ old('type', $medicine->type) == 'Capsule' ? 'selected' : '' }}>Capsule</option>
                        <option value="Syrup" {{ old('type', $medicine->type) == 'Syrup' ? 'selected' : '' }}>Syrup</option>
                        <option value="Injection" {{ old('type', $medicine->type) == 'Injection' ? 'selected' : '' }}>Injection</option>
                        <option value="Ointment" {{ old('type', $medicine->type) == 'Ointment' ? 'selected' : '' }}>Ointment</option>
                        <option value="Drops" {{ old('type', $medicine->type) == 'Drops' ? 'selected' : '' }}>Drops</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="stock">Current Stock *</label>
                    <input type="number" id="stock" name="stock" value="{{ old('stock', $medicine->stock) }}" min="0" required>
                    <small class="form-hint">Current: {{ $medicine->stock }} units</small>
                </div>
                <div class="form-group">
                    <label for="reorder_level">Reorder Level</label>
                    <input type="number" id="reorder_level" name="reorder_level" value="{{ old('reorder_level', $medicine->reorder_level) }}" min="5" max="100">
                    <small class="form-hint">Alert when stock falls below this level</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="unit_price">Unit Price (₱)</label>
                    <input type="number" id="unit_price" name="unit_price" value="{{ old('unit_price', $medicine->unit_price) }}" min="0" step="0.01">
                </div>
                <div class="form-group">
                    <label for="expiry_date">Expiry Date</label>
                    <input 
                        type="date" 
                        id="expiry_date" 
                        name="expiry_date" 
                        value="{{ old('expiry_date', $medicine->expiry_date ? $medicine->expiry_date->format('Y-m-d') : '') }}"
                    >
                    @if($medicine->expiry_date)
                        <small class="form-hint {{ $medicine->is_expired ? 'text-danger' : '' }}">
                            {{ $medicine->is_expired ? 'Expired on ' : 'Expires on ' }}
                            {{ $medicine->expiry_date->format('M d, Y') }}
                        </small>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3">{{ old('description', $medicine->description) }}</textarea>
            </div>
        </div>

        <div class="form-section">
            <h3>Record Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <label>Added On:</label>
                    <span>{{ $medicine->created_at->format('M d, Y h:i A') }}</span>
                </div>
                <div class="info-item">
                    <label>Last Updated:</label>
                    <span>{{ $medicine->updated_at->format('M d, Y h:i A') }}</span>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('medicines.show', $medicine->id) }}" class="btn btn-secondary">Cancel</a>
            <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                <i class="fas fa-trash"></i>
                Delete Medicine
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                Update Medicine
            </button>
        </div>
    </form>

    <!-- Delete Form (hidden) -->
    <form id="deleteForm" action="{{ route('medicines.destroy', $medicine->id) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</div>

<style>
.form-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 2rem;
}

.alert {
    padding: 1rem 1.5rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.alert-error {
    background-color: #fee2e2;
    border-left: 4px solid #dc2626;
    color: #991b1b;
}

.medicine-form {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.form-section {
    padding: 2rem;
    border-bottom: 1px solid #e5e7eb;
}

.form-section:last-of-type {
    border-bottom: none;
}

.form-section h3 {
    margin: 0 0 1.5rem 0;
    color: #1f2937;
    font-size: 1.25rem;
    font-weight: 600;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #374151;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid #d1d5db;
    border-radius: 5px;
    font-size: 14px;
    transition: border-color 0.2s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #3b82f6;
}

.form-group textarea {
    resize: vertical;
}

.form-hint {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: #6b7280;
}

.form-hint.text-danger {
    color: #dc2626;
    font-weight: 500;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    padding: 1rem;
    background-color: #f9fafb;
    border-radius: 5px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.info-item label {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
}

.info-item span {
    color: #1f2937;
    font-weight: 500;
}

.form-actions {
    padding: 1.5rem 2rem;
    background-color: #f9fafb;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
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

.btn-danger {
    background-color: #dc2626;
    color: white;
}

.btn-danger:hover {
    background-color: #b91c1c;
}

@media (max-width: 768px) {
    .form-container {
        padding: 1rem;
    }

    .form-row,
    .info-grid {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column;
    }

    .form-actions .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete this medicine?\n\nThis action cannot be undone.')) {
        document.getElementById('deleteForm').submit();
    }
}
</script>
@endsection
