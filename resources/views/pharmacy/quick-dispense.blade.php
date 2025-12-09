@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('pharmacy.index') }}" class="text-blue-600 hover:text-blue-800">
            ← Back to Pharmacy
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-green-600 text-white px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold">Quick Dispense</h1>
                    <p class="text-green-100 mt-1">For walk-in patients and paper prescriptions</p>
                </div>
                <div class="text-5xl">💊</div>
            </div>
        </div>

        @if(session('error'))
            <div class="mx-6 mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Quick Dispense Form -->
        <form action="{{ route('pharmacy.quick-dispense.store') }}" method="POST" id="quickDispenseForm">
            @csrf
            
            <div class="px-6 py-4">
                <!-- Patient Information -->
                <div class="mb-6 p-4 bg-gray-50 border rounded-lg">
                    <h2 class="text-lg font-bold mb-4 text-gray-800">Patient Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Patient Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="patient_name" 
                                   value="{{ old('patient_name') }}"
                                   required
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="e.g., Juan Dela Cruz">
                            @error('patient_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Contact Number (Optional)
                            </label>
                            <input type="text" 
                                   name="patient_contact" 
                                   value="{{ old('patient_contact') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="e.g., 09171234567">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Prescription Source <span class="text-red-500">*</span>
                        </label>
                        <select name="prescription_source" 
                                required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">-- Select Source --</option>
                            <option value="Paper Prescription" {{ old('prescription_source') == 'Paper Prescription' ? 'selected' : '' }}>Paper Prescription</option>
                            <option value="External Doctor" {{ old('prescription_source') == 'External Doctor' ? 'selected' : '' }}>External Doctor</option>
                            <option value="Emergency" {{ old('prescription_source') == 'Emergency' ? 'selected' : '' }}>Emergency</option>
                            <option value="Refill" {{ old('prescription_source') == 'Refill' ? 'selected' : '' }}>Refill</option>
                            <option value="Walk-in" {{ old('prescription_source') == 'Walk-in' ? 'selected' : '' }}>Walk-in</option>
                            <option value="Other" {{ old('prescription_source') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('prescription_source')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Medicines to Dispense -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-bold text-gray-800">Medicines to Dispense</h2>
                        <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded" onclick="addMedicineRow()">
                            <i class="fas fa-plus"></i> Add Medicine
                        </button>
                    </div>

                    <div id="medicinesContainer" class="space-y-4">
                        <!-- Medicine rows will be added here dynamically -->
                    </div>
                </div>

                <!-- Notes -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Dispensing Notes (Optional)
                    </label>
                    <textarea name="notes" 
                              rows="3" 
                              class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              placeholder="Any additional notes about this dispensing...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t flex justify-between items-center">
                <a href="{{ route('pharmacy.index') }}" 
                   class="text-gray-600 hover:text-gray-800">
                    Cancel
                </a>
                <button type="submit" 
                        id="submitBtn"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded font-medium">
                    ✓ Dispense Medicines
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Available medicines and batches from server
const medicines = @json($medicines);
const batches = @json($batches);
let medicineCounter = 0;

function addMedicineRow() {
    const container = document.getElementById('medicinesContainer');
    const index = medicineCounter++;
    
    const medicineRow = document.createElement('div');
    medicineRow.className = 'medicine-row p-4 border rounded-lg bg-gray-50';
    medicineRow.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Medicine <span class="text-red-500">*</span>
                </label>
                <select name="medicines[${index}][medicine_id]" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 medicine-select" 
                        required 
                        onchange="updateBatches(this, ${index})">
                    <option value="">-- Select Medicine --</option>
                    ${medicines.map(m => `
                        <option value="${m.id}" data-stock="${m.stock}">
                            ${m.name} (Stock: ${m.stock})
                        </option>
                    `).join('')}
                </select>
            </div>

            <div class="md:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Batch (FEFO) <span class="text-red-500">*</span>
                </label>
                <select name="medicines[${index}][batch_id]" 
                        id="batch-select-${index}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                        required>
                    <option value="">-- Select Medicine First --</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Quantity <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       name="medicines[${index}][quantity]" 
                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                       min="1" 
                       step="1" 
                       required 
                       placeholder="0">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Instructions
                </label>
                <input type="text" 
                       name="medicines[${index}][instructions]" 
                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                       placeholder="e.g., 3x daily">
            </div>
        </div>

        <div class="mt-3 flex justify-end">
            <button type="button" 
                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm" 
                    onclick="removeMedicineRow(this)">
                <i class="fas fa-trash"></i> Remove
            </button>
        </div>
    `;
    
    container.appendChild(medicineRow);
}

function updateBatches(selectElement, index) {
    const medicineId = selectElement.value;
    const batchSelect = document.getElementById(`batch-select-${index}`);
    
    // Clear existing options
    batchSelect.innerHTML = '<option value="">-- Select Batch --</option>';
    
    if (!medicineId) return;
    
    // Filter batches for selected medicine and sort by expiry (FEFO)
    const medicineBatches = batches.filter(b => b.medicine_id == medicineId && b.quantity > 0);
    
    if (medicineBatches.length === 0) {
        batchSelect.innerHTML = '<option value="">-- No batches available --</option>';
        return;
    }
    
    medicineBatches.forEach((batch, idx) => {
        const expiryDate = new Date(batch.expiry_date);
        const today = new Date();
        const daysToExpiry = Math.ceil((expiryDate - today) / (1000 * 60 * 60 * 24));
        
        let statusText = '';
        if (daysToExpiry < 0) {
            statusText = '⚠️ EXPIRED';
        } else if (daysToExpiry <= 90) {
            statusText = `⚠️ ${daysToExpiry} days`;
        } else {
            statusText = `${daysToExpiry} days`;
        }
        
        const option = document.createElement('option');
        option.value = batch.id;
        option.textContent = `${batch.batch_number} | Qty: ${batch.quantity} | Exp: ${expiryDate.toLocaleDateString()} (${statusText})`;
        if (idx === 0) {
            option.selected = true; // Auto-select first (oldest) batch
        }
        batchSelect.appendChild(option);
    });
}

function removeMedicineRow(button) {
    button.closest('.medicine-row').remove();
}

// Add first medicine row on page load
window.addEventListener('DOMContentLoaded', () => {
    addMedicineRow();
});

// Form validation
document.getElementById('quickDispenseForm').addEventListener('submit', function(e) {
    const medicineRows = document.querySelectorAll('.medicine-row');
    
    if (medicineRows.length === 0) {
        e.preventDefault();
        alert('Please add at least one medicine to dispense.');
        return false;
    }
    
    // Disable submit button to prevent double submission
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').innerHTML = '⏳ Processing...';
});
</script>
@endsection
