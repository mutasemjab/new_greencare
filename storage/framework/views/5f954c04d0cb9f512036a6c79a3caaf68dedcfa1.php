<?php $__env->startSection('title', 'تفاصيل الطلب #' . $order->id); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="<?php echo e(route('admin.orders.index')); ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right"></i>
        </a>
        <h4 class="mb-0 fw-bold">طلب رقم #<?php echo e($order->id); ?></h4>
        <?php
            $statusMap = [
                'pending'    => ['label' => 'بانتظار التأكيد', 'class' => 'bg-warning-subtle text-warning'],
                'confirmed'  => ['label' => 'مؤكد',            'class' => 'bg-info-subtle text-info'],
                'processing' => ['label' => 'قيد المعالجة',    'class' => 'bg-primary-subtle text-primary'],
                'shipped'    => ['label' => 'تم الشحن',        'class' => 'bg-primary-subtle text-primary'],
                'delivered'  => ['label' => 'تم التوصيل',      'class' => 'bg-success-subtle text-success'],
                'cancelled'  => ['label' => 'ملغي',            'class' => 'bg-danger-subtle text-danger'],
            ];
            $st = $statusMap[$order->status] ?? ['label' => $order->status, 'class' => 'bg-secondary-subtle text-secondary'];
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
                            <div class="text-muted small">الاسم</div>
                            <div class="fw-semibold"><?php echo e($order->user?->name ?? '—'); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small">الهاتف</div>
                            <div class="fw-semibold"><?php echo e($order->user?->phone ?? '—'); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small">البريد الإلكتروني</div>
                            <div class="fw-semibold"><?php echo e($order->user?->email ?? '—'); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small">منطقة التوصيل</div>
                            <div class="fw-semibold"><?php echo e($order->deliveryZone?->name ?? '—'); ?></div>
                        </div>
                        <div class="col-12">
                            <div class="text-muted small">العنوان</div>
                            <div class="fw-semibold">
                                <?php if($order->address): ?>
                                    <?php echo e($order->address->label); ?> — <?php echo e($order->address->address); ?>, <?php echo e($order->address->city); ?>

                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-cart me-2"></i>المنتجات
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>المنتج</th>
                                    <th class="text-center">الكمية</th>
                                    <th class="text-end">السعر</th>
                                    <th class="text-end">المجموع</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold"><?php echo e($item->product?->name ?? $item->product_name); ?></div>
                                    </td>
                                    <td class="text-center"><?php echo e($item->quantity); ?></td>
                                    <td class="text-end"><?php echo e(number_format($item->price, 2)); ?> JD</td>
                                    <td class="text-end fw-semibold"><?php echo e(number_format($item->price * $item->quantity, 2)); ?> JD</td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-semibold">المجموع الفرعي:</td>
                                    <td class="text-end fw-semibold"><?php echo e(number_format($order->subtotal, 2)); ?> JD</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end fw-semibold">رسوم التوصيل:</td>
                                    <td class="text-end fw-semibold"><?php echo e(number_format($order->delivery_fee, 2)); ?> JD</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold fs-5">الإجمالي:</td>
                                    <td class="text-end fw-bold fs-5 text-primary"><?php echo e(number_format($order->total, 2)); ?> JD</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            
            <?php if($order->notes): ?>
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-chat-text me-2"></i>ملاحظات
                </div>
                <div class="card-body">
                    <p class="mb-0"><?php echo e($order->notes); ?></p>
                </div>
            </div>
            <?php endif; ?>

        </div>

        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-arrow-repeat me-2"></i>تحديث الحالة
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('admin.orders.status', $order)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">الحالة</label>
                            <select name="status" class="form-select">
                                <option value="pending"    <?php if($order->status === 'pending'): echo 'selected'; endif; ?>>بانتظار التأكيد</option>
                                <option value="confirmed"  <?php if($order->status === 'confirmed'): echo 'selected'; endif; ?>>مؤكد</option>
                                <option value="processing" <?php if($order->status === 'processing'): echo 'selected'; endif; ?>>قيد المعالجة</option>
                                <option value="shipped"    <?php if($order->status === 'shipped'): echo 'selected'; endif; ?>>تم الشحن</option>
                                <option value="delivered"  <?php if($order->status === 'delivered'): echo 'selected'; endif; ?>>تم التوصيل</option>
                                <option value="cancelled"  <?php if($order->status === 'cancelled'): echo 'selected'; endif; ?>>ملغي</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-lg me-1"></i> تحديث
                        </button>
                    </form>
                </div>
            </div>

            
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-info-circle me-2"></i>معلومات الطلب
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <span class="text-muted small">تاريخ الطلب:</span>
                        <div class="fw-semibold"><?php echo e($order->created_at->format('Y/m/d H:i')); ?></div>
                    </div>
                    <div>
                        <span class="text-muted small">آخر تحديث:</span>
                        <div class="fw-semibold"><?php echo e($order->updated_at->format('Y/m/d H:i')); ?></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\green\resources\views/admin/orders/show.blade.php ENDPATH**/ ?>