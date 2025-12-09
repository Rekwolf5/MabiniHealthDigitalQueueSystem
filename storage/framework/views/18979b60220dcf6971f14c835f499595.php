

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Activity Logs</h1>
                    <p class="text-muted mb-0">System audit trail and user activity history</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Print Logs
                    </button>
                    <button class="btn btn-primary" onclick="refreshLogs()">
                        <i class="fas fa-sync-alt me-2"></i>Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2"></i>Filters
            </h5>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('admin.activity.logs')); ?>" method="GET" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="action" class="form-label">Action Type</label>
                        <select class="form-select" id="action" name="action">
                            <option value="">All Actions</option>
                            <option value="create" <?php echo e(request('action') == 'create' ? 'selected' : ''); ?>>Create</option>
                            <option value="update" <?php echo e(request('action') == 'update' ? 'selected' : ''); ?>>Update</option>
                            <option value="delete" <?php echo e(request('action') == 'delete' ? 'selected' : ''); ?>>Delete</option>
                            <option value="login" <?php echo e(request('action') == 'login' ? 'selected' : ''); ?>>Login</option>
                            <option value="logout" <?php echo e(request('action') == 'logout' ? 'selected' : ''); ?>>Logout</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="user_type" class="form-label">User Type</label>
                        <select class="form-select" id="user_type" name="user_type">
                            <option value="">All Users</option>
                            <option value="admin" <?php echo e(request('user_type') == 'admin' ? 'selected' : ''); ?>>Admin</option>
                            <option value="manager" <?php echo e(request('user_type') == 'manager' ? 'selected' : ''); ?>>Manager</option>
                            <option value="staff" <?php echo e(request('user_type') == 'staff' ? 'selected' : ''); ?>>Service Staff</option>
                            <option value="front_desk" <?php echo e(request('user_type') == 'front_desk' ? 'selected' : ''); ?>>Front Desk</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" value="<?php echo e(request('date_from')); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" value="<?php echo e(request('date_to')); ?>">
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label for="search" class="form-label">Search Description</label>
                        <input type="text" class="form-control" id="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search in activity description...">
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-2"></i>Apply Filters
                        </button>
                        <a href="<?php echo e(route('admin.activity.logs')); ?>" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-white-50">Total Activities</h6>
                            <h2 class="mb-0 mt-2"><?php echo e(number_format($logs->total())); ?></h2>
                        </div>
                        <div>
                            <i class="fas fa-list-ul fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-white-50">Today's Activities</h6>
                            <h2 class="mb-0 mt-2"><?php echo e(number_format($logs->where('created_at', '>=', today())->count())); ?></h2>
                        </div>
                        <div>
                            <i class="fas fa-calendar-day fa-3x opacity-50"></i>
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
                            <h6 class="mb-0 text-white-50">This Week</h6>
                            <h2 class="mb-0 mt-2"><?php echo e(number_format($logs->where('created_at', '>=', now()->startOfWeek())->count())); ?></h2>
                        </div>
                        <div>
                            <i class="fas fa-calendar-week fa-3x opacity-50"></i>
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
                            <h6 class="mb-0 text-white-50">This Month</h6>
                            <h2 class="mb-0 mt-2"><?php echo e(number_format($logs->where('created_at', '>=', now()->startOfMonth())->count())); ?></h2>
                        </div>
                        <div>
                            <i class="fas fa-calendar-alt fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Logs Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Activity History</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 140px;">Date & Time</th>
                            <th style="width: 100px;">Action</th>
                            <th>Description</th>
                            <th style="width: 150px;">User</th>
                            <th style="width: 100px;">Type</th>
                            <th style="width: 120px;">IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="text-muted"><?php echo e($log->id); ?></td>
                            <td>
                                <small>
                                    <?php echo e($log->created_at->format('M d, Y')); ?><br>
                                    <span class="text-muted"><?php echo e($log->created_at->format('h:i A')); ?></span>
                                </small>
                            </td>
                            <td>
                                <?php
                                    $actionColors = [
                                        'create' => 'success',
                                        'update' => 'info',
                                        'delete' => 'danger',
                                        'login' => 'primary',
                                        'logout' => 'secondary',
                                        'restock' => 'warning',
                                        'dispense' => 'purple'
                                    ];
                                    $color = $actionColors[$log->action] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?php echo e($color); ?>">
                                    <?php echo e(ucfirst($log->action)); ?>

                                </span>
                            </td>
                            <td>
                                <div class="activity-description">
                                    <?php echo e($log->description); ?>

                                    <?php if($log->model_type && $log->model_id): ?>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-link me-1"></i>
                                            <?php echo e(class_basename($log->model_type)); ?> #<?php echo e($log->model_id); ?>

                                        </small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <?php if(in_array($log->user_type, ['staff', 'admin', 'manager', 'front_desk'])): ?>
                                    <?php
                                        $user = \App\Models\User::find($log->user_id);
                                    ?>
                                    <?php if($user): ?>
                                        <div>
                                            <strong><?php echo e($user->name); ?></strong><br>
                                            <small class="text-muted"><?php echo e($user->email); ?></small>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">User #<?php echo e($log->user_id); ?></span>
                                    <?php endif; ?>
                                <?php elseif($log->user_type == 'patient'): ?>
                                    <?php
                                        $patient = \App\Models\PatientAccount::find($log->user_id);
                                    ?>
                                    <?php if($patient): ?>
                                        <div>
                                            <strong><?php echo e($patient->full_name); ?></strong><br>
                                            <small class="text-muted"><?php echo e($patient->email); ?></small>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">Patient #<?php echo e($log->user_id); ?></span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">System</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                    $typeColors = [
                                        'admin' => 'danger',
                                        'manager' => 'info',
                                        'staff' => 'primary',
                                        'front_desk' => 'success',
                                        'patient' => 'info'
                                    ];
                                    $typeColor = $typeColors[$log->user_type] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?php echo e($typeColor); ?>">
                                    <?php echo e(ucfirst(str_replace('_', ' ', $log->user_type ?? 'System'))); ?>

                                </span>
                            </td>
                            <td>
                                <small class="text-muted font-monospace">
                                    <?php echo e($log->ip_address ?? 'N/A'); ?>

                                </small>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">No activity logs found</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if($logs->hasPages()): ?>
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">
                        Showing <?php echo e($logs->firstItem()); ?> to <?php echo e($logs->lastItem()); ?> of <?php echo e(number_format($logs->total())); ?> entries
                    </small>
                </div>
                <div>
                    <?php echo e($logs->links()); ?>

                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

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

.bg-purple {
    background-color: #6f42c1 !important;
}

.activity-description {
    line-height: 1.4;
}

.form-control, .form-select {
    border-radius: 6px;
    border: 1px solid #dee2e6;
    padding: 0.5rem 0.75rem;
    font-size: 0.9rem;
}

.btn {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-weight: 500;
}

@media print {
    .card-header,
    .btn,
    .pagination,
    .card-footer {
        display: none !important;
    }
    
    .table {
        font-size: 10pt;
    }
}
</style>

<script>
function refreshLogs() {
    location.reload();
}

// Set max date for date inputs to today
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('date_from').setAttribute('max', today);
    document.getElementById('date_to').setAttribute('max', today);
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ramir\Desktop\Health-Center-Queue-System-main\resources\views/admin/activity-logs.blade.php ENDPATH**/ ?>