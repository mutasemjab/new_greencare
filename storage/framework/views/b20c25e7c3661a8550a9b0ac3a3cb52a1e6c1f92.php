<aside class="sidebar" id="sidebar">

    
    <div class="sidebar-brand">
        <div class="brand-icon">
            <i class="bi bi-heart-pulse-fill"></i>
        </div>
        <span class="brand-text">Green Medical</span>
    </div>

    
    <nav class="sidebar-nav">

        
        <div class="nav-label">الرئيسية</div>
        <ul>
            <li class="nav-item">
                <a href="<?php echo e(route('admin.dashboard')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
                    <i class="nav-icon bi bi-speedometer2"></i>
                    <span>لوحة التحكم</span>
                </a>
            </li>
        </ul>

        
        <div class="nav-label">المستخدمون</div>
        <ul>
            <li class="nav-item">
                <a href="<?php echo e(route('admin.users.index')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('admin.users.*') ? 'active' : ''); ?>">
                    <i class="nav-icon bi bi-people"></i>
                    <span>المستخدمون</span>
                </a>
            </li>
        </ul>

        
        <div class="nav-label">البنرات</div>
        <ul>
            <li class="nav-item">
                <a href="<?php echo e(route('admin.banners.index', ['section' => 'home'])); ?>"
                    class="nav-link <?php echo e(request()->routeIs('admin.banners.*') && request('section') !== 'store' ? 'active' : ''); ?>">
                    <i class="nav-icon bi bi-image"></i>
                    <span>بنر الرئيسية</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo e(route('admin.banners.index', ['section' => 'store'])); ?>"
                    class="nav-link <?php echo e(request()->routeIs('admin.banners.*') && request('section') === 'store' ? 'active' : ''); ?>">
                    <i class="nav-icon bi bi-images"></i>
                    <span>بنر المتجر</span>
                </a>
            </li>
        </ul>

        
        <div class="nav-label">المتجر الإلكتروني</div>
        <ul>
            <li class="nav-item">
                <a href="<?php echo e(route('admin.store.categories.index')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('admin.store.categories.*') ? 'active' : ''); ?>">
                    <i class="nav-icon bi bi-grid"></i>
                    <span>التصنيفات</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo e(route('admin.store.products.index')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('admin.store.products.*') ? 'active' : ''); ?>">
                    <i class="nav-icon bi bi-box-seam"></i>
                    <span>المنتجات</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo e(route('admin.delivery-zones.index')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('admin.delivery-zones.*') ? 'active' : ''); ?>">
                    <i class="nav-icon bi bi-geo-alt"></i>
                    <span>مناطق التوصيل</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo e(route('admin.orders.index')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('admin.orders.*') ? 'active' : ''); ?>">
                    <i class="nav-icon bi bi-bag-check"></i>
                    <span>الطلبات</span>
                </a>
            </li>
        </ul>

        
        <div class="nav-label">رعاية صحية منزلية</div>
        <ul>
            
            <li class="nav-item has-submenu <?php echo e(request()->routeIs('admin.nursing.*') ? 'open' : ''); ?>">
                <a href="#" class="nav-link submenu-toggle">
                    <i class="nav-icon bi bi-person-badge"></i>
                    <span>طلب تمريض</span>
                    <i class="bi bi-chevron-down ms-auto submenu-arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="<?php echo e(route('admin.nursing.types')); ?>"
                            class="nav-link <?php echo e(request()->routeIs('admin.nursing.types*') ? 'active' : ''); ?>">
                            <i class="nav-icon bi bi-list-ul"></i>
                            <span>أنواع الخدمة</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.nursing.requests')); ?>"
                            class="nav-link <?php echo e(request()->routeIs('admin.nursing.requests*') ? 'active' : ''); ?>">
                            <i class="nav-icon bi bi-clipboard2-pulse"></i>
                            <span>الطلبات</span>
                        </a>
                    </li>
                </ul>
            </li>

            
            <li class="nav-item has-submenu <?php echo e(request()->routeIs('admin.bathing.*') ? 'open' : ''); ?>">
                <a href="#" class="nav-link submenu-toggle">
                    <i class="nav-icon bi bi-droplet"></i>
                    <span>طلب استحمام</span>
                    <i class="bi bi-chevron-down ms-auto submenu-arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="<?php echo e(route('admin.bathing.pos')); ?>"
                            class="nav-link <?php echo e(request()->routeIs('admin.bathing.pos*') ? 'active' : ''); ?>">
                            <i class="nav-icon bi bi-shop"></i>
                            <span>نقاط البيع</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.bathing.cards')); ?>"
                            class="nav-link <?php echo e(request()->routeIs('admin.bathing.cards*') ? 'active' : ''); ?>">
                            <i class="nav-icon bi bi-credit-card-2-front"></i>
                            <span>البطاقات</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.bathing.requests')); ?>"
                            class="nav-link <?php echo e(request()->routeIs('admin.bathing.requests*') ? 'active' : ''); ?>">
                            <i class="nav-icon bi bi-clipboard2-pulse"></i>
                            <span>الطلبات</span>
                        </a>
                    </li>
                </ul>
            </li>

            
            <li class="nav-item has-submenu <?php echo e(request()->routeIs('admin.care.*') ? 'open' : ''); ?>">
                <a href="#" class="nav-link submenu-toggle">
                    <i class="nav-icon bi bi-heart-pulse"></i>
                    <span>طلب رعاية</span>
                    <i class="bi bi-chevron-down ms-auto submenu-arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="<?php echo e(route('admin.care.services')); ?>"
                            class="nav-link <?php echo e(request()->routeIs('admin.care.services*') ? 'active' : ''); ?>">
                            <i class="nav-icon bi bi-list-ul"></i>
                            <span>الخدمات</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.care.requests')); ?>"
                            class="nav-link <?php echo e(request()->routeIs('admin.care.requests*') ? 'active' : ''); ?>">
                            <i class="nav-icon bi bi-clipboard2-pulse"></i>
                            <span>الطلبات</span>
                        </a>
                    </li>
                </ul>
            </li>

            
            <li class="nav-item has-submenu <?php echo e(request()->routeIs('admin.lab.*') ? 'open' : ''); ?>">
                <a href="#" class="nav-link submenu-toggle">
                    <i class="nav-icon bi bi-eyedropper"></i>
                    <span>المختبر</span>
                    <i class="bi bi-chevron-down ms-auto submenu-arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="<?php echo e(route('admin.lab.categories')); ?>"
                            class="nav-link <?php echo e(request()->routeIs('admin.lab.categories*') ? 'active' : ''); ?>">
                            <i class="nav-icon bi bi-folder2"></i>
                            <span>الفئات</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.lab.tests')); ?>"
                            class="nav-link <?php echo e(request()->routeIs('admin.lab.tests*') ? 'active' : ''); ?>">
                            <i class="nav-icon bi bi-list-ul"></i>
                            <span>الفحوصات</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.lab.requests')); ?>"
                            class="nav-link <?php echo e(request()->routeIs('admin.lab.requests*') ? 'active' : ''); ?>">
                            <i class="nav-icon bi bi-clipboard2-pulse"></i>
                            <span>الطلبات</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.lab.staff.index')); ?>"
                            class="nav-link <?php echo e(request()->routeIs('admin.lab.staff*') ? 'active' : ''); ?>">
                            <i class="nav-icon bi bi-person-badge"></i>
                            <span>حسابات لوحة المختبر</span>
                        </a>
                    </li>
                </ul>
            </li>

            
            <li class="nav-item has-submenu <?php echo e(request()->routeIs('admin.xray.*') ? 'open' : ''); ?>">
                <a href="#" class="nav-link submenu-toggle">
                    <i class="nav-icon bi bi-radioactive"></i>
                    <span>أشعة منزلية</span>
                    <i class="bi bi-chevron-down ms-auto submenu-arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="<?php echo e(route('admin.xray.categories')); ?>"
                            class="nav-link <?php echo e(request()->routeIs('admin.xray.categories*') ? 'active' : ''); ?>">
                            <i class="nav-icon bi bi-folder2"></i>
                            <span>الفئات</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.xray.tests')); ?>"
                            class="nav-link <?php echo e(request()->routeIs('admin.xray.tests*') ? 'active' : ''); ?>">
                            <i class="nav-icon bi bi-list-ul"></i>
                            <span>الأشعة</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.xray.requests')); ?>"
                            class="nav-link <?php echo e(request()->routeIs('admin.xray.requests*') ? 'active' : ''); ?>">
                            <i class="nav-icon bi bi-clipboard2-pulse"></i>
                            <span>الطلبات</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>

        
        <div class="nav-label">خدمات طبية منزلية</div>
        <ul>
            
            <li class="nav-item has-submenu <?php echo e(request()->routeIs('admin.doctors.*') || request()->routeIs('admin.doctor-bookings.*') ? 'open' : ''); ?>">
                <a href="#" class="nav-link submenu-toggle">
                    <i class="nav-icon bi bi-person-vcard"></i>
                    <span>الأطباء</span>
                    <i class="bi bi-chevron-down ms-auto submenu-arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="<?php echo e(route('admin.doctors.index')); ?>"
                            class="nav-link <?php echo e(request()->routeIs('admin.doctors.*') ? 'active' : ''); ?>">
                            <i class="nav-icon bi bi-list-ul"></i>
                            <span>قائمة الأطباء</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.doctor-bookings.index')); ?>"
                            class="nav-link <?php echo e(request()->routeIs('admin.doctor-bookings.*') ? 'active' : ''); ?>">
                            <i class="nav-icon bi bi-calendar-check"></i>
                            <span>الحجوزات</span>
                        </a>
                    </li>
                </ul>
            </li>

            
            <li class="nav-item">
                <a href="<?php echo e(route('admin.nutrition.index')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('admin.nutrition.*') ? 'active' : ''); ?>">
                    <i class="nav-icon bi bi-apple"></i>
                    <span>طلبات التغذية</span>
                </a>
            </li>

            
            <li class="nav-item has-submenu <?php echo e(request()->routeIs('admin.transfers.*') ? 'open' : ''); ?>">
                <a href="#" class="nav-link submenu-toggle">
                    <i class="nav-icon bi bi-truck-front"></i>
                    <span>نقل المرضى</span>
                    <i class="bi bi-chevron-down ms-auto submenu-arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="<?php echo e(route('admin.transfers.index')); ?>"
                            class="nav-link <?php echo e(request()->routeIs('admin.transfers.index') || request()->routeIs('admin.transfers.show') ? 'active' : ''); ?>">
                            <i class="nav-icon bi bi-clipboard2-pulse"></i>
                            <span>الطلبات</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.transfers.note.edit')); ?>"
                            class="nav-link <?php echo e(request()->routeIs('admin.transfers.note*') ? 'active' : ''); ?>">
                            <i class="nav-icon bi bi-sticky"></i>
                            <span>الملاحظة التوضيحية</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>

        
        <div class="nav-label">المحتوى</div>
        <ul>
            <li class="nav-item">
                <a href="<?php echo e(route('admin.articles.index')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('admin.articles.*') ? 'active' : ''); ?>">
                    <i class="nav-icon bi bi-newspaper"></i>
                    <span>المقالات</span>
                </a>
            </li>

            
            <li class="nav-item has-submenu <?php echo e(request()->routeIs('admin.forum.*') ? 'open' : ''); ?>">
                <a href="#" class="nav-link submenu-toggle">
                    <i class="nav-icon bi bi-chat-heart"></i>
                    <span>منتدى الأمهات</span>
                    <i class="bi bi-chevron-down ms-auto submenu-arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="<?php echo e(route('admin.forum.categories')); ?>"
                            class="nav-link <?php echo e(request()->routeIs('admin.forum.categories*') ? 'active' : ''); ?>">
                            <i class="nav-icon bi bi-folder2"></i>
                            <span>الأقسام الرئيسية</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.forum.sub-categories')); ?>"
                            class="nav-link <?php echo e(request()->routeIs('admin.forum.sub-categories*') ? 'active' : ''); ?>">
                            <i class="nav-icon bi bi-folder2-open"></i>
                            <span>الأقسام الفرعية</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.forum.posts')); ?>"
                            class="nav-link <?php echo e(request()->routeIs('admin.forum.posts*') ? 'active' : ''); ?>">
                            <i class="nav-icon bi bi-chat-square-text"></i>
                            <span>المنشورات</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>

        
        <div class="nav-label">صحتي</div>
        <ul>
            
            <li class="nav-item">
                <a href="<?php echo e(route('admin.sihati.rooms.index')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('admin.sihati.rooms.*') ? 'active' : ''); ?>">
                    <i class="nav-icon bi bi-house-heart"></i>
                    <span>الغرف</span>
                </a>
            </li>

            
            <li class="nav-item">
                <a href="<?php echo e(route('admin.sihati.templates.index')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('admin.sihati.templates.*') || request()->routeIs('admin.sihati.fields.*') ? 'active' : ''); ?>">
                    <i class="nav-icon bi bi-journal-medical"></i>
                    <span>قوالب التقارير</span>
                </a>
            </li>

            
            <li class="nav-item has-submenu <?php echo e(request()->routeIs('admin.sihati.documents.*') ? 'open' : ''); ?>">
                <a href="#" class="nav-link submenu-toggle">
                    <i class="nav-icon bi bi-file-earmark-text"></i>
                    <span>الوثائق</span>
                    <i class="bi bi-chevron-down ms-auto submenu-arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="<?php echo e(route('admin.sihati.documents.authorization')); ?>"
                            class="nav-link <?php echo e(request()->routeIs('admin.sihati.documents.authorization') ? 'active' : ''); ?>">
                            <i class="nav-icon bi bi-file-earmark-check"></i>
                            <span>وثيقة التفويض</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.sihati.documents.pledge')); ?>"
                            class="nav-link <?php echo e(request()->routeIs('admin.sihati.documents.pledge') ? 'active' : ''); ?>">
                            <i class="nav-icon bi bi-file-earmark-lock2"></i>
                            <span>وثيقة التعهد</span>
                        </a>
                    </li>
                </ul>
            </li>

            
            <li class="nav-item">
                <a href="<?php echo e(route('admin.sihati.medications.index')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('admin.sihati.medications.*') ? 'active' : ''); ?>">
                    <i class="nav-icon bi bi-capsule"></i>
                    <span>أدوية المرضى</span>
                </a>
            </li>

            
            <li class="nav-item">
                <a href="<?php echo e(route('admin.sihati.diagnoses.index')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('admin.sihati.diagnoses.*') ? 'active' : ''); ?>">
                    <i class="nav-icon bi bi-clipboard2-pulse"></i>
                    <span>التشخيصات</span>
                </a>
            </li>

            
            <li class="nav-item">
                <a href="<?php echo e(route('admin.sihati.chronic-diseases.index')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('admin.sihati.chronic-diseases.*') ? 'active' : ''); ?>">
                    <i class="nav-icon bi bi-heart-pulse"></i>
                    <span>الأمراض المزمنة</span>
                </a>
            </li>

            
            <li class="nav-item">
                <a href="<?php echo e(route('admin.sihati.complaints.index')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('admin.sihati.complaints.*') ? 'active' : ''); ?>">
                    <i class="nav-icon bi bi-chat-left-text"></i>
                    <span>الشكاوى</span>
                </a>
            </li>

            
            <li class="nav-item has-submenu <?php echo e(request()->routeIs('admin.sihati.visit-form*') ? 'open' : ''); ?>">
                <a href="#" class="nav-link submenu-toggle">
                    <i class="nav-icon bi bi-file-earmark-medical"></i>
                    <span>نموذج الزيارة الطبية</span>
                    <i class="bi bi-chevron-down ms-auto submenu-arrow"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="<?php echo e(route('admin.sihati.visit-form-fields.index')); ?>"
                            class="nav-link <?php echo e(request()->routeIs('admin.sihati.visit-form-fields.*') ? 'active' : ''); ?>">
                            <i class="nav-icon bi bi-list-check"></i>
                            <span>الحقول</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.sihati.visit-forms.index')); ?>"
                            class="nav-link <?php echo e(request()->routeIs('admin.sihati.visit-forms.*') ? 'active' : ''); ?>">
                            <i class="nav-icon bi bi-journal-check"></i>
                            <span>النماذج المُرسلة</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>

        
        <div class="nav-label">النظام</div>
        <ul>
            <li class="nav-item">
                <a href="<?php echo e(route('admin.role.index')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('admin.role.*') ? 'active' : ''); ?>">
                    <i class="nav-icon bi bi-shield-check"></i>
                    <span>الأدوار والصلاحيات</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo e(route('admin.employee.index')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('admin.employee.*') ? 'active' : ''); ?>">
                    <i class="nav-icon bi bi-person-gear"></i>
                    <span>المشرفون</span>
                </a>
            </li>
        </ul>

    </nav>

    
    <div class="sidebar-footer">
        <ul>
            <li class="nav-item">
                <a href="<?php echo e(route('admin.login.edit', auth('admin')->id())); ?>" class="nav-link">
                    <i class="nav-icon bi bi-gear"></i>
                    <span>الإعدادات</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link"
                    onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                    <i class="nav-icon bi bi-box-arrow-right"></i>
                    <span>تسجيل الخروج</span>
                </a>
            </li>
        </ul>
        <form id="admin-logout-form" action="<?php echo e(route('admin.logout')); ?>" method="POST" class="d-none">
            <?php echo csrf_field(); ?>
        </form>
        <button class="sidebar-collapse-btn" id="sidebarCollapseBtn" title="طي القائمة">
            <i class="bi bi-arrow-bar-left"></i>
        </button>
    </div>

</aside>
<?php /**PATH C:\xampp\htdocs\green\resources\views/admin/includes/sidebar.blade.php ENDPATH**/ ?>