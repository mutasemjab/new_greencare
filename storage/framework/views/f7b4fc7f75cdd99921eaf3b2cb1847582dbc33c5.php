<?php $__env->startSection('title', 'غرف صحتي'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold"><i class="bi bi-house-heart me-2"></i>غرف صحتي</h4>
        <a href="<?php echo e(route('admin.sihati.rooms.create')); ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> إنشاء غرفة
        </a>
    </div>

    <?php echo $__env->make('admin.includes.alerts.success', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('admin.includes.alerts.error', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                        class="form-control form-control-sm"
                        placeholder="بحث باسم الغرفة أو اسم المريض...">
                </div>
                <div class="col-auto">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">كل الغرف</option>
                        <option value="active"   <?php echo e(request('status') === 'active'   ? 'selected' : ''); ?>>نشطة</option>
                        <option value="inactive" <?php echo e(request('status') === 'inactive' ? 'selected' : ''); ?>>معطلة</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary">بحث</button>
                    <?php if(request()->hasAny(['search', 'status'])): ?>
                        <a href="<?php echo e(route('admin.sihati.rooms.index')); ?>" class="btn btn-sm btn-outline-secondary">مسح</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>اسم الغرفة</th>
                        <th>كود المريض</th>
                        <th>المريض</th>
                        <th>أُنشئت بواسطة</th>
                        <th>الأعضاء</th>
                        <th>الخصم</th>
                        <th>الحالة</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="text-muted small"><?php echo e($loop->iteration); ?></td>
                        <td>
                            <a href="<?php echo e(route('admin.sihati.rooms.show', $room)); ?>" class="fw-semibold text-decoration-none">
                                <?php echo e($room->name); ?>

                            </a>
                            <?php if($room->description): ?>
                                <div class="small text-muted"><?php echo e(Str::limit($room->description, 50)); ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="fw-semibold text-primary"><?php echo e($room->patient_code ?? '—'); ?></td>
                        <td>
                            <div class="fw-semibold"><?php echo e($room->patient?->name ?? '—'); ?></div>
                            <div class="small text-muted"><?php echo e($room->patient?->phone); ?></div>
                        </td>
                        <td class="small text-muted"><?php echo e($room->createdBy?->name ?? '—'); ?></td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                <?php echo e($room->members_count); ?> عضو
                            </span>
                        </td>
                        <td>
                            <?php if($room->discount_value > 0): ?>
                                <span class="badge bg-warning-subtle text-warning">
                                    <?php echo e($room->discount_value); ?>%
                                </span>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($room->is_active): ?>
                                <span class="badge bg-success-subtle text-success">نشطة</span>
                            <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger">معطلة</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <a href="<?php echo e(route('admin.sihati.rooms.show', $room)); ?>"
                                class="btn btn-sm btn-outline-primary" title="عرض التفاصيل">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="<?php echo e(route('admin.sihati.rooms.edit', $room)); ?>"
                                class="btn btn-sm btn-outline-warning" title="تعديل">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="<?php echo e(route('admin.sihati.rooms.toggle', $room)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PATCH'); ?>
                                <button class="btn btn-sm <?php echo e($room->is_active ? 'btn-outline-danger' : 'btn-outline-success'); ?>"
                                    title="<?php echo e($room->is_active ? 'تعطيل' : 'تفعيل'); ?>">
                                    <i class="bi bi-<?php echo e($room->is_active ? 'pause-circle' : 'play-circle'); ?>"></i>
                                </button>
                            </form>
                            <form action="<?php echo e(route('admin.sihati.rooms.destroy', $room)); ?>" method="POST" class="d-inline"
                                onsubmit="return confirm('هل أنت متأكد من حذف هذه الغرفة؟ لا يمكن التراجع عن هذا الإجراء.')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button class="btn btn-sm btn-outline-danger" title="حذف">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">
                            <i class="bi bi-house-heart fs-3 d-block mb-2"></i>
                            لا توجد غرف
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($rooms->hasPages()): ?>
        <div class="card-footer bg-transparent d-flex justify-content-end">
            <?php echo e($rooms->links()); ?>

        </div>
        <?php endif; ?>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\green\resources\views/admin/sihati/rooms/index.blade.php ENDPATH**/ ?>