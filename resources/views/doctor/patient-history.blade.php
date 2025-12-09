@extends('layouts.app')

@section('title', 'Medical History - ' . $patient->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Medical History</h1>
            <p class="text-gray-600">Complete consultation records for {{ $patient->name }}</p>
        </div>
        <a href="{{ route('doctor.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
        </a>
    </div>

    <!-- Patient Summary Card -->
    <div class="bg-white rounded-lg shadow mb-6 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <p class="text-sm text-gray-600">Patient Name</p>
                <p class="font-semibold text-gray-900">{{ $patient->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Age / Gender</p>
                <p class="font-semibold text-gray-900">{{ $patient->age }} years / {{ ucfirst($patient->gender) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Contact</p>
                <p class="font-semibold text-gray-900">{{ $patient->phone }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Total Consultations</p>
                <p class="font-semibold text-gray-900">{{ $consultations->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Consultation Timeline -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-6 text-gray-800">
            <i class="fas fa-history mr-2"></i>Consultation Timeline
        </h2>

        @if($consultations->isEmpty())
            <div class="text-center py-12">
                <i class="fas fa-file-medical-alt fa-4x text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">No consultation records found</p>
                <p class="text-sm text-gray-400 mt-2">This patient has no previous consultations</p>
            </div>
        @else
            <div class="relative">
                <!-- Timeline Line -->
                <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-gray-200"></div>

                <!-- Consultations -->
                <div class="space-y-8">
                    @foreach($consultations as $consultation)
                    <div class="relative pl-20">
                        <!-- Timeline Dot -->
                        <div class="absolute left-6 w-4 h-4 bg-blue-600 rounded-full border-4 border-white"></div>
                        
                        <!-- Consultation Card -->
                        <div class="bg-gray-50 rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow">
                            <!-- Header -->
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ $consultation->created_at->format('F d, Y') }}
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        {{ $consultation->created_at->format('g:i A') }}
                                        @if($consultation->doctor)
                                        • Dr. {{ $consultation->doctor->name }}
                                        @endif
                                        @if($consultation->queue)
                                        • Queue #{{ $consultation->queue->queue_number }}
                                        @endif
                                    </p>
                                </div>
                                <span class="text-xs text-gray-500">
                                    {{ $consultation->created_at ? $consultation->created_at->diffForHumans() : 'Recently' }}
                                </span>
                            </div>

                            <!-- Vital Signs -->
                            @if($consultation->blood_pressure || $consultation->temperature || $consultation->pulse_rate)
                            <div class="mb-4 p-4 bg-white rounded border border-gray-200">
                                <h4 class="text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-heartbeat mr-1"></i>Vital Signs
                                </h4>
                                <div class="grid grid-cols-2 md:grid-cols-5 gap-3 text-sm">
                                    @if($consultation->blood_pressure)
                                    <div>
                                        <span class="text-gray-600">BP:</span>
                                        <span class="font-medium">{{ $consultation->blood_pressure }}</span>
                                    </div>
                                    @endif
                                    @if($consultation->temperature)
                                    <div>
                                        <span class="text-gray-600">Temp:</span>
                                        <span class="font-medium">{{ $consultation->temperature }}°C</span>
                                    </div>
                                    @endif
                                    @if($consultation->pulse_rate)
                                    <div>
                                        <span class="text-gray-600">Pulse:</span>
                                        <span class="font-medium">{{ $consultation->pulse_rate }} bpm</span>
                                    </div>
                                    @endif
                                    @if($consultation->weight)
                                    <div>
                                        <span class="text-gray-600">Weight:</span>
                                        <span class="font-medium">{{ $consultation->weight }} kg</span>
                                    </div>
                                    @endif
                                    @if($consultation->height)
                                    <div>
                                        <span class="text-gray-600">Height:</span>
                                        <span class="font-medium">{{ $consultation->height }} cm</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <!-- Chief Complaint -->
                            @if($consultation->chief_complaint)
                            <div class="mb-3">
                                <p class="text-sm font-semibold text-gray-700">Chief Complaint:</p>
                                <p class="text-gray-900">{{ $consultation->chief_complaint }}</p>
                            </div>
                            @endif

                            <!-- Symptoms -->
                            @if($consultation->symptoms)
                            <div class="mb-3">
                                <p class="text-sm font-semibold text-gray-700">Symptoms:</p>
                                <p class="text-gray-900">{{ $consultation->symptoms }}</p>
                            </div>
                            @endif

                            <!-- Physical Examination -->
                            @if($consultation->physical_examination)
                            <div class="mb-3">
                                <p class="text-sm font-semibold text-gray-700">Physical Examination:</p>
                                <p class="text-gray-900">{{ $consultation->physical_examination }}</p>
                            </div>
                            @endif

                            <!-- Diagnosis -->
                            <div class="mb-3">
                                <p class="text-sm font-semibold text-gray-700">Diagnosis:</p>
                                <p class="text-gray-900 font-medium">{{ $consultation->diagnosis }}</p>
                            </div>

                            <!-- Treatment -->
                            @if($consultation->treatment)
                            <div class="mb-3">
                                <p class="text-sm font-semibold text-gray-700">Treatment Plan:</p>
                                <p class="text-gray-900">{{ $consultation->treatment }}</p>
                            </div>
                            @endif

                            <!-- Prescribed Medicines -->
                            @if($consultation->prescribed_medicines)
                            <div class="mb-3">
                                <p class="text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-prescription mr-1"></i>Prescription:
                                </p>
                                <div class="bg-white rounded border border-gray-200 overflow-hidden">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Medicine</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Dosage</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Frequency</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Duration</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach(json_decode($consultation->prescribed_medicines, true) ?? [] as $medicine)
                                            <tr>
                                                <td class="px-4 py-2 text-sm text-gray-900">
                                                    @php
                                                        $med = \App\Models\Medicine::find($medicine['medicine_id']);
                                                    @endphp
                                                    {{ $med->name ?? 'N/A' }}
                                                </td>
                                                <td class="px-4 py-2 text-sm text-gray-900">{{ $medicine['dosage'] }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-900">{{ $medicine['frequency'] }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-900">{{ $medicine['duration'] }}</td>
                                                <td class="px-4 py-2">
                                                    @if($consultation->prescription_dispensed)
                                                    <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-800">
                                                        <i class="fas fa-check mr-1"></i>Dispensed
                                                    </span>
                                                    @else
                                                    <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-800">
                                                        <i class="fas fa-clock mr-1"></i>Pending
                                                    </span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif

                            <!-- Follow-up -->
                            @if($consultation->follow_up_date)
                            <div class="mb-3">
                                <p class="text-sm font-semibold text-gray-700">Follow-up Date:</p>
                                <p class="text-gray-900">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ \Carbon\Carbon::parse($consultation->follow_up_date)->format('F d, Y') }}
                                </p>
                            </div>
                            @endif

                            <!-- Doctor's Notes -->
                            @if($consultation->doctor_notes)
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <p class="text-sm font-semibold text-gray-700">Doctor's Notes:</p>
                                <p class="text-gray-900 italic">{{ $consultation->doctor_notes }}</p>
                            </div>
                            @endif

                            <!-- Dispensing Information -->
                            @if($consultation->medicinesDispensed && $consultation->medicinesDispensed->count() > 0)
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <p class="text-sm font-semibold text-gray-700 mb-2">Dispensing Records:</p>
                                @foreach($consultation->medicinesDispensed as $dispensed)
                                <div class="text-sm text-gray-600">
                                    <i class="fas fa-pills mr-1"></i>
                                    {{ $dispensed->medicine->name }} - {{ $dispensed->quantity }} pcs
                                    ({{ $dispensed->dispensed_at->format('M d, Y') }})
                                </div>
                                @endforeach
                            </div>
                            @endif

                            <!-- Print Prescription Button -->
                            <div class="mt-4 pt-4 border-t border-gray-200 flex justify-end">
                                <a href="{{ route('doctor.prescription.print', $consultation->id) }}" 
                                   target="_blank"
                                   class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded transition">
                                    <i class="fas fa-print mr-2"></i>Print Prescription
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
