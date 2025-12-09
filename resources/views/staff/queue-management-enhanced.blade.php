@extends('layouts.app')

@section('title', 'Staff Tools - Mabini Health Center')
@section('page-title', 'Staff Tools')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h2><i class="fas fa-tools"></i> Staff Tools</h2>
            <p>Comprehensive queue management, requests, and patient search</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('staff.print.queue') }}" class="btn btn-secondary" target="_blank">
                <i class="fas fa-print"></i> Print Queue
            </a>
            <a href="{{ route('queue.add') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add to Queue
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

    <!-- Statistics Dashboard -->
    <div class="stats-row" style="margin-bottom: 2rem;">
        <div class="stat-card stat-total">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-content">
                <h3>{{ $stats['total_today'] }}</h3>
                <p>Total Today</p>
            </div>
        </div>
        <div class="stat-card stat-waiting">
            <div class="stat-icon"><i class="fas fa-clock"></i></div>
            <div class="stat-content">
                <h3>{{ $stats['waiting'] }}</h3>
                <p>Waiting</p>
            </div>
        </div>
        <div class="stat-card stat-consulting">
            <div class="stat-icon"><i class="fas fa-user-md"></i></div>
            <div class="stat-content">
                <h3>{{ $stats['consulting'] }}</h3>
                <p>Consulting</p>
            </div>
        </div>
        <div class="stat-card stat-completed">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="stat-content">
                <h3>{{ $stats['completed'] }}</h3>
                <p>Completed</p>
            </div>
        </div>
        <div class="stat-card stat-pending">
            <div class="stat-icon"><i class="fas fa-bell"></i></div>
            <div class="stat-content">
                <h3>{{ $stats['pending'] }}</h3>
                <p>Pending Requests</p>
            </div>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="info-boxes" style="margin-bottom: 2rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
        <div class="info-box" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 1.5rem; border-radius: 8px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 0.875rem; opacity: 0.9;">Avg Wait Time</div>
                    <div style="font-size: 2rem; font-weight: bold; margin-top: 0.5rem;">{{ $stats['average_wait_time'] }} min</div>
                </div>
                <i class="fas fa-hourglass-half" style="font-size: 3rem; opacity: 0.3;"></i>
            </div>
        </div>
        
        <div class="info-box" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 1.5rem; border-radius: 8px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 0.875rem; opacity: 0.9;">Services Today</div>
                    <div style="font-size: 2rem; font-weight: bold; margin-top: 0.5rem;">{{ count($stats['service_breakdown']) }}</div>
                </div>
                <i class="fas fa-stethoscope" style="font-size: 3rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="card">
        <div class="tabs-container" style="border-bottom: 2px solid #e5e7eb;">
            <div class="tabs" style="display: flex; gap: 0;">
                <a href="{{ route('staff.queue.management', ['tab' => 'queue']) }}" 
                   class="tab-link {{ $tab === 'queue' ? 'active' : '' }}"
                   style="padding: 1rem 1.5rem; text-decoration: none; color: {{ $tab === 'queue' ? '#2563eb' : '#6b7280' }}; border-bottom: 3px solid {{ $tab === 'queue' ? '#2563eb' : 'transparent' }}; transition: all 0.3s;">
                    <i class="fas fa-list-ol"></i> Queue Management
                    <span class="badge" style="background: #3b82f6; color: white; padding: 2px 8px; border-radius: 12px; font-size: 11px; margin-left: 8px;">
                        {{ $stats['waiting'] + $stats['consulting'] }}
                    </span>
                </a>
                <a href="{{ route('staff.queue.management', ['tab' => 'requests']) }}" 
                   class="tab-link {{ $tab === 'requests' ? 'active' : '' }}"
                   style="padding: 1rem 1.5rem; text-decoration: none; color: {{ $tab === 'requests' ? '#2563eb' : '#6b7280' }}; border-bottom: 3px solid {{ $tab === 'requests' ? '#2563eb' : 'transparent' }}; transition: all 0.3s;">
                    <i class="fas fa-bell"></i> Pending Requests
                    @if($stats['pending'] > 0)
                        <span class="badge" style="background: #fbbf24; color: #000; padding: 2px 8px; border-radius: 12px; font-size: 11px; margin-left: 8px; font-weight: bold;">
                            {{ $stats['pending'] }}
                        </span>
                    @endif
                </a>
                <a href="{{ route('staff.queue.management', ['tab' => 'search']) }}" 
                   class="tab-link {{ $tab === 'search' ? 'active' : '' }}"
                   style="padding: 1rem 1.5rem; text-decoration: none; color: {{ $tab === 'search' ? '#2563eb' : '#6b7280' }}; border-bottom: 3px solid {{ $tab === 'search' ? '#2563eb' : 'transparent' }}; transition: all 0.3s;">
                    <i class="fas fa-search"></i> Patient Search
                </a>
            </div>
        </div>

        <div class="card-body">
            @if($tab === 'queue')
                @include('staff.partials.queue-tab', ['queue' => $queue, 'medicines' => $medicines])
            @elseif($tab === 'requests')
                @include('staff.partials.requests-tab', ['pendingRequests' => $pendingRequests])
            @elseif($tab === 'search')
                @include('staff.partials.search-tab', ['searchResults' => $searchResults])
            @endif
        </div>
    </div>
</div>

<style>
.stat-card {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
}
.stat-icon {
    width: 3rem;
    height: 3rem;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}
.stat-total .stat-icon { background: #dbeafe; color: #2563eb; }
.stat-waiting .stat-icon { background: #fef3c7; color: #f59e0b; }
.stat-consulting .stat-icon { background: #ddd6fe; color: #7c3aed; }
.stat-completed .stat-icon { background: #d1fae5; color: #059669; }
.stat-pending .stat-icon { background: #fee2e2; color: #dc2626; }
.stat-content h3 {
    font-size: 2rem;
    font-weight: bold;
    margin: 0;
    color: #111827;
}
.stat-content p {
    margin: 0;
    color: #6b7280;
    font-size: 0.875rem;
}
.stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
}
.tab-link:hover {
    color: #2563eb !important;
    background: #f3f4f6;
}
</style>
@endsection
