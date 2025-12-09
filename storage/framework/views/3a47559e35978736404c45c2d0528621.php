

<?php $__env->startSection('title', 'Support Messages - Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="container" style="max-width: 1200px; margin: 2rem auto; padding: 0 1rem;">
    <div style="background: white; border-radius: 8px; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.75rem; font-weight: 600; color: #1f2937; margin-bottom: 0.5rem;">
                    <i class="fas fa-headset"></i> Support Messages
                </h1>
                <p style="color: #6b7280;">Manage user support requests and inquiries</p>
            </div>
            <div style="display: flex; gap: 1rem;">
                <span style="padding: 0.5rem 1rem; background: #fef3c7; color: #92400e; border-radius: 4px; font-size: 0.875rem;">
                    <i class="fas fa-clock"></i> <?php echo e($messages->where('status', 'pending')->count()); ?> Pending
                </span>
                <span style="padding: 0.5rem 1rem; background: #d1fae5; color: #065f46; border-radius: 4px; font-size: 0.875rem;">
                    <i class="fas fa-check"></i> <?php echo e($messages->where('status', 'replied')->count()); ?> Replied
                </span>
            </div>
        </div>

        <?php if(session('success')): ?>
            <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem;">
                <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if($messages->isEmpty()): ?>
            <div style="text-align: center; padding: 4rem 2rem; color: #9ca3af;">
                <i class="fas fa-inbox" style="font-size: 64px; margin-bottom: 1rem;"></i>
                <p style="font-size: 1.125rem;">No support messages yet</p>
            </div>
        <?php else: ?>
            <?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 1.5rem; margin-bottom: 1rem; 
                    <?php if($msg->status === 'pending'): ?> border-left: 4px solid #f59e0b; <?php endif; ?>">
                    
                    <div style="display: flex; justify-content: between; align-items: start; margin-bottom: 1rem;">
                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                                <strong style="font-size: 1.125rem; color: #1f2937;"><?php echo e($msg->subject); ?></strong>
                                <span style="font-size: 0.75rem; padding: 0.25rem 0.75rem; border-radius: 12px; font-weight: 600;
                                    <?php if($msg->status === 'replied'): ?> background: #d1fae5; color: #065f46;
                                    <?php elseif($msg->status === 'closed'): ?> background: #e5e7eb; color: #6b7280;
                                    <?php else: ?> background: #fef3c7; color: #92400e;
                                    <?php endif; ?>">
                                    <?php echo e(ucfirst($msg->status)); ?>

                                </span>
                            </div>
                            <div style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.5rem;">
                                <i class="fas fa-user"></i> <?php echo e($msg->name); ?> (<?php echo e($msg->email); ?>)
                                <span style="margin-left: 1rem;">
                                    <i class="fas fa-tag"></i> <?php echo e(ucfirst($msg->user_type)); ?>

                                </span>
                            </div>
                            <small style="color: #9ca3af;">
                                <i class="fas fa-clock"></i> <?php echo e($msg->created_at->format('M d, Y h:i A')); ?>

                            </small>
                        </div>
                    </div>

                    <div style="background: #f9fafb; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                        <strong style="font-size: 0.875rem; color: #374151;">Message:</strong>
                        <p style="color: #6b7280; margin-top: 0.5rem; white-space: pre-wrap;"><?php echo e($msg->message); ?></p>
                    </div>

                    <?php if($msg->admin_reply): ?>
                        <div style="background: #f0fdf4; border-left: 4px solid #059669; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                            <strong style="color: #059669; font-size: 0.875rem;">
                                <i class="fas fa-reply"></i> Your Reply:
                            </strong>
                            <p style="color: #374151; margin-top: 0.5rem; white-space: pre-wrap;"><?php echo e($msg->admin_reply); ?></p>
                            <small style="color: #6b7280;">Replied on: <?php echo e($msg->replied_at->format('M d, Y h:i A')); ?></small>
                        </div>
                    <?php else: ?>
                        <form method="POST" action="<?php echo e(route('admin.support.reply', $msg->id)); ?>">
                            <?php echo csrf_field(); ?>
                            <div style="margin-bottom: 1rem;">
                                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #374151; font-size: 0.875rem;">
                                    <i class="fas fa-reply"></i> Reply to <?php echo e($msg->name); ?>

                                </label>
                                <textarea 
                                    name="reply" 
                                    required 
                                    rows="4"
                                    style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 4px; font-size: 0.875rem; resize: vertical;"
                                    placeholder="Type your reply here..."
                                ></textarea>
                            </div>
                            <button 
                                type="submit" 
                                style="background: #059669; color: white; padding: 0.625rem 1.5rem; border: none; border-radius: 4px; font-size: 0.875rem; font-weight: 600; cursor: pointer;"
                            >
                                <i class="fas fa-paper-plane"></i> Send Reply
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <div style="margin-top: 2rem;">
                <?php echo e($messages->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ramir\Desktop\Health-Center-Queue-System-main\resources\views/admin/support/index.blade.php ENDPATH**/ ?>