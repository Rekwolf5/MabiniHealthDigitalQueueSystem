<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\DailySummaryController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;

// Default redirect - redirect to staff login for walk-in system
Route::get('/', function () {
    if (Auth::guard('web')->check()) {
        return redirect()->route('staff.dashboard');
    }
    return redirect()->route('login');
});

// Staff/Admin Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
// SECURITY: Staff/Admin registration disabled - use admin panel to create staff accounts
// Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
// Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes for Staff/Admin
Route::get('/forgot-password', [\App\Http\Controllers\PasswordResetController::class, 'showRequestForm'])->name('password.request');
Route::post('/forgot-password', [\App\Http\Controllers\PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [\App\Http\Controllers\PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [\App\Http\Controllers\PasswordResetController::class, 'reset'])->name('password.update');

// Protected Staff/Admin Routes
Route::middleware('auth')->group(function () {
    // Main dashboard route for staff/admin
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('staff.dashboard');

    // Notification Routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/count', [\App\Http\Controllers\NotificationController::class, 'getCount'])->name('count');
        Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::delete('/{id}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('destroy');
    });

    // Service Status API (for front desk dashboard)
    Route::get('/api/service-status', function() {
        $services = \App\Models\Service::with(['frontDeskQueues' => function($query) {
            $query->whereIn('status', ['waiting', 'called'])
                  ->whereDate('created_at', today());
        }])
        ->get()
        ->map(function($service) {
            $service->current_queue_count = $service->frontDeskQueues->count();
            return $service;
        });
        
        return response()->json($services);
    })->name('api.service-status');

    // Support Routes (Staff/Admin)
    Route::get('/staff/support', [\App\Http\Controllers\SupportController::class, 'show'])->name('staff.support');
    Route::post('/staff/support', [\App\Http\Controllers\SupportController::class, 'submit'])->name('staff.support.submit');

    // Front Desk Queue Management (Replaces Patient Management)
    Route::prefix('front-desk')->name('front-desk.')->group(function () {
        Route::get('/', [App\Http\Controllers\FrontDeskController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\FrontDeskController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\FrontDeskController::class, 'store'])->name('store');
        Route::post('/{id}/call', [App\Http\Controllers\FrontDeskController::class, 'callNext'])->name('call');
        Route::post('/{id}/complete', [App\Http\Controllers\FrontDeskController::class, 'complete'])->name('complete');
        Route::post('/{id}/cancel', [App\Http\Controllers\FrontDeskController::class, 'cancel'])->name('cancel');
        Route::put('/{id}', [App\Http\Controllers\FrontDeskController::class, 'update'])->name('update');
        Route::get('/service-capacity/{serviceId}', [App\Http\Controllers\FrontDeskController::class, 'getServiceCapacity'])->name('service.capacity');
    });

    // Phase 2: Service-Specific Queue Management
    Route::prefix('services')->name('services.')->middleware(['auth'])->group(function () {
        Route::get('/{serviceId}/dashboard', [App\Http\Controllers\ServiceQueueController::class, 'dashboard'])->name('dashboard');
        Route::post('/accept/{queueId}', [App\Http\Controllers\ServiceQueueController::class, 'acceptPatient'])->name('accept');
        Route::post('/start/{queueId}', [App\Http\Controllers\ServiceQueueController::class, 'startService'])->name('start');
        Route::post('/complete/{queueId}', [App\Http\Controllers\ServiceQueueController::class, 'completeService'])->name('complete');
        Route::post('/skip/{queueId}', [App\Http\Controllers\ServiceQueueController::class, 'skipPatient'])->name('skip');
        Route::post('/no-show/{queueId}', [App\Http\Controllers\ServiceQueueController::class, 'markNoShow'])->name('no-show');
        Route::post('/vitals/call/{queueId}', [App\Http\Controllers\ServiceQueueController::class, 'callForVitals'])->name('vitals.call');
        Route::post('/vitals/complete/{queueId}', [App\Http\Controllers\ServiceQueueController::class, 'completeVitals'])->name('vitals.complete');
        Route::post('/vitals/store', [App\Http\Controllers\ServiceQueueController::class, 'storeVitals'])->name('vitals.store');
        Route::post('/transfer/{queueId}', [App\Http\Controllers\ServiceQueueController::class, 'transferPatient'])->name('transfer');
        Route::post('/{serviceId}/call-next', [App\Http\Controllers\ServiceQueueController::class, 'callNext'])->name('call-next');
        Route::get('/available/{currentServiceId}', [App\Http\Controllers\ServiceQueueController::class, 'getAvailableServices'])->name('available');
        Route::get('/status/{serviceId}', [App\Http\Controllers\ServiceQueueController::class, 'getServiceStatus'])->name('status');
    });

    // Service Availability and Capacity Management
    Route::prefix('service-management')->name('service-management.')->middleware(['auth'])->group(function () {
        Route::get('/', [App\Http\Controllers\ServiceManagementController::class, 'index'])->name('index');
        Route::post('/{serviceId}/availability', [App\Http\Controllers\ServiceManagementController::class, 'updateAvailability'])->name('update-availability');
        Route::post('/{serviceId}/toggle', [App\Http\Controllers\ServiceManagementController::class, 'quickToggle'])->name('quick-toggle');
        Route::get('/{serviceId}/stats', [App\Http\Controllers\ServiceManagementController::class, 'getServiceStats'])->name('stats');
        Route::post('/reset-counters', [App\Http\Controllers\ServiceManagementController::class, 'resetDailyCounters'])->name('reset-counters');
    });

    // Centralized Announcement System
    Route::get('/queue/announcements', [QueueController::class, 'announcements'])->name('queue.announcements');
    Route::get('/api/queue/pending-announcements', [QueueController::class, 'getPendingAnnouncements'])->name('queue.pending-announcements');
    
    // Redirect old patient routes to front desk
    Route::redirect('/patients', '/front-desk');
    Route::redirect('/patients/create', '/front-desk');

    // Legacy Patient Management (Kept for reference, commented out for now)
    /*
    Route::prefix('patients')->name('patients.')->group(function () {
        Route::get('/', [PatientController::class, 'index'])->name('index');
        Route::get('/create', [PatientController::class, 'create'])->name('create');
        Route::post('/', [PatientController::class, 'store'])->name('store');
        Route::get('/{id}', [PatientController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [PatientController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PatientController::class, 'update'])->name('update');
        Route::delete('/{id}', [PatientController::class, 'destroy'])->name('destroy');
    });
    */

    // Queue Management (All authenticated users)
    Route::prefix('queue')->name('queue.')->group(function () {
        Route::get('/', [QueueController::class, 'index'])->name('index');
        Route::get('/add', [QueueController::class, 'add'])->name('add');
        Route::post('/', [QueueController::class, 'store'])->name('store');
        Route::patch('/{id}/status', [QueueController::class, 'updateStatus'])->name('updateStatus');
        Route::patch('/queue/{id}/recall', [QueueController::class, 'recall'])->name('queue.recall');
        Route::post('/{id}/assign-doctor', [QueueController::class, 'assignDoctor'])->name('assign-doctor');
    });
    
    // Public Queue Display
    Route::get('/queue-display', [QueueController::class, 'display'])->name('queue.display');

    // Medicine Management (All authenticated users)
    Route::prefix('medicines')->name('medicines.')->group(function () {
        Route::get('/', [MedicineController::class, 'index'])->name('index');
        Route::get('/create', [MedicineController::class, 'create'])->name('create');
        Route::post('/', [MedicineController::class, 'store'])->name('store');
        Route::get('/{id}', [MedicineController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [MedicineController::class, 'edit'])->name('edit');
        Route::put('/{id}', [MedicineController::class, 'update'])->name('update');
        Route::delete('/{id}', [MedicineController::class, 'destroy'])->name('destroy');
    });

    // Reports (Front Desk + Admin only)  
    Route::prefix('reports')->name('reports.')->middleware(['auth'])->group(function () {
        Route::get('/', [ReportsController::class, 'index'])->name('index');
        Route::get('/patients', [ReportsController::class, 'patients'])->name('patients');
        Route::get('/queue', [ReportsController::class, 'queue'])->name('queue');
        Route::get('/medicines', [ReportsController::class, 'medicines'])->name('medicines');
        Route::post('/generate', [ReportsController::class, 'generate'])->name('generate');
    });

    // Front Desk Routes (Front Desk + Admin only)
    Route::prefix('staff')->name('staff.')->middleware(['auth', 'front_desk'])->group(function () {
        // Queue management - accessible by front desk and admin
        Route::get('/queue-management', [StaffController::class, 'queueManagement'])->name('queue.management');
        Route::post('/queue/{id}/call-next', [StaffController::class, 'callNext'])->name('queue.call-next');
        Route::post('/queue/{id}/mark-served', [StaffController::class, 'markServed'])->name('queue.mark-served');
        Route::post('/queue/{id}/mark-no-show', [StaffController::class, 'markNoShow'])->name('queue.mark-no-show');
    });

    
     


    // Admin Routes (Admin only)
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // Support Management
        Route::get('/support', [\App\Http\Controllers\SupportController::class, 'adminIndex'])->name('support.index');
        Route::post('/support/{id}/reply', [\App\Http\Controllers\SupportController::class, 'adminReply'])->name('support.reply');
        
        // User Management
        Route::get('/users', [AdminController::class, 'userManagement'])->name('users');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');
        
        // Staff Management (Admin-only staff creation)
        Route::get('/staff/create', [AdminController::class, 'createStaff'])->name('staff.create');
        Route::post('/staff', [AdminController::class, 'storeStaff'])->name('staff.store');
        
        // Queue Monitor
        Route::get('/queue-monitor', [AdminController::class, 'queueMonitor'])->name('queue.monitor');
        
       

        // Medicine Management
        Route::get('/medicine-management', [AdminController::class, 'medicineManagement'])->name('medicine.management');
        Route::post('/medicine/{id}/approve-change', [AdminController::class, 'approveMedicineChange'])->name('medicine.approve-change');
        
        // System Management
        Route::get('/activity-logs', [AdminController::class, 'activityLogs'])->name('activity.logs');
        Route::get('/system-settings', [AdminController::class, 'systemSettings'])->name('system.settings');
        Route::post('/system-settings', [AdminController::class, 'updateSettings'])->name('system.settings.update');
        Route::put('/system-settings', [AdminController::class, 'updateSystemSettings'])->name('system-settings.update');
        Route::post('/process-cutoff', [AdminController::class, 'processCutoff'])->name('process-cutoff');
        
        // Backup & Restore
        Route::get('/backup', [AdminController::class, 'backupData'])->name('backup');
        Route::post('/restore', [AdminController::class, 'restoreData'])->name('restore');
    });
});