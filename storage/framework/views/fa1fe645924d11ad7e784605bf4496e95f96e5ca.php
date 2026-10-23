<?php $__env->startSection('title', 'الإشعارات'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">الإشعارات المُرسلة</h4>
        <a href="<?php echo e(route('admin.notifications.create')); ?>" class="btn btn-primary">
            <i class="bi bi-send me-1"></i> إرسال إشعار
        </a>
    </div>

    <?php echo $__env->make('admin.includes.alerts.success', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php if($broadcasts->isNotEmpty()): ?>
    <?php $inProgress = $broadcasts->whereIn('status', ['pending', 'sending'])->isNotEmpty(); ?>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent fw-bold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-broadcast me-2"></i>الإرسال لجميع المستخدمين (في الخلفية)</span>
            <?php if($inProgress): ?>
                <span class="small text-muted fw-normal">
                    <span class="spinner-border spinner-border-sm me-1"></span> يتم التحديث تلقائياً
                </span>
            <?php endif; ?>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>العنوان</th>
                        <th>الحالة</th>
                        <th style="min-width: 200px">التقدّم</th>
                        <th>وصل</th>
                        <th>ما وصل</th>
                        <th>أرسلها</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $broadcasts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $broadcast): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?php echo e($broadcast->title); ?></div>
                            <div class="small text-muted"><?php echo e(\Illuminate\Support\Str::limit($broadcast->body, 50)); ?></div>
                        </td>
                        <td>
                            <span class="badge bg-<?php echo e($broadcast->status_color); ?>-subtle text-<?php echo e($broadcast->status_color); ?>">
                                <?php echo e($broadcast->status_label); ?>

                            </span>
                            <?php if($broadcast->status === 'failed' && $broadcast->error): ?>
                                <div class="small text-danger mt-1"><?php echo e(\Illuminate\Support\Str::limit($broadcast->error, 60)); ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="progress" style="height: 8px">
                                <div class="progress-bar bg-<?php echo e($broadcast->status_color); ?>" style="width: <?php echo e($broadcast->progress_percent); ?>%"></div>
                            </div>
                            <div class="small text-muted mt-1">
                                <?php echo e(number_format($broadcast->processed_count)); ?> / <?php echo e(number_format($broadcast->total_users)); ?>

                                (<?php echo e($broadcast->progress_percent); ?>%)
                            </div>
                        </td>
                        <td class="text-success fw-semibold"><?php echo e(number_format($broadcast->sent_count)); ?></td>
                        <td class="text-muted"><?php echo e(number_format($broadcast->failed_count)); ?></td>
                        <td class="small text-muted"><?php echo e($broadcast->sentBy?->name ?? '—'); ?></td>
                        <td class="small text-muted"><?php echo e($broadcast->created_at->format('Y/m/d H:i')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if($inProgress): ?>
    <?php $__env->startPush('scripts'); ?>
    <script>
        // Refresh progress while a background send is running.
        setTimeout(function () { window.location.reload(); }, 15000);
    </script>
    <?php $__env->stopPush(); ?>
    <?php endif; ?>
    <?php endif; ?>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('admin.notifications.index')); ?>" class="row g-3">
                <div class="col-md-8">
                    <input type="text" name="search" class="form-control"
                        placeholder="بحث بالعنوان أو اسم المستخدم..." value="<?php echo e(request('search')); ?>">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search me-1"></i> بحث
                    </button>
                    <a href="<?php echo e(route('admin.notifications.index')); ?>" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>العنوان</th>
                            <th>النص</th>
                            <th>المستلم</th>
                            <th>النوع</th>
                            <th>أرسلها</th>
                            <th>حالة الإرسال</th>
                            <th>مقروء؟</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="text-muted small"><?php echo e($loop->iteration); ?></td>
                            <td class="fw-semibold"><?php echo e($notification->title); ?></td>
                            <td class="small"><?php echo e(\Illuminate\Support\Str::limit($notification->body, 40)); ?></td>
                            <td>
                                <div class="fw-semibold"><?php echo e($notification->user?->name ?? '—'); ?></div>
                                <div class="small text-muted"><?php echo e($notification->user?->phone); ?></div>
                            </td>
                            <td>
                                <?php if($notification->type === 'broadcast'): ?>
                                    <span class="badge bg-info-subtle text-info">للجميع</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary">شخصي</span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted"><?php echo e($notification->sentBy?->name ?? '—'); ?></td>
                            <td>
                                <?php if($notification->fcm_sent): ?>
                                    <span class="badge bg-success-subtle text-success">وصل الإشعار</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning">ما وصل (لا يوجد جهاز)</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($notification->is_read): ?>
                                    <span class="badge bg-success-subtle text-success">مقروء</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted">غير مقروء</span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted"><?php echo e($notification->created_at->format('Y/m/d H:i')); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <i class="bi bi-bell-slash fs-3 d-block mb-2"></i>
                                ما في إشعارات مُرسلة من لوحة التحكم بعد
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if($notifications->hasPages()): ?>
        <div class="card-footer bg-transparent d-flex justify-content-center">
            <?php echo e($notifications->links()); ?>

        </div>
        <?php endif; ?>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\green\resources\views/admin/notifications/index.blade.php ENDPATH**/ ?>