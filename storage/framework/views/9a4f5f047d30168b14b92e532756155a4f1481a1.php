<?php $__env->startSection('title', 'تفاصيل طلب المختبر'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="<?php echo e(route('lab.requests')); ?>" class="btn btn-outline-secondary btn-sm">
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
                    <i class="bi bi-person-circle me-2"></i>معلومات المستخدم الطالب
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="text-muted small">الاسم</div>
                            <div class="fw-semibold"><?php echo e($request->user?->name ?? '—'); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small">الهاتف</div>
                            <div class="fw-semibold"><?php echo e($request->user?->phone ?? '—'); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small">البريد الإلكتروني</div>
                            <div class="fw-semibold"><?php echo e($request->user?->email ?? '—'); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small">الدور</div>
                            <div class="fw-semibold"><?php echo e($request->user?->role_label ?? '—'); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small">الجنس</div>
                            <div class="fw-semibold"><?php echo e($request->user?->gender_label ?? '—'); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small">تاريخ الميلاد</div>
                            <div class="fw-semibold">
                                <?php echo e($request->user?->date_of_birth ? \Carbon\Carbon::parse($request->user->date_of_birth)->format('Y/m/d') : '—'); ?>

                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small">كود المريض</div>
                            <div class="fw-semibold"><?php echo e($request->patient_code ?? '—'); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small">حالة الحساب</div>
                            <div class="fw-semibold">
                                <?php if($request->user?->is_active): ?>
                                    <span class="badge bg-success-subtle text-success">نشط</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger">غير نشط</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small">تاريخ الحجز</div>
                            <div class="fw-semibold">
                                <?php echo e($request->booking_date ? \Carbon\Carbon::parse($request->booking_date)->format('Y/m/d') : '—'); ?>

                                <?php if($request->booking_time): ?>
                                    — <?php echo e(\Carbon\Carbon::parse($request->booking_time)->format('H:i')); ?>

                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if($request->address): ?>
                        <div class="col-12">
                            <div class="text-muted small">العنوان</div>
                            <div class="fw-semibold"><?php echo e($request->address->address ?? $request->address); ?></div>
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
                    <i class="bi bi-arrow-repeat me-2"></i>تحديث الحالة
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('lab.requests.status', $request)); ?>" method="POST">
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

            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-file-earmark-pdf me-2"></i>نتيجة التحليل
                </div>
                <div class="card-body">
                    <?php if($request->result_file): ?>
                        <a href="<?php echo e(Storage::disk('public')->url($request->result_file)); ?>"
                            target="_blank" class="btn btn-outline-success w-100 mb-3">
                            <i class="bi bi-download me-1"></i> عرض/تحميل النتيجة الحالية
                        </a>
                    <?php else: ?>
                        <p class="text-muted small mb-3">لم يتم رفع نتيجة بعد.</p>
                    <?php endif; ?>

                    <form action="<?php echo e(route('lab.requests.result', $request)); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <?php echo e($request->result_file ? 'استبدال الملف' : 'رفع ملف PDF'); ?>

                            </label>
                            <input type="file" name="result_file" accept="application/pdf"
                                class="form-control <?php $__errorArgs = ['result_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <?php $__errorArgs = ['result_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-upload me-1"></i> رفع
                        </button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
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

<?php echo $__env->make('lab.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\green\resources\views/lab/requests/show.blade.php ENDPATH**/ ?>