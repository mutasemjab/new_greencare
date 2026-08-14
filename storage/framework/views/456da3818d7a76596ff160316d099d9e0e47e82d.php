<?php $__env->startSection('title', 'طلبات المختبر'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">طلبات المختبر</h4>
    </div>

    <?php echo $__env->make('admin.includes.alerts.success', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('lab.requests')); ?>" class="row g-3">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control"
                        placeholder="بحث باسم المستخدم أو كود المريض..."
                        value="<?php echo e(request('search')); ?>">
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">-- جميع الحالات --</option>
                        <option value="pending"     <?php if(request('status') === 'pending'): echo 'selected'; endif; ?>>بانتظار التأكيد</option>
                        <option value="confirmed"   <?php if(request('status') === 'confirmed'): echo 'selected'; endif; ?>>مؤكد</option>
                        <option value="in_progress" <?php if(request('status') === 'in_progress'): echo 'selected'; endif; ?>>قيد التنفيذ</option>
                        <option value="completed"   <?php if(request('status') === 'completed'): echo 'selected'; endif; ?>>مكتمل</option>
                        <option value="cancelled"   <?php if(request('status') === 'cancelled'): echo 'selected'; endif; ?>>ملغي</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search me-1"></i> بحث
                    </button>
                    <a href="<?php echo e(route('lab.requests')); ?>" class="btn btn-outline-secondary w-100">
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
                            <th>المستخدم</th>
                            <th>كود المريض</th>
                            <th>عدد الفحوصات</th>
                            <th>الإجمالي</th>
                            <th>تاريخ الحجز</th>
                            <th>النتيجة</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $statusMap = [
                                'pending'     => ['label' => 'بانتظار التأكيد', 'class' => 'bg-warning-subtle text-warning'],
                                'confirmed'   => ['label' => 'مؤكد',            'class' => 'bg-info-subtle text-info'],
                                'in_progress' => ['label' => 'قيد التنفيذ',     'class' => 'bg-primary-subtle text-primary'],
                                'completed'   => ['label' => 'مكتمل',           'class' => 'bg-success-subtle text-success'],
                                'cancelled'   => ['label' => 'ملغي',            'class' => 'bg-danger-subtle text-danger'],
                            ];
                            $st = $statusMap[$req->status] ?? ['label' => $req->status, 'class' => 'bg-secondary-subtle text-secondary'];
                        ?>
                        <tr>
                            <td class="text-muted small"><?php echo e($loop->iteration); ?></td>
                            <td>
                                <div class="fw-semibold"><?php echo e($req->user?->name ?? '—'); ?></div>
                                <div class="small text-muted"><?php echo e($req->user?->phone); ?></div>
                            </td>
                            <td class="small text-muted"><?php echo e($req->patient_code ?? '—'); ?></td>
                            <td>
                                <span class="badge bg-secondary rounded-pill">
                                    <?php echo e($req->tests->count()); ?> فحص
                                </span>
                            </td>
                            <td class="fw-semibold"><?php echo e(number_format($req->total, 2)); ?> JD</td>
                            <td><?php echo e($req->booking_date ? \Carbon\Carbon::parse($req->booking_date)->format('Y/m/d') : '—'); ?></td>
                            <td>
                                <?php if($req->result_file): ?>
                                    <span class="badge bg-success-subtle text-success"><i class="bi bi-file-earmark-pdf"></i> مرفوعة</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted">لم تُرفع بعد</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge <?php echo e($st['class']); ?>"><?php echo e($st['label']); ?></span>
                            </td>
                            <td>
                                <a href="<?php echo e(route('lab.requests.show', $req)); ?>"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                لا توجد طلبات بعد
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if($requests->hasPages()): ?>
        <div class="card-footer bg-transparent d-flex justify-content-center">
            <?php echo e($requests->links()); ?>

        </div>
        <?php endif; ?>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('lab.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\green\resources\views/lab/requests/index.blade.php ENDPATH**/ ?>