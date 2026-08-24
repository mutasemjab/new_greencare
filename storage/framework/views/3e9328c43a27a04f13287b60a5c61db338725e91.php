<?php $__env->startSection('title', 'طلبات المتجر'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">طلبات المتجر</h4>
    </div>

    <?php echo $__env->make('admin.includes.alerts.success', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('admin.orders.index')); ?>" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control"
                        placeholder="بحث باسم العميل أو رقم الطلب..."
                        value="<?php echo e(request('search')); ?>">
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">-- جميع الحالات --</option>
                        <option value="pending"      <?php if(request('status') === 'pending'): echo 'selected'; endif; ?>>بانتظار التأكيد</option>
                        <option value="confirmed"    <?php if(request('status') === 'confirmed'): echo 'selected'; endif; ?>>مؤكد</option>
                        <option value="processing"   <?php if(request('status') === 'processing'): echo 'selected'; endif; ?>>قيد المعالجة</option>
                        <option value="shipped"      <?php if(request('status') === 'shipped'): echo 'selected'; endif; ?>>تم الشحن</option>
                        <option value="delivered"    <?php if(request('status') === 'delivered'): echo 'selected'; endif; ?>>تم التوصيل</option>
                        <option value="cancelled"    <?php if(request('status') === 'cancelled'): echo 'selected'; endif; ?>>ملغي</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search me-1"></i> بحث
                    </button>
                    <a href="<?php echo e(route('admin.orders.index')); ?>" class="btn btn-outline-secondary w-100">
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
                            <th>العميل</th>
                            <th>العنوان</th>
                            <th>المجموع</th>
                            <th>رسوم التوصيل</th>
                            <th>الإجمالي</th>
                            <th>الحالة</th>
                            <th>تاريخ الطلب</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
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
                        <tr>
                            <td class="text-muted small"><?php echo e($order->id); ?></td>
                            <td>
                                <div class="fw-semibold"><?php echo e($order->user?->name ?? '—'); ?></div>
                                <div class="small text-muted"><?php echo e($order->user?->phone); ?></div>
                            </td>
                            <td class="small"><?php echo e(Str::limit($order->address?->address, 40)); ?></td>
                            <td><?php echo e(number_format($order->subtotal, 2)); ?> JD</td>
                            <td><?php echo e(number_format($order->delivery_fee, 2)); ?> JD</td>
                            <td class="fw-bold"><?php echo e(number_format($order->total, 2)); ?> JD</td>
                            <td>
                                <span class="badge <?php echo e($st['class']); ?>"><?php echo e($st['label']); ?></span>
                            </td>
                            <td class="small text-muted"><?php echo e($order->created_at->format('Y/m/d H:i')); ?></td>
                            <td>
                                <a href="<?php echo e(route('admin.orders.show', $order)); ?>"
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
        <?php if($orders->hasPages()): ?>
        <div class="card-footer bg-transparent d-flex justify-content-center">
            <?php echo e($orders->links()); ?>

        </div>
        <?php endif; ?>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\green\resources\views/admin/orders/index.blade.php ENDPATH**/ ?>