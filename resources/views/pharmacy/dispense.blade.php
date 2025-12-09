@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('pharmacy.index') }}" class="text-blue-600 hover:text-blue-800">
            ← Back to Pharmacy
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <!-- Patient & Queue Info -->
        <div class="bg-blue-600 text-white px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold">Dispense Medicine</h1>
                    <p class="text-blue-100 mt-1">Queue #{{ $queue->queue_number }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-blue-100">Patient</p>
                    <p class="text-lg font-bold">{{ $queue->patient->name }}</p>
                    <p class="text-sm text-blue-100">ID: {{ $queue->patient->patient_id }}</p>
                </div>
            </div>
        </div>

        <!-- Consultation Info -->
        <div class="px-6 py-4 bg-gray-50 border-b">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Service Type</p>
                    <p class="font-medium">{{ $queue->service_type }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Consultation Date</p>
                    <p class="font-medium">{{ $queue->consultation->created_at->format('M d, Y h:i A') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Diagnosis</p>
                    <p class="font-medium">{{ $queue->consultation->diagnosis }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Treatment</p>
                    <p class="font-medium">{{ $queue->consultation->treatment ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        @if(session('error'))
            <div class="mx-6 mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Dispensing Form -->
        <form action="{{ route('pharmacy.dispense', $queue->id) }}" method="POST" id="dispenseForm">
            @csrf
            <div class="px-6 py-4">
                <h2 class="text-xl font-bold mb-4">Prescribed Medicines</h2>

                @php
                    $prescribedMedicines = json_decode($queue->consultation->prescribed_medicines, true);
                @endphp

                @foreach($prescribedMedicines as $index => $prescribed)
                    @php
                        $medicine = $medicines->firstWhere('id', $prescribed['medicine_id']);
                        $availableBatches = $batches->where('medicine_id', $prescribed['medicine_id'])
                                                     ->where('quantity', '>', 0);
                    @endphp

                    <div class="mb-6 p-4 border rounded-lg {{ $availableBatches->isEmpty() ? 'bg-red-50 border-red-300' : 'bg-white border-gray-300' }}">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-gray-800">
                                    {{ $prescribed['name'] ?? $medicine->name ?? 'Unknown Medicine' }}
                                </h3>
                                <p class="text-sm text-gray-600">
                                    Prescribed: <span class="font-medium">{{ $prescribed['quantity'] }} {{ $prescribed['unit'] ?? 'pcs' }}</span>
                                </p>
                                @if(!empty($prescribed['instructions']))
                                    <p class="text-sm text-gray-600 mt-1">
                                        Instructions: <span class="italic">{{ $prescribed['instructions'] }}</span>
                                    </p>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">Available Stock</p>
                                <p class="text-lg font-bold {{ $medicine && $medicine->stock >= $prescribed['quantity'] ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $medicine->stock ?? 0 }} {{ $medicine->unit ?? 'pcs' }}
                                </p>
                            </div>
                        </div>

                        @if($availableBatches->isEmpty())
                            <div class="bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded">
                                ⚠️ No stock available for this medicine
                            </div>
                        @else
                            <input type="hidden" name="medicines[{{ $index }}][medicine_id]" value="{{ $prescribed['medicine_id'] }}">
                            <input type="hidden" name="medicines[{{ $index }}][quantity]" value="{{ $prescribed['quantity'] }}">
                            <input type="hidden" name="medicines[{{ $index }}][instructions]" value="{{ $prescribed['instructions'] ?? '' }}">

                            <div class="mt-3">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Select Batch <span class="text-red-500">*</span>
                                    <span class="text-gray-500 font-normal">(FEFO - First Expiry First Out)</span>
                                </label>
                                <select name="medicines[{{ $index }}][batch_id]" 
                                        required
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">-- Select Batch --</option>
                                    @foreach($availableBatches as $batch)
                                        @php
                                            $daysToExpiry = now()->diffInDays($batch->expiry_date, false);
                                            $isExpiring = $daysToExpiry <= 90;
                                            $isExpired = $daysToExpiry < 0;
                                        @endphp
                                        <option value="{{ $batch->id }}" 
                                                {{ $loop->first ? 'selected' : '' }}
                                                class="{{ $isExpired ? 'text-red-600' : ($isExpiring ? 'text-orange-600' : '') }}">
                                            Batch: {{ $batch->batch_number }} | 
                                            Stock: {{ $batch->quantity }} | 
                                            Exp: {{ $batch->expiry_date->format('M d, Y') }}
                                            @if($isExpired)
                                                ⚠️ EXPIRED
                                            @elseif($isExpiring)
                                                ⚠️ Expiring in {{ $daysToExpiry }} days
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">
                                    💡 Batches are sorted by expiry date (oldest first) following FEFO principle
                                </p>
                            </div>

                            <!-- Batch Details Display -->
                            <div class="mt-3 p-3 bg-blue-50 rounded">
                                <p class="text-sm font-medium text-gray-700 mb-2">Available Batches:</p>
                                <div class="space-y-1">
                                    @foreach($availableBatches as $batch)
                                        @php
                                            $daysToExpiry = now()->diffInDays($batch->expiry_date, false);
                                        @endphp
                                        <div class="flex justify-between text-xs {{ $loop->first ? 'font-bold text-blue-800' : 'text-gray-600' }}">
                                            <span>
                                                {{ $loop->first ? '🔹 Recommended: ' : '' }}
                                                {{ $batch->batch_number }}
                                            </span>
                                            <span>Qty: {{ $batch->quantity }}</span>
                                            <span class="{{ $daysToExpiry < 0 ? 'text-red-600' : ($daysToExpiry <= 90 ? 'text-orange-600' : 'text-green-600') }}">
                                                Exp: {{ $batch->expiry_date->format('M d, Y') }}
                                                ({{ $daysToExpiry < 0 ? 'Expired' : $daysToExpiry . ' days' }})
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach

                <!-- Notes -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Dispensing Notes (Optional)
                    </label>
                    <textarea name="notes" 
                              rows="3" 
                              class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              placeholder="Any additional notes for this dispensing..."></textarea>
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
                    ✓ Dispense All Medicines
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Form validation
document.getElementById('dispenseForm').addEventListener('submit', function(e) {
    const selects = this.querySelectorAll('select[name$="[batch_id]"]');
    let allSelected = true;
    
    selects.forEach(select => {
        if (!select.value) {
            allSelected = false;
            select.classList.add('border-red-500');
        } else {
            select.classList.remove('border-red-500');
        }
    });
    
    if (!allSelected) {
        e.preventDefault();
        alert('Please select a batch for all medicines before dispensing.');
        return false;
    }
    
    // Disable submit button to prevent double submission
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').innerHTML = '⏳ Processing...';
});
</script>
@endsection
