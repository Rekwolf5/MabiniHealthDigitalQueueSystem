@extends('layouts.app')

@section('title', 'Add Medicine - Mabini Health Center')
@section('page-title', 'Add New Medicine')

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

    <form action="{{ route('medicines.store') }}" method="POST" class="medicine-form">
        @csrf
        
        <div class="form-section">
            <h3>Medicine Information</h3>
            
            <div class="form-group">
                <label for="name">Medicine Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="dosage">Dosage *</label>
                    <input type="text" id="dosage" name="dosage" value="{{ old('dosage') }}" placeholder="e.g., 500mg" required>
                </div>
                <div class="form-group">
                    <label for="type">Type *</label>
                    <select id="type" name="type" required>
                        <option value="">Select Type</option>
                        <option value="Tablet" {{ old('type') == 'Tablet' ? 'selected' : '' }}>Tablet</option>
                        <option value="Capsule" {{ old('type') == 'Capsule' ? 'selected' : '' }}>Capsule</option>
                        <option value="Syrup" {{ old('type') == 'Syrup' ? 'selected' : '' }}>Syrup</option>
                        <option value="Injection" {{ old('type') == 'Injection' ? 'selected' : '' }}>Injection</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="stock">Initial Stock *</label>
                    <input type="number" id="stock" name="stock" value="{{ old('stock', 0) }}" min="0" required>
                </div>
                <div class="form-group">
                    <label for="reorder_level">Reorder Level</label>
                    <input type="number" id="reorder_level" name="reorder_level" value="{{ old('reorder_level', 15) }}" min="5" max="100">
                    <small style="display: block; margin-top: 5px; color: #666; font-size: 12px;">Alert when stock falls below this level (default: 15)</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="unit_price">Unit Price (₱)</label>
                    <input type="number" id="unit_price" name="unit_price" value="{{ old('unit_price', 0) }}" min="0" step="0.01">
                </div>
                <div class="form-group">
                    <label for="expiry_date">Expiry Date *</label>
                    <input type="date" id="expiry_date" name="expiry_date" value="{{ old('expiry_date') }}" required>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description (Optional)</label>
                <textarea id="description" name="description" rows="3">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('medicines.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                Save Medicine
            </button>
        </div>
    </form>
</div>

<script>
// Debug form submission
document.querySelector('.medicine-form').addEventListener('submit', function(e) {
    console.log('Medicine form submitted');
    console.log('Form data:', new FormData(this));
});
</script>
@endsection
