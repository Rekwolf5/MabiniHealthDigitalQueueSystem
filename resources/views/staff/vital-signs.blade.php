@extends('layouts.app')

@section('title', 'Record Vital Signs')
@section('page-title', 'Record Vital Signs')

@section('content')
<div class="page-header">
    <h2>Record Vital Signs</h2>
    <p>Enter patient's vital signs before consultation</p>
</div>

<!-- Patient Info Card -->
<div class="dashboard-section" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; margin-bottom: 2rem;">
    <h3 style="color: white; margin-bottom: 1rem;">
        <i class="fas fa-user-circle"></i> Patient Information
    </h3>
    
    <div class="row">
        <div class="col-md-4">
            <p><strong><i class="fas fa-user"></i> Name:</strong> {{ $queue->patient->full_name }}</p>
        </div>
        <div class="col-md-4">
            <p><strong><i class="fas fa-birthday-cake"></i> Age:</strong> {{ $queue->patient->age ?? 'N/A' }} years</p>
        </div>
        <div class="col-md-4">
            <p><strong><i class="fas fa-venus-mars"></i> Gender:</strong> {{ ucfirst($queue->patient->gender ?? 'N/A') }}</p>
        </div>
    </div>
    <div class="row" style="margin-top: 0.5rem;">
        <div class="col-md-6">
            <p><strong><i class="fas fa-ticket-alt"></i> Queue Number:</strong> {{ $queue->queue_number }}</p>
        </div>
        <div class="col-md-6">
            <p><strong><i class="fas fa-stethoscope"></i> Service:</strong> {{ $queue->service_type }}</p>
        </div>
    </div>
</div>

<!-- Vital Signs Form -->
<div class="dashboard-section">
    <form action="{{ route('staff.vital-signs.save', $queue->id) }}" method="POST">
        @csrf
        
        <h3 style="margin-bottom: 1.5rem;">
            <i class="fas fa-heartbeat text-danger"></i> Vital Signs
        </h3>
        
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="blood_pressure">
                        <i class="fas fa-tint text-danger"></i> Blood Pressure
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="blood_pressure" 
                           name="blood_pressure" 
                           value="{{ old('blood_pressure', $queue->consultation->blood_pressure ?? '') }}"
                           placeholder="e.g., 120/80">
                    <small class="form-text text-muted">Format: systolic/diastolic (e.g., 120/80)</small>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="form-group">
                    <label for="temperature">
                        <i class="fas fa-thermometer-half text-warning"></i> Temperature (°C)
                    </label>
                    <input type="number" 
                           step="0.1" 
                           class="form-control" 
                           id="temperature" 
                           name="temperature" 
                           value="{{ old('temperature', $queue->consultation->temperature ?? '') }}"
                           placeholder="e.g., 37.5"
                           min="30"
                           max="45">
                    <small class="form-text text-muted">Normal: 36.1°C - 37.2°C</small>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="form-group">
                    <label for="pulse_rate">
                        <i class="fas fa-heartbeat text-danger"></i> Pulse Rate (bpm)
                    </label>
                    <input type="number" 
                           class="form-control" 
                           id="pulse_rate" 
                           name="pulse_rate" 
                           value="{{ old('pulse_rate', $queue->consultation->pulse_rate ?? '') }}"
                           placeholder="e.g., 72"
                           min="30"
                           max="200">
                    <small class="form-text text-muted">Normal: 60-100 beats per minute</small>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="weight">
                        <i class="fas fa-weight text-primary"></i> Weight (kg)
                    </label>
                    <input type="number" 
                           step="0.1" 
                           class="form-control" 
                           id="weight" 
                           name="weight" 
                           value="{{ old('weight', $queue->consultation->weight ?? '') }}"
                           placeholder="e.g., 65.5"
                           min="1"
                           max="500">
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="height">
                        <i class="fas fa-ruler-vertical text-info"></i> Height (cm)
                    </label>
                    <input type="number" 
                           step="0.1" 
                           class="form-control" 
                           id="height" 
                           name="height" 
                           value="{{ old('height', $queue->consultation->height ?? '') }}"
                           placeholder="e.g., 165"
                           min="30"
                           max="300">
                </div>
            </div>
        </div>
        
        <div class="form-actions" style="margin-top: 2rem;">
            <a href="{{ route('queue.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Queue
            </a>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Save Vital Signs
            </button>
        </div>
    </form>
</div>

@if($queue->consultation && ($queue->consultation->blood_pressure || $queue->consultation->temperature || $queue->consultation->pulse_rate || $queue->consultation->weight || $queue->consultation->height))
<div class="dashboard-section" style="border-left: 4px solid #28a745;">
    <h4 style="color: #28a745;">
        <i class="fas fa-check-circle"></i> Previously Recorded Vital Signs
    </h4>
    <div class="row" style="margin-top: 1rem;">
        @if($queue->consultation->blood_pressure)
        <div class="col-md-4">
            <p><strong>Blood Pressure:</strong> {{ $queue->consultation->blood_pressure }}</p>
        </div>
        @endif
        @if($queue->consultation->temperature)
        <div class="col-md-4">
            <p><strong>Temperature:</strong> {{ $queue->consultation->temperature }}°C</p>
        </div>
        @endif
        @if($queue->consultation->pulse_rate)
        <div class="col-md-4">
            <p><strong>Pulse Rate:</strong> {{ $queue->consultation->pulse_rate }} bpm</p>
        </div>
        @endif
        @if($queue->consultation->weight)
        <div class="col-md-4">
            <p><strong>Weight:</strong> {{ $queue->consultation->weight }} kg</p>
        </div>
        @endif
        @if($queue->consultation->height)
        <div class="col-md-4">
            <p><strong>Height:</strong> {{ $queue->consultation->height }} cm</p>
        </div>
        @endif
    </div>
</div>
@endif

@endsection
