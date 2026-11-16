<?php $__env->startSection('title', 'سندات القبض'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold"><i class="bi bi-receipt me-2"></i>سندات القبض</h4>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('receipt-add')): ?>
        <a href="<?php echo e(route('admin.receipts.create')); ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> سند قبض جديد
        </a>
        <?php endif; ?>
    </div>

    <?php echo $__env->make('admin.includes.alerts.success', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('admin.includes.alerts.error', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('admin.receipts.index')); ?>" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-1">بحث</label>
                    <input type="text" name="search" class="form-control"
                        placeholder="اسم الشخص، البيان أو رقم السند..." value="<?php echo e(request('search')); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">من تاريخ</label>
                    <input type="date" name="from" class="form-control" value="<?php echo e(request('from')); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">إلى تاريخ</label>
                    <input type="date" name="to" class="form-control" value="<?php echo e(request('to')); ?>">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="<?php echo e(route('admin.receipts.index')); ?>" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            <span class="fw-bold"><?php echo e(number_format($receipts->total())); ?> سند</span>
            <span>المجموع: <strong class="text-success"><?php echo e(number_format($total, 3)); ?> د.أ</strong></span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>رقم السند</th>
                        <th>التاريخ</th>
                        <th>استلمنا من</th>
                        <th>المبلغ</th>
                        <th>طريقة الدفع</th>
                        <th>وذلك عن</th>
                        <th>أنشأه</th>
                        <th class="text-end"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $receipts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $receipt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="fw-semibold"><?php echo e($receipt->number); ?></td>
                        <td class="small"><?php echo e($receipt->receipt_date->format('Y/m/d')); ?></td>
                        <td><?php echo e($receipt->person_name); ?></td>
                        <td class="fw-semibold text-success"><?php echo e(number_format($receipt->amount, 3)); ?> د.أ</td>
                        <td><span class="badge bg-light text-dark"><?php echo e($receipt->payment_method_label); ?></span></td>
                        <td class="small"><?php echo e(\Illuminate\Support\Str::limit($receipt->description, 40) ?: '—'); ?></td>
                        <td class="small text-muted"><?php echo e($receipt->createdBy?->name ?? '—'); ?></td>
                        <td class="text-end text-nowrap">
                            <a href="<?php echo e(route('admin.receipts.show', $receipt)); ?>" class="btn btn-sm btn-outline-primary" title="عرض / طباعة">
                                <i class="bi bi-printer"></i>
                            </a>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('receipt-edit')): ?>
                            <a href="<?php echo e(route('admin.receipts.edit', $receipt)); ?>" class="btn btn-sm btn-outline-warning" title="تعديل">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('receipt-delete')): ?>
                            <form action="<?php echo e(route('admin.receipts.destroy', $receipt)); ?>" method="POST" class="d-inline"
                                onsubmit="return confirm('حذف سند القبض رقم <?php echo e($receipt->number); ?>؟')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button class="btn btn-sm btn-outline-danger" title="حذف">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-receipt fs-3 d-block mb-2"></i>
                            لا توجد سندات قبض
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($receipts->hasPages()): ?>
        <div class="card-footer bg-transparent d-flex justify-content-center">
            <?php echo e($receipts->links('pagination::bootstrap-5')); ?>

        </div>
        <?php endif; ?>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\green\resources\views/admin/receipts/index.blade.php ENDPATH**/ ?>