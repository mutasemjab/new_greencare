<?php $__env->startSection('title', $receipt->exists ? 'تعديل سند قبض' : 'سند قبض جديد'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="<?php echo e(route('admin.receipts.index')); ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right"></i>
        </a>
        <h4 class="mb-0 fw-bold">
            <?php echo e($receipt->exists ? 'تعديل سند القبض رقم ' . $receipt->number : 'سند قبض جديد'); ?>

        </h4>
    </div>

    <div class="card border-0 shadow-sm mx-auto" style="max-width:700px;">
        <div class="card-body p-4">
            <form action="<?php echo e($receipt->exists ? route('admin.receipts.update', $receipt) : route('admin.receipts.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php if($receipt->exists): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label fw-semibold">استلمنا من السيد/ة <span class="text-danger">*</span></label>
                    <input type="text" name="person_name" class="form-control <?php $__errorArgs = ['person_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        value="<?php echo e(old('person_name', $receipt->person_name)); ?>" required autofocus>
                    <?php $__errorArgs = ['person_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">المبلغ (دينار أردني) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="amount" step="0.001" min="0.001"
                                class="form-control <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                value="<?php echo e(old('amount', $receipt->amount)); ?>" required>
                            <span class="input-group-text">د.أ</span>
                            <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">التاريخ <span class="text-danger">*</span></label>
                        <input type="date" name="receipt_date" class="form-control <?php $__errorArgs = ['receipt_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            value="<?php echo e(old('receipt_date', $receipt->receipt_date?->format('Y-m-d'))); ?>" required>
                        <?php $__errorArgs = ['receipt_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">طريقة الدفع <span class="text-danger">*</span></label>
                    <select name="payment_method" class="form-select <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php $__currentLoopData = \App\Models\ReceiptVoucher::PAYMENT_METHODS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($value); ?>" <?php echo e(old('payment_method', $receipt->payment_method) === $value ? 'selected' : ''); ?>>
                                <?php echo e($label); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">وذلك عن <span class="text-muted small fw-normal">(اختياري)</span></label>
                    <input type="text" name="description" class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        value="<?php echo e(old('description', $receipt->description)); ?>" placeholder="مثال: رسوم زيارة تمريضية لشهر تشرين الثاني">
                    <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-check-lg me-1"></i>
                    <?php echo e($receipt->exists ? 'حفظ التعديلات' : 'حفظ السند'); ?>

                </button>
            </form>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\green\resources\views/admin/receipts/form.blade.php ENDPATH**/ ?>