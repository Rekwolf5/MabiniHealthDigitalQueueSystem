@extends('layouts.app')

@section('title', 'Patients - Mabini Health Center')
@section('page-title', 'Patients')

@section('content')
<div class="dashboard-grid">
    <!-- Page Header with Enhanced Design -->
    <div class="dashboard-section" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); color: white; border: none;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">
                    <i class="fas fa-users"></i> Patient Management
                </h1>
                <p style="opacity: 0.9;">Manage patient records and information</p>
            </div>
            <div>
                <a href="{{ route('patients.create') }}" class="btn btn-primary" style="background: white; color: #059669; font-weight: 600;">
                    <i class="fas fa-plus"></i>
                    Add New Patient
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Dashboard -->
    <div class="stats-row">
        <div class="stat-card" style="border-left: 4px solid #3b82f6;">
            <div class="stat-icon" style="background: #3b82f6;">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['total_patients'] }}</h3>
                <p>Total Patients</p>
            </div>
        </div>
        
        <div class="stat-card" style="border-left: 4px solid #10b981;">
            <div class="stat-icon" style="background: #10b981;">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['registered_today'] }}</h3>
                <p>Registered Today</p>
            </div>
        </div>
        
        <div class="stat-card" style="border-left: 4px solid #6366f1;">
            <div class="stat-icon" style="background: #6366f1;">
                <i class="fas fa-mars"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['male_count'] }}</h3>
                <p>Male Patients</p>
            </div>
        </div>
        
        <div class="stat-card" style="border-left: 4px solid #ec4899;">
            <div class="stat-icon" style="background: #ec4899;">
                <i class="fas fa-venus"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['female_count'] }}</h3>
                <p>Female Patients</p>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="dashboard-section">
        <div style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #1f2937; margin: 0;">
                <i class="fas fa-search" style="color: #059669;"></i> Search & Filter Patients
            </h2>
            <button type="button" 
                    onclick="toggleFilters()" 
                    id="filterToggleBtn"
                    class="btn btn-secondary"
                    style="display: flex; align-items: center; gap: 0.5rem; background: #6b7280; color: white; border: none; padding: 0.625rem 1.25rem; font-weight: 500;">
                <i class="fas fa-filter" id="filterIcon" style="opacity: 1;"></i>
                <span id="filterToggleText">Show Filters</span>
                <i class="fas fa-chevron-down" id="filterChevron" style="opacity: 1;"></i>
            </button>
        </div>
        
        <form method="GET" 
              action="{{ route('patients.index') }}" 
              id="filterForm"
              style="background: #f9fafb; padding: 1.5rem; border-radius: 8px; border: 1px solid #e5e7eb; display: none;">
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1rem;">
                <!-- Search Input -->
                <div class="form-group" style="grid-column: span 2;">
                    <label><i class="fas fa-search" style="color: #059669;"></i> Search Patient</label>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search by name, patient ID, or contact number..." 
                           style="width: 100%;">
                </div>
                
                <!-- Gender Filter -->
                <div class="form-group">
                    <label style="color: #1f2937; font-weight: 600;"><i class="fas fa-venus-mars" style="color: #059669;"></i> Gender</label>
                    <select name="gender" style="width: 100%;">
                        <option value="">All Genders</option>
                        <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                
                <!-- Age Range -->
                <div class="form-group">
                    <label style="color: #1f2937; font-weight: 600;"><i class="fas fa-birthday-cake" style="color: #059669;"></i> Age Range</label>
                    <div style="display: grid; grid-template-columns: 1fr auto 1fr; gap: 0.5rem; align-items: center;">
                        <input type="number" 
                               name="age_from" 
                               value="{{ request('age_from') }}" 
                               placeholder="From" 
                               min="0" 
                               max="150">
                        <span style="color: #6b7280;">-</span>
                        <input type="number" 
                               name="age_to" 
                               value="{{ request('age_to') }}" 
                               placeholder="To" 
                               min="0" 
                               max="150">
                    </div>
                </div>
                
                <!-- Date From -->
                <div class="form-group">
                    <label style="color: #1f2937; font-weight: 600;"><i class="fas fa-calendar-alt" style="color: #059669;"></i> Registered From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" style="width: 100%;">
                </div>
                
                <!-- Date To -->
                <div class="form-group">
                    <label style="color: #1f2937; font-weight: 600;"><i class="fas fa-calendar-check" style="color: #059669;"></i> Registered To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" style="width: 100%;">
                </div>
            </div>
            
            <!-- Filter Buttons -->
            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                <a href="{{ route('patients.index') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Apply Filters
                </button>
            </div>
        </form>
        
        <!-- Active Filters Display -->
        @if(request()->hasAny(['search', 'gender', 'age_from', 'age_to', 'date_from', 'date_to']))
            <div id="activeFilters" style="margin-top: 1rem; padding: 0.75rem; background: #eff6ff; border-radius: 4px; border-left: 4px solid #3b82f6; display: none;">
                <strong style="color: #1e40af;">
                    <i class="fas fa-filter"></i> Active Filters:
                </strong>
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.5rem;">
                    @if(request('search'))
                        <span class="btn-sm" style="background: #3b82f6; color: white;">
                            Search: "{{ request('search') }}"
                        </span>
                    @endif
                    @if(request('gender'))
                        <span class="btn-sm" style="background: #3b82f6; color: white;">
                            Gender: {{ ucfirst(request('gender')) }}
                        </span>
                    @endif
                    @if(request('age_from') || request('age_to'))
                        <span class="btn-sm" style="background: #3b82f6; color: white;">
                            Age: {{ request('age_from', '0') }} - {{ request('age_to', '150+') }}
                        </span>
                    @endif
                    @if(request('date_from'))
                        <span class="btn-sm" style="background: #3b82f6; color: white;">
                            From: {{ \Carbon\Carbon::parse(request('date_from'))->format('M d, Y') }}
                        </span>
                    @endif
                    @if(request('date_to'))
                        <span class="btn-sm" style="background: #3b82f6; color: white;">
                            To: {{ \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') }}
                        </span>
                    @endif
                    <span style="color: #6b7280; font-size: 0.875rem; align-self: center;">
                        ({{ $patients->total() }} {{ Str::plural('result', $patients->total()) }} found)
                    </span>
                </div>
            </div>
        @endif
    </div>

    <!-- Patients Table -->
    <div class="data-table-container" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
        <!-- Mobile scroll hint -->
        <div style="display: none; background: #fef3c7; padding: 0.5rem 1rem; border-radius: 6px; margin-bottom: 0.5rem; font-size: 0.875rem; color: #92400e; text-align: center;" id="scrollHint">
            <i class="fas fa-hand-point-right"></i> Scroll right to see action buttons
        </div>
        <table class="data-table" style="min-width: 100%; width: max-content;">
            <thead>
                <tr style="background: #059669;">
                    <th style="color: black !important;"><i class="fas fa-id-card"></i> Patient ID</th>
                    <th style="color: black !important;"><i class="fas fa-user"></i> Name</th>
                    <th style="color: black !important;"><i class="fas fa-birthday-cake"></i> Age</th>
                    <th style="color: black !important;"><i class="fas fa-venus-mars"></i> Gender</th>
                    <th style="color: black !important;"><i class="fas fa-phone"></i> Contact</th>
                    <th style="color: black !important;"><i class="fas fa-map-marker-alt"></i> Address</th>
                    <th style="color: black !important;"><i class="fas fa-calendar"></i> Registered</th>
                    <th style="color: white !important; text-align: center; min-width: 140px; position: sticky; right: 0; background: #059669; z-index: 10;"><i class="fas fa-cog"></i> Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $patient)
                <tr>
                    <td>
                        <span style="background: #dbeafe; color: #1e40af; padding: 0.5rem; border-radius: 4px; font-weight: 700; font-family: monospace;">
                            {{ $patient->patient_id }}
                        </span>
                    </td>
                    <td>
                        <div class="patient-name">
                            <i class="fas fa-user-circle"></i>
                            <div>
                                <strong>{{ $patient->full_name }}</strong>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span style="font-weight: 600; color: #374151;">{{ $patient->age }}</span> 
                        <small style="color: #9ca3af;">years</small>
                    </td>
                    <td>
                        <span class="btn-sm" style="
                            background: {{ $patient->gender === 'male' ? '#dbeafe' : '#fce7f3' }};
                            color: {{ $patient->gender === 'male' ? '#1e40af' : '#9f1239' }};
                            border: 1px solid {{ $patient->gender === 'male' ? '#93c5fd' : '#fbcfe8' }};">
                            <i class="fas fa-{{ $patient->gender === 'male' ? 'mars' : 'venus' }}"></i>
                            {{ ucfirst($patient->gender) }}
                        </span>
                    </td>
                    <td>
                        <i class="fas fa-phone" style="color: #9ca3af;"></i>
                        {{ $patient->contact }}
                    </td>
                    <td style="max-width: 200px;">
                        <i class="fas fa-map-marker-alt" style="color: #9ca3af;"></i>
                        <small>{{ Str::limit($patient->address, 50) }}</small>
                    </td>
                    <td>
                        <i class="fas fa-calendar" style="color: #9ca3af;"></i>
                        {{ $patient->created_at->format('M d, Y') }}<br>
                        <small style="color: #6b7280;">{{ $patient->created_at->diffForHumans() }}</small>
                    </td>
                    <td style="position: sticky; right: 0; background: white; box-shadow: -2px 0 4px rgba(0,0,0,0.05); z-index: 5;">
                        <div class="action-buttons" style="justify-content: center; display: flex; flex-wrap: nowrap; gap: 0.375rem; padding: 0.25rem;">
                            <a href="{{ route('patients.show', $patient->id) }}" 
                               class="btn btn-sm btn-info" 
                               title="View Details"
                               style="min-width: 40px; padding: 0.5rem 0.625rem;">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('patients.edit', $patient->id) }}" 
                               class="btn btn-sm btn-warning" 
                               title="Edit Patient"
                               style="min-width: 40px; padding: 0.5rem 0.625rem;">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" 
                                  action="{{ route('patients.destroy', $patient->id) }}" 
                                  style="display: inline; margin: 0;"
                                  onsubmit="return confirm('Are you sure you want to delete this patient? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-sm btn-danger" 
                                        title="Delete Patient"
                                        style="min-width: 40px; padding: 0.5rem 0.625rem; cursor: pointer;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 3rem;">
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                            <div style="background: #f3f4f6; border-radius: 50%; padding: 2rem; width: 120px; height: 120px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-users" style="font-size: 3rem; color: #d1d5db;"></i>
                            </div>
                            <div>
                                @if(request()->hasAny(['search', 'gender', 'age_from', 'age_to', 'date_from', 'date_to']))
                                    <h3 style="font-size: 1.25rem; font-weight: 700; color: #6b7280; margin-bottom: 0.5rem;">No Patients Found</h3>
                                    <p style="color: #9ca3af;">Try adjusting your search filters</p>
                                    <a href="{{ route('patients.index') }}" class="btn btn-secondary" style="margin-top: 1rem;">
                                        <i class="fas fa-redo"></i> Clear Filters
                                    </a>
                                @else
                                    <h3 style="font-size: 1.25rem; font-weight: 700; color: #6b7280; margin-bottom: 0.5rem;">No Patients Yet</h3>
                                    <p style="color: #9ca3af;">Get started by adding your first patient</p>
                                    <a href="{{ route('patients.create') }}" class="btn btn-primary" style="margin-top: 1rem;">
                                        <i class="fas fa-plus"></i> Add First Patient
                                    </a>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($patients->hasPages())
    <div style="margin-top: 1.5rem; display: flex; justify-content: center;">
        {{ $patients->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<script>
function toggleFilters() {
    const form = document.getElementById('filterForm');
    const activeFilters = document.getElementById('activeFilters');
    const icon = document.getElementById('filterIcon');
    const text = document.getElementById('filterToggleText');
    const chevron = document.getElementById('filterChevron');
    
    if (form.style.display === 'none') {
        form.style.display = 'block';
        if (activeFilters) activeFilters.style.display = 'block';
        text.textContent = 'Hide Filters';
        chevron.classList.remove('fa-chevron-down');
        chevron.classList.add('fa-chevron-up');
        localStorage.setItem('filtersOpen', 'true');
    } else {
        form.style.display = 'none';
        if (activeFilters) activeFilters.style.display = 'none';
        text.textContent = 'Show Filters';
        chevron.classList.remove('fa-chevron-up');
        chevron.classList.add('fa-chevron-down');
        localStorage.setItem('filtersOpen', 'false');
    }
}

// Show filters if there are active filters or if user previously had them open
document.addEventListener('DOMContentLoaded', function() {
    const hasActiveFilters = {{ request()->hasAny(['search', 'gender', 'age_from', 'age_to', 'date_from', 'date_to']) ? 'true' : 'false' }};
    const filtersWereOpen = localStorage.getItem('filtersOpen') === 'true';
    
    if (hasActiveFilters || filtersWereOpen) {
        toggleFilters();
    }
    
    // Show scroll hint on mobile devices
    if (window.innerWidth <= 768) {
        const scrollHint = document.getElementById('scrollHint');
        const tableContainer = document.querySelector('.data-table-container');
        
        if (scrollHint && tableContainer) {
            scrollHint.style.display = 'block';
            
            // Hide hint after user scrolls
            let scrolled = false;
            tableContainer.addEventListener('scroll', function() {
                if (!scrolled && this.scrollLeft > 50) {
                    scrolled = true;
                    scrollHint.style.transition = 'opacity 0.5s';
                    scrollHint.style.opacity = '0';
                    setTimeout(() => {
                        scrollHint.style.display = 'none';
                    }, 500);
                }
            });
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                if (!scrolled) {
                    scrollHint.style.transition = 'opacity 0.5s';
                    scrollHint.style.opacity = '0';
                    setTimeout(() => {
                        scrollHint.style.display = 'none';
                    }, 500);
                }
            }, 5000);
        }
    }
});
</script>
@endsection
