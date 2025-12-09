

<?php $__env->startSection('title', 'User Management - Mabini Health Center'); ?>
<?php $__env->startSection('page-title', 'User Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1 fw-bold text-dark"><i class="fas fa-users-cog me-2"></i>User Management</h2>
            <p class="text-muted mb-0 small">Manage all system users and staff accounts</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('admin.system.settings')); ?>" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-cog me-1"></i> System Settings
            </a>
            <a href="<?php echo e(route('admin.backup')); ?>" class="btn btn-sm btn-outline-warning">
                <i class="fas fa-database me-1"></i> Backup & Restore
            </a>
            <a href="<?php echo e(route('admin.staff.create')); ?>" class="btn btn-sm btn-success" style="background-color: #10b981; border-color: #10b981;">
                <i class="fas fa-user-plus me-1"></i> Create New Staff
            </a>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
            <h5 class="mb-0 fw-semibold text-dark"><i class="fas fa-list me-2"></i>All Users</h5>
            <div class="d-flex align-items-center gap-2">
                <input 
                    type="text" 
                    id="searchInput" 
                    class="form-control form-control-sm" 
                    placeholder="Search by name, email..."
                    style="width: 280px;"
                >
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #f8f9fa;">
                        <tr>
                            <th class="text-center py-3" style="width: 70px; font-weight: 600; font-size: 0.875rem; color: #4b5563;">ID</th>
                            <th class="py-3" style="font-weight: 600; font-size: 0.875rem; color: #4b5563;">Name</th>
                            <th class="py-3" style="font-weight: 600; font-size: 0.875rem; color: #4b5563;">Email</th>
                            <th class="py-3" style="font-weight: 600; font-size: 0.875rem; color: #4b5563;">Phone</th>
                            <th class="py-3" style="font-weight: 600; font-size: 0.875rem; color: #4b5563;">Role</th>
                            <th class="text-center py-3" style="font-weight: 600; font-size: 0.875rem; color: #4b5563;">Status</th>
                            <th class="py-3" style="font-weight: 600; font-size: 0.875rem; color: #4b5563;">Created At</th>
                            <th class="text-center py-3" style="width: 140px; font-weight: 600; font-size: 0.875rem; color: #4b5563;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody">
                        <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td class="text-center py-3" style="font-size: 0.875rem; color: #6b7280;"><?php echo e($user->id); ?></td>
                                <td class="py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white" style="width: 36px; height: 36px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); font-size: 0.875rem;">
                                            <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                                        </div>
                                        <span class="fw-semibold" style="font-size: 0.9rem; color: #111827;"><?php echo e($user->name); ?></span>
                                    </div>
                                </td>
                                <td class="py-3" style="font-size: 0.875rem; color: #6b7280;"><?php echo e($user->email); ?></td>
                                <td class="py-3" style="font-size: 0.875rem; color: #6b7280;"><?php echo e($user->phone ?? 'N/A'); ?></td>
                                <td class="py-3">
                                    <?php
                                        $roleBadgeClass = match($user->role) {
                                            'admin' => 'primary',
                                            'manager' => 'info',
                                            'doctor' => 'info',
                                            'front_desk' => 'success',
                                            'staff' => 'success',
                                            default => 'secondary'
                                        };
                                        $roleDisplay = match($user->role) {
                                            'front_desk' => 'Front Desk',
                                            'staff' => $user->service ? $user->service->name : 'Unassigned',
                                            default => ucfirst($user->role)
                                        };
                                    ?>
                                    <span class="badge bg-<?php echo e($roleBadgeClass); ?> px-2 py-1" style="font-size: 0.75rem; font-weight: 500;">
                                        <?php echo e($roleDisplay); ?>

                                    </span>
                                </td>
                                <td class="text-center py-3">
                                    <span class="badge bg-<?php echo e($user->status === 'active' ? 'success' : 'secondary'); ?> px-2 py-1" style="font-size: 0.75rem; font-weight: 500;">
                                        <?php echo e(ucfirst($user->status ?? 'active')); ?>

                                    </span>
                                </td>
                                <td class="py-3">
                                    <small class="text-muted" style="font-size: 0.8125rem;"><?php echo e($user->created_at->format('M d, Y')); ?></small>
                                </td>
                                <td class="text-center py-3">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button 
                                            class="btn btn-outline-info btn-sm" 
                                            onclick="viewUser(<?php echo e($user->id); ?>)"
                                            title="View Details"
                                            style="font-size: 0.75rem; padding: 0.25rem 0.5rem;"
                                        >
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <?php if($user->id !== auth()->id()): ?>
                                            <a 
                                                href="<?php echo e(route('admin.users.edit', $user->id)); ?>"
                                                class="btn btn-outline-warning btn-sm"
                                                title="Edit User"
                                                style="font-size: 0.75rem; padding: 0.25rem 0.5rem;"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <form 
                                                method="POST" 
                                                action="<?php echo e(route('admin.users.delete', $user->id)); ?>" 
                                                style="display: inline;"
                                                onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')"
                                            >
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button 
                                                    type="submit" 
                                                    class="btn btn-outline-danger btn-sm"
                                                    title="Delete User"
                                                    style="font-size: 0.75rem; padding: 0.25rem 0.5rem;"
                                                >
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="badge bg-light text-secondary ms-1" style="font-size: 0.7rem; padding: 0.35rem 0.5rem;">Current User</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-users fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-0">No users found</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
        <?php if($users->hasPages()): ?>
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-center">
                <?php echo e($users->links()); ?>

            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- User Statistics -->
    <div class="row g-3 mt-1">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100" style="border-left: 4px solid #10b981 !important;">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(16, 185, 129, 0.1);">
                                <i class="fas fa-users text-success" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1 small">Total Users</p>
                            <h4 class="mb-0 fw-bold"><?php echo e($users->total()); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100" style="border-left: 4px solid #3b82f6 !important;">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(59, 130, 246, 0.1);">
                                <i class="fas fa-user-shield text-primary" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1 small">Administrators</p>
                            <h4 class="mb-0 fw-bold"><?php echo e(App\Models\User::where('role', 'admin')->count()); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100" style="border-left: 4px solid #8b5cf6 !important;">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(139, 92, 246, 0.1);">
                                <i class="fas fa-user-nurse" style="color: #8b5cf6; font-size: 1.5rem;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1 small">Staff Members</p>
                            <h4 class="mb-0 fw-bold"><?php echo e(App\Models\User::whereIn('role', ['staff', 'front_desk'])->count()); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100" style="border-left: 4px solid #10b981 !important;">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(16, 185, 129, 0.1);">
                                <i class="fas fa-check-circle text-success" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1 small">Active Users</p>
                            <h4 class="mb-0 fw-bold"><?php echo e(App\Models\User::where('status', 'active')->orWhereNull('status')->count()); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Search functionality
document.getElementById('searchInput')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#userTableBody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

function viewUser(userId) {
    window.location.href = `/admin/users/${userId}/edit`;
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ramir\Desktop\Health-Center-Queue-System-main\resources\views/admin/user-management.blade.php ENDPATH**/ ?>