

<?php $__env->startSection('title', 'Admin Dashboard - Mabini Health Center'); ?>
<?php $__env->startSection('page-title', 'System Overview Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<style>
.dashboard-overview {
    padding: 20px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border-left: 4px solid #059669;
    transition: transform 0.2s;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.stat-card.patients { border-left-color: #059669; }
.stat-card.waiting { border-left-color: #f59e0b; }
.stat-card.called { border-left-color: #3b82f6; }
.stat-card.in-progress { border-left-color: #8b5cf6; }
.stat-card.completed { border-left-color: #10b981; }
.stat-card.no-show { border-left-color: #ef4444; }
.stat-card.medicines { border-left-color: #ec4899; }
.stat-card.reports { border-left-color: #6366f1; }

.stat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
}

.stat-card.patients .stat-icon { background: #059669; }
.stat-card.waiting .stat-icon { background: #f59e0b; }
.stat-card.called .stat-icon { background: #3b82f6; }
.stat-card.in-progress .stat-icon { background: #8b5cf6; }
.stat-card.completed .stat-icon { background: #10b981; }
.stat-card.no-show .stat-icon { background: #ef4444; }
.stat-card.medicines .stat-icon { background: #ec4899; }
.stat-card.reports .stat-icon { background: #6366f1; }

.stat-value {
    font-size: 32px;
    font-weight: bold;
    color: #1f2937;
    margin: 10px 0 5px 0;
}

.stat-label {
    font-size: 14px;
    color: #6b7280;
    font-weight: 500;
}

.dashboard-sections {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.section-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.section-title {
    font-size: 18px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-title i {
    color: #059669;
}

.service-item {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr 1fr;
    gap: 15px;
    padding: 15px;
    border-bottom: 1px solid #e5e7eb;
    align-items: center;
}

.service-item:last-child {
    border-bottom: none;
}

.service-name {
    font-weight: 600;
    color: #1f2937;
}

.service-stat {
    text-align: center;
}

.service-stat-value {
    font-size: 20px;
    font-weight: bold;
    display: block;
}

.service-stat-label {
    font-size: 11px;
    color: #6b7280;
    text-transform: uppercase;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 12px;
    border-bottom: 1px solid #f3f4f6;
    transition: background 0.2s;
}

.activity-item:hover {
    background: #f9fafb;
}

.activity-time {
    font-size: 12px;
    color: #6b7280;
    min-width: 60px;
}

.activity-content {
    flex: 1;
}

.activity-queue {
    font-weight: 600;
    color: #059669;
}

.activity-patient {
    color: #1f2937;
    font-weight: 500;
}

.activity-service {
    color: #6b7280;
    font-size: 13px;
}

.status-badge {
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-waiting { background: #fef3c7; color: #92400e; }
.status-called { background: #dbeafe; color: #1e40af; }
.status-in_progress { background: #ede9fe; color: #5b21b6; }

.priority-badge {
    padding: 2px 8px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 600;
}

.priority-emergency { background: #fee2e2; color: #991b1b; }
.priority-senior { background: #f3e8ff; color: #7c3aed; }
.priority-pwd { background: #dbeafe; color: #1e40af; }
.priority-pregnant { background: #fce7f3; color: #be185d; }
.priority-normal { background: #f3f4f6; color: #374151; }

.medicine-item {
    display: flex;
    justify-content: space-between;
    padding: 10px;
    border-bottom: 1px solid #f3f4f6;
}

.medicine-name {
    font-weight: 600;
    color: #1f2937;
}

.medicine-stock {
    color: #ef4444;
    font-weight: 600;
}

.staff-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    border-bottom: 1px solid #f3f4f6;
}

.staff-name {
    font-weight: 600;
    color: #1f2937;
}

.staff-count {
    background: #059669;
    color: white;
    padding: 4px 12px;
    border-radius: 12px;
    font-weight: 600;
}

.priority-distribution {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.priority-item {
    flex: 1;
    min-width: 100px;
    padding: 10px;
    border-radius: 8px;
    text-align: center;
}

.priority-count {
    font-size: 24px;
    font-weight: bold;
    display: block;
}

.priority-label {
    font-size: 12px;
    color: #6b7280;
}

.refresh-notice {
    background: #f0fdf4;
    border: 1px solid #86efac;
    color: #166534;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.full-width {
    grid-column: 1 / -1;
}

@media (max-width: 968px) {
    .dashboard-sections {
        grid-template-columns: 1fr;
    }
    
    .service-item {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    
    .service-stat {
        text-align: left;
    }
}
</style>

<div class="dashboard-overview">
    <div class="refresh-notice">
        <i class="fas fa-sync-alt"></i>
        <span>Dashboard auto-refreshes every 30 seconds to show real-time data</span>
    </div>

    <!-- Main Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card patients">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stat-value"><?php echo e($stats['patients_today']); ?></div>
            <div class="stat-label">Patients Today</div>
        </div>

        <div class="stat-card waiting">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="stat-value"><?php echo e($stats['queue_waiting']); ?></div>
            <div class="stat-label">Waiting</div>
        </div>

        <div class="stat-card called">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-bell"></i>
                </div>
            </div>
            <div class="stat-value"><?php echo e($stats['queue_called']); ?></div>
            <div class="stat-label">Called</div>
        </div>

        <div class="stat-card in-progress">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-user-md"></i>
                </div>
            </div>
            <div class="stat-value"><?php echo e($stats['queue_in_progress']); ?></div>
            <div class="stat-label">In Progress</div>
        </div>

        <div class="stat-card completed">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="stat-value"><?php echo e($stats['queue_completed']); ?></div>
            <div class="stat-label">Completed</div>
        </div>

        <div class="stat-card no-show">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-user-times"></i>
                </div>
            </div>
            <div class="stat-value"><?php echo e($stats['queue_no_show']); ?></div>
            <div class="stat-label">No Show</div>
        </div>

        <div class="stat-card reports">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
            </div>
            <div class="stat-value"><?php echo e($stats['reports_generated']); ?></div>
            <div class="stat-label">Reports Today</div>
        </div>
    </div>

    <!-- Service Performance Section -->
    <div class="section-card" style="margin-bottom: 20px;">
        <div class="section-title">
            <i class="fas fa-hospital"></i>
            Service Performance Today
        </div>
        
        <div class="service-item" style="background: #f9fafb; font-weight: 600; font-size: 12px; color: #6b7280;">
            <div>SERVICE</div>
            <div class="service-stat">TOTAL</div>
            <div class="service-stat">WAITING</div>
            <div class="service-stat">IN PROGRESS</div>
            <div class="service-stat">COMPLETED</div>
        </div>
        
        <?php $__currentLoopData = $serviceStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="service-item">
            <div class="service-name"><?php echo e($service['name']); ?></div>
            <div class="service-stat">
                <span class="service-stat-value" style="color: #1f2937;"><?php echo e($service['total']); ?></span>
            </div>
            <div class="service-stat">
                <span class="service-stat-value" style="color: #f59e0b;"><?php echo e($service['waiting']); ?></span>
            </div>
            <div class="service-stat">
                <span class="service-stat-value" style="color: #8b5cf6;"><?php echo e($service['in_progress']); ?></span>
            </div>
            <div class="service-stat">
                <span class="service-stat-value" style="color: #10b981;"><?php echo e($service['completed']); ?></span>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <!-- Priority Distribution & Activity -->
    <div class="dashboard-sections">
        <!-- Recent Activity -->
        <div class="section-card">
            <div class="section-title">
                <i class="fas fa-stream"></i>
                Real-Time Queue Activity
            </div>
            
            <?php if($recentActivity->isEmpty()): ?>
                <p style="color: #6b7280; text-align: center; padding: 20px;">No activity yet today</p>
            <?php else: ?>
                <?php $__currentLoopData = $recentActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="activity-item">
                    <div class="activity-time"><?php echo e($activity['time']); ?></div>
                    <div class="activity-content">
                        <div>
                            <span class="activity-queue"><?php echo e($activity['queue_number']); ?></span>
                            <span class="activity-patient"><?php echo e($activity['patient_name']); ?></span>
                        </div>
                        <div class="activity-service"><?php echo e($activity['service']); ?></div>
                    </div>
                    <span class="status-badge status-<?php echo e($activity['status']); ?>"><?php echo e($activity['status']); ?></span>
                    <span class="priority-badge priority-<?php echo e($activity['priority']); ?>"><?php echo e(ucfirst($activity['priority'])); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </div>

        <!-- Side Panel -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <!-- Priority Distribution -->
            <div class="section-card">
                <div class="section-title">
                    <i class="fas fa-flag"></i>
                    Priority Distribution
                </div>
                
                <div class="priority-distribution">
                    <div class="priority-item" style="background: #fee2e2;">
                        <span class="priority-count" style="color: #991b1b;"><?php echo e($priorityStats['emergency'] ?? 0); ?></span>
                        <span class="priority-label">Emergency</span>
                    </div>
                    <div class="priority-item" style="background: #f3e8ff;">
                        <span class="priority-count" style="color: #7c3aed;"><?php echo e($priorityStats['senior'] ?? 0); ?></span>
                        <span class="priority-label">Senior</span>
                    </div>
                    <div class="priority-item" style="background: #dbeafe;">
                        <span class="priority-count" style="color: #1e40af;"><?php echo e($priorityStats['pwd'] ?? 0); ?></span>
                        <span class="priority-label">PWD</span>
                    </div>
                    <div class="priority-item" style="background: #fce7f3;">
                        <span class="priority-count" style="color: #be185d;"><?php echo e($priorityStats['pregnant'] ?? 0); ?></span>
                        <span class="priority-label">Pregnant</span>
                    </div>
                    <div class="priority-item" style="background: #f3f4f6;">
                        <span class="priority-count" style="color: #374151;"><?php echo e($priorityStats['normal'] ?? 0); ?></span>
                        <span class="priority-label">Regular</span>
                    </div>
                </div>
            </div>

            <!-- Top Performing Staff -->
            <?php if($staffActivity->isNotEmpty()): ?>
            <div class="section-card">
                <div class="section-title">
                    <i class="fas fa-user-md"></i>
                    Top Staff Today
                </div>
                
                <?php $__currentLoopData = $staffActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $staff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="staff-item">
                    <span class="staff-name"><?php echo e($staff->name); ?></span>
                    <span class="staff-count"><?php echo e($staff->queues_handled); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Auto-refresh dashboard every 30 seconds
setTimeout(function() {
    location.reload();
}, 30000);
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ramir\Desktop\Health-Center-Queue-System-main\resources\views/dashboard.blade.php ENDPATH**/ ?>