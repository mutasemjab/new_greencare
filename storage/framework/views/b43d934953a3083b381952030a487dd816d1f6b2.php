<?php $__env->startSection('title', __('messages.page_dashboard')); ?>

<?php $__env->startSection('content'); ?>


<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title"><?php echo e(__('messages.page_dashboard')); ?></h1>
        <p class="page-sub"><?php echo e(__('messages.welcome_back')); ?></p>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><?php echo e(__('messages.page_dashboard')); ?></li>
        </ol>
    </nav>
</div>


<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>



<div class="row g-3 mb-3">

    
    <div class="col-12 col-xl-8">
        <div class="panel-card h-100">
            <div class="panel-card-header">
                <h2 class="panel-card-title"><?php echo e(__('messages.new_messages')); ?></h2>
            </div>
         
        </div>
    </div>

   

</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\green\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>