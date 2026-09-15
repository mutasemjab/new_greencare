<?php $__env->startSection('title', 'مفاتيح API'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">
            <i class="bi bi-key me-2"></i>مفاتيح API — تكاملات خارجية
        </h4>
        <a href="<?php echo e(route('admin.api-clients.create')); ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> مفتاح جديد
        </a>
    </div>

    <?php echo $__env->make('admin.includes.alerts.success', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php if(session('generated_key')): ?>
    <div class="alert alert-warning">
        <div class="fw-bold mb-2"><i class="bi bi-exclamation-triangle me-1"></i> هذا المفتاح لن يظهر مرة أخرى — انسخه الآن وسلّمه للمنصة الخارجية:</div>
        <div class="input-group">
            <input type="text" class="form-control font-monospace" value="<?php echo e(session('generated_key')); ?>" id="generatedKeyInput" readonly>
            <button class="btn btn-outline-secondary" type="button" onclick="copyGeneratedKey()">
                <i class="bi bi-clipboard me-1"></i> نسخ
            </button>
        </div>
        <div class="small text-muted mt-2">
            يُرسل بترويسة الطلب: <code>X-API-Key: <?php echo e(session('generated_key')); ?></code>
        </div>
    </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>الحالة</th>
                        <th>آخر استخدام</th>
                        <th>تاريخ الإنشاء</th>
                        <th class="text-end"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $apiClients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="text-muted small"><?php echo e($loop->iteration); ?></td>
                        <td class="fw-semibold"><?php echo e($client->name); ?></td>
                        <td>
                            <?php if($client->is_active): ?>
                                <span class="badge bg-success-subtle text-success">مفعّل</span>
                            <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger">معطّل</span>
                            <?php endif; ?>
                        </td>
                        <td class="small text-muted"><?php echo e($client->last_used_at?->format('Y/m/d H:i') ?? 'لم يُستخدم بعد'); ?></td>
                        <td class="small text-muted"><?php echo e($client->created_at->format('Y/m/d')); ?></td>
                        <td class="text-end">
                            <form action="<?php echo e(route('admin.api-clients.toggle', $client)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PATCH'); ?>
                                <button class="btn btn-sm <?php echo e($client->is_active ? 'btn-outline-danger' : 'btn-outline-success'); ?>">
                                    <i class="bi bi-<?php echo e($client->is_active ? 'pause-circle' : 'play-circle'); ?>"></i>
                                </button>
                            </form>
                            <form action="<?php echo e(route('admin.api-clients.destroy', $client)); ?>" method="POST" class="d-inline"
                                onsubmit="return confirm('حذف هذا المفتاح نهائيًا؟ أي منصة تستخدمه ستفقد الوصول فورًا.')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-key fs-3 d-block mb-2"></i>
                            لا توجد مفاتيح API بعد
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
function copyGeneratedKey() {
    const input = document.getElementById('generatedKeyInput');
    input.select();
    navigator.clipboard.writeText(input.value);
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\green\resources\views/admin/api-clients/index.blade.php ENDPATH**/ ?>