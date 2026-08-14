<?php $__env->startSection('title', 'تفاصيل طلب المختبر'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="<?php echo e(route('admin.lab.requests')); ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right"></i>
        </a>
        <h4 class="mb-0 fw-bold">طلب مختبر #<?php echo e($request->id); ?></h4>
        <?php
            $statusMap = [
                'pending'     => ['label' => 'بانتظار التأكيد', 'class' => 'bg-warning-subtle text-warning'],
                'confirmed'   => ['label' => 'مؤكد',            'class' => 'bg-info-subtle text-info'],
                'in_progress' => ['label' => 'قيد التنفيذ',     'class' => 'bg-primary-subtle text-primary'],
                'completed'   => ['label' => 'مكتمل',           'class' => 'bg-success-subtle text-success'],
                'cancelled'   => ['label' => 'ملغي',            'class' => 'bg-danger-subtle text-danger'],
            ];
            $st = $statusMap[$request->status] ?? ['label' => $request->status, 'class' => 'bg-secondary-subtle text-secondary'];
        ?>
        <span class="badge <?php echo e($st['class']); ?> fs-6"><?php echo e($st['label']); ?></span>
    </div>

    <?php echo $__env->make('admin.includes.alerts.success', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="row g-4">
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-person-circle me-2"></i>معلومات العميل
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="text-muted small">المستخدم</div>
                            <div class="fw-semibold"><?php echo e($request->user?->name ?? '—'); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small">الهاتف</div>
                            <div class="fw-semibold"><?php echo e($request->user?->phone ?? '—'); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small">كود المريض</div>
                            <div class="fw-semibold"><?php echo e($request->patient_code ?? '—'); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small">تاريخ الحجز</div>
                            <div class="fw-semibold">
                                <?php echo e($request->booking_date ? \Carbon\Carbon::parse($request->booking_date)->format('Y/m/d') : '—'); ?>

                            </div>
                        </div>
                        <?php if($request->address): ?>
                        <div class="col-12">
                            <div class="text-muted small">العنوان</div>
                            <div class="fw-semibold"><?php echo e($request->address); ?></div>
                        </div>
                        <?php endif; ?>
                        <?php if($request->notes): ?>
                        <div class="col-12">
                            <div class="text-muted small">ملاحظات</div>
                            <div><?php echo e($request->notes); ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-clipboard2-pulse me-2"></i>الفحوصات المطلوبة
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>اسم الفحص</th>
                                    <th>الفئة</th>
                                    <th class="text-end">السعر</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $request->tests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reqTest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="fw-semibold"><?php echo e($reqTest->test?->name ?? '—'); ?></td>
                                    <td class="text-muted small"><?php echo e($reqTest->test?->category?->name ?? '—'); ?></td>
                                    <td class="text-end"><?php echo e(number_format($reqTest->unit_price, 2)); ?> JD</td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="2" class="fw-bold fs-5">الإجمالي</td>
                                    <td class="text-end fw-bold fs-5 text-primary"><?php echo e(number_format($request->total, 2)); ?> JD</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-file-earmark-pdf me-2"></i>نتيجة التحليل
                </div>
                <div class="card-body">
                    <?php if($request->result_file): ?>
                        <a href="<?php echo e(Storage::disk('public')->url($request->result_file)); ?>"
                            target="_blank" class="btn btn-outline-success w-100">
                            <i class="bi bi-download me-1"></i> عرض/تحميل النتيجة
                        </a>
                    <?php else: ?>
                        <p class="text-muted small mb-0">لم يقم المختبر برفع النتيجة بعد.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-arrow-repeat me-2"></i>تحديث الحالة
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('admin.lab.requests.status', $request)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">الحالة</label>
                            <select name="status" class="form-select">
                                <option value="pending"     <?php if($request->status === 'pending'): echo 'selected'; endif; ?>>بانتظار التأكيد</option>
                                <option value="confirmed"   <?php if($request->status === 'confirmed'): echo 'selected'; endif; ?>>مؤكد</option>
                                <option value="in_progress" <?php if($request->status === 'in_progress'): echo 'selected'; endif; ?>>قيد التنفيذ</option>
                                <option value="completed"   <?php if($request->status === 'completed'): echo 'selected'; endif; ?>>مكتمل</option>
                                <option value="cancelled"   <?php if($request->status === 'cancelled'): echo 'selected'; endif; ?>>ملغي</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-lg me-1"></i> تحديث
                        </button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <div class="mb-2">
                        <span class="text-muted small">تاريخ الإنشاء:</span>
                        <div class="fw-semibold"><?php echo e($request->created_at->format('Y/m/d H:i')); ?></div>
                    </div>
                    <div>
                        <span class="text-muted small">آخر تحديث:</span>
                        <div class="fw-semibold"><?php echo e($request->updated_at->format('Y/m/d H:i')); ?></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\green\resources\views/admin/lab/requests/show.blade.php ENDPATH**/ ?>