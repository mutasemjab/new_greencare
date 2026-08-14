<nav class="navbar" id="navbar">

    
    <button class="navbar-toggler"
            id="sidebarToggler"
            aria-label="<?php echo e(__('messages.toggle_sidebar')); ?>">
        <i class="bi bi-list"></i>
    </button>

    
    <div class="navbar-search">
        <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="text"
                   placeholder="<?php echo e(__('messages.search_placeholder')); ?>"
                   aria-label="<?php echo e(__('messages.search_placeholder')); ?>">
        </div>
    </div>

    
    <div class="navbar-end">

        <a href="#"
           class="icon-btn"
           title="<?php echo e(__('messages.notifications')); ?>">
            <i class="bi bi-bell"></i>
            <span class="dot"></span>
        </a>

        <?php $__currentLoopData = LaravelLocalization::getSupportedLocales(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $locale => $properties): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($locale !== app()->getLocale()): ?>
                <a href="<?php echo e(LaravelLocalization::getLocalizedURL($locale, null, [], true)); ?>"
                   class="icon-btn"
                   hreflang="<?php echo e($locale); ?>">
                    <?php echo e(strtoupper($locale)); ?>

                </a>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <div class="nav-divider"></div>

        <div class="dropdown">
            <div class="user-menu" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="user-avatar">A</div>

                <div class="user-info">
                    <span class="user-name">Admin</span>
                    <span class="user-role"><?php echo e(__('messages.administrator')); ?></span>
                </div>

                <i class="bi bi-chevron-down ms-1"
                   style="font-size:.65rem;color:var(--muted)"></i>
            </div>

            <ul class="dropdown-menu dropdown-menu-end shadow-sm border"
                style="border-radius:12px;min-width:180px;font-size:.845rem;">

                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                       href="#">
                        <i class="bi bi-person-circle"
                           style="color:var(--muted)"></i>
                        <?php echo e(__('messages.my_profile')); ?>

                    </a>
                </li>

                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                       href="#">
                        <i class="bi bi-gear"
                           style="color:var(--muted)"></i>
                        <?php echo e(__('messages.settings')); ?>

                    </a>
                </li>

                <li>
                    <hr class="dropdown-divider my-1">
                </li>

                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger"
                       href="#"
                       onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                        <i class="bi bi-box-arrow-right"></i>
                        <?php echo e(__('messages.sign_out')); ?>

                    </a>

                    <form id="admin-logout-form"
                          action="<?php echo e(route('admin.logout')); ?>"
                          method="POST"
                          class="d-none">
                        <?php echo csrf_field(); ?>
                    </form>
                </li>

            </ul>
        </div>

    </div>
</nav><?php /**PATH C:\xampp\htdocs\green\resources\views/admin/includes/navbar.blade.php ENDPATH**/ ?>