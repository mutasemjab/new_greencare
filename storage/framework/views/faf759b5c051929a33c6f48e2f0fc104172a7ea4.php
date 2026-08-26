<?php $__env->startSection('title', 'غرفة: ' . $room->name); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">

    
    <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
        <a href="<?php echo e(route('admin.sihati.rooms.index')); ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right"></i>
        </a>
        <div>
            <h4 class="mb-0 fw-bold"><?php echo e($room->name); ?></h4>
            <span class="small text-muted">
                مريض: <?php echo e($room->patient?->name); ?>

                &nbsp;·&nbsp;
                أنشأها: <?php echo e($room->createdBy?->name ?? '—'); ?>

            </span>
        </div>
        <?php if($room->is_active): ?>
            <span class="badge bg-success-subtle text-success">نشطة</span>
        <?php else: ?>
            <span class="badge bg-danger-subtle text-danger">معطلة</span>
        <?php endif; ?>
        <div class="ms-auto d-flex gap-2">
            <a href="<?php echo e(route('admin.sihati.rooms.edit', $room)); ?>" class="btn btn-sm btn-outline-warning">
                <i class="bi bi-pencil me-1"></i> تعديل
            </a>
            <form action="<?php echo e(route('admin.sihati.rooms.toggle', $room)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PATCH'); ?>
                <button class="btn btn-sm <?php echo e($room->is_active ? 'btn-outline-danger' : 'btn-outline-success'); ?>">
                    <i class="bi bi-<?php echo e($room->is_active ? 'pause-circle' : 'play-circle'); ?> me-1"></i>
                    <?php echo e($room->is_active ? 'تعطيل الغرفة' : 'تفعيل الغرفة'); ?>

                </button>
            </form>
            <form action="<?php echo e(route('admin.sihati.rooms.destroy', $room)); ?>" method="POST"
                onsubmit="return confirm('هل أنت متأكد من حذف هذه الغرفة؟ لا يمكن التراجع عن هذا الإجراء.')">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-trash me-1"></i> حذف
                </button>
            </form>
        </div>
    </div>

    <?php echo $__env->make('admin.includes.alerts.success', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php if($errors->any()): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <ul class="mb-0">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    
    <ul class="nav nav-tabs mb-4" id="roomTabs">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#tab-info">
                <i class="bi bi-info-circle me-1"></i>المعلومات
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tab-members">
                <i class="bi bi-people me-1"></i>الأعضاء
                <span class="badge bg-secondary ms-1"><?php echo e($members->count()); ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tab-templates">
                <i class="bi bi-journal-medical me-1"></i>القوالب
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tab-reports">
                <i class="bi bi-clipboard2-pulse me-1"></i>التقارير
                <span class="badge bg-secondary ms-1"><?php echo e($reports->count()); ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tab-orders">
                <i class="bi bi-chat-left-text me-1"></i>أوامر الدكتور
                <span class="badge bg-secondary ms-1"><?php echo e($doctorOrders->count()); ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tab-medications">
                <i class="bi bi-capsule me-1"></i>الأدوية
                <span class="badge bg-secondary ms-1"><?php echo e($medications->count()); ?></span>
            </a>
        </li>
    </ul>

    <div class="tab-content">

        
        <div class="tab-pane fade show active" id="tab-info">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent fw-bold">
                            <i class="bi bi-house me-2"></i>بيانات الغرفة
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">المريض</span>
                                    <span class="fw-semibold"><?php echo e($room->patient?->name ?? '—'); ?></span>
                                </li>
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">كود المريض</span>
                                    <span class="fw-semibold text-primary"><?php echo e($room->patient_code ?? '—'); ?></span>
                                </li>
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">الرعاية الصحية أنشأها</span>
                                    <span class="fw-semibold"><?php echo e($room->createdBy?->name ?? '—'); ?></span>
                                </li>
                                <?php if($room->address): ?>
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">العنوان</span>
                                    <span class="fw-semibold"><?php echo e($room->address); ?></span>
                                </li>
                                <?php endif; ?>
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">نسبة الخصم</span>
                                    <span class="fw-semibold">
                                        <?php if($room->discount_value > 0): ?>
                                            <span class="badge bg-warning-subtle text-warning fs-6"><?php echo e($room->discount_value); ?>%</span>
                                        <?php else: ?>
                                            لا يوجد خصم
                                        <?php endif; ?>
                                    </span>
                                </li>
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">قالب التسجيل</span>
                                    <span class="fw-semibold"><?php echo e($room->registrationTemplate?->name ?? '—'); ?></span>
                                </li>
                                <li class="d-flex justify-content-between py-2">
                                    <span class="text-muted">تاريخ الإنشاء</span>
                                    <span><?php echo e($room->created_at->format('Y/m/d')); ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent fw-bold">
                            <i class="bi bi-clipboard-check me-2"></i>تقرير التسجيل
                        </div>
                        <div class="card-body">
                            <?php if($registrationReport): ?>
                                <div class="small text-muted mb-3">
                                    قدّمه: <?php echo e($registrationReport->submittedBy?->name); ?>

                                    — <?php echo e($registrationReport->submitted_at?->format('Y/m/d H:i')); ?>

                                </div>
                                <ul class="list-unstyled mb-0">
                                    <?php $__currentLoopData = $registrationReport->answers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $answer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="py-2 border-bottom">
                                        <div class="text-muted small"><?php echo e($answer->field_question); ?></div>
                                        <div class="fw-semibold"><?php echo e($answer->display_answer); ?></div>
                                    </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            <?php else: ?>
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-clipboard fs-3 d-block mb-2"></i>
                                    لم يُقدَّم تقرير التسجيل بعد
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="row g-4 mt-1">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent fw-bold">
                            <i class="bi bi-person-vcard me-2"></i>بيانات المريض (نموذج الاستقبال)
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">العمر</span>
                                    <span class="fw-semibold"><?php echo e($room->age ?? '—'); ?></span>
                                </li>
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">الجنس</span>
                                    <span class="fw-semibold"><?php echo e($room->gender === 'male' ? 'ذكر' : ($room->gender === 'female' ? 'أنثى' : '—')); ?></span>
                                </li>
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">الوزن</span>
                                    <span class="fw-semibold"><?php echo e($room->weight ? $room->weight . ' كغ' : '—'); ?></span>
                                </li>
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">فصيلة الدم</span>
                                    <span class="fw-semibold"><?php echo e($room->blood_group ?? '—'); ?></span>
                                </li>
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">الحالة الاجتماعية</span>
                                    <span class="fw-semibold">
                                        <?php
                                            $maritalMap = ['single'=>'أعزب','married'=>'متزوج','divorced'=>'مطلق','widowed'=>'أرمل'];
                                        ?>
                                        <?php echo e($maritalMap[$room->marital_status] ?? '—'); ?>

                                    </span>
                                </li>
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">الحالة الوظيفية</span>
                                    <span class="fw-semibold">
                                        <?php
                                            $funcMap = ['independent'=>'مستقل','partially_dependent'=>'معتمد جزئياً','fully_dependent'=>'معتمد كلياً'];
                                        ?>
                                        <?php echo e($funcMap[$room->functional_status] ?? '—'); ?>

                                    </span>
                                </li>
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">العرق</span>
                                    <span class="fw-semibold"><?php echo e($room->race === 'white' ? 'أبيض' : ($room->race === 'black' ? 'أسود' : '—')); ?></span>
                                </li>
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">المستوى التعليمي</span>
                                    <span class="fw-semibold"><?php echo e($room->education_level ?? '—'); ?></span>
                                </li>
                                <li class="d-flex justify-content-between py-2">
                                    <span class="text-muted">الحساسية</span>
                                    <span class="fw-semibold">
                                        <?php if($room->has_allergies): ?>
                                            <span class="badge bg-warning-subtle text-warning"><?php echo e($room->allergy_details ?: 'يوجد'); ?></span>
                                        <?php else: ?>
                                            لا يوجد
                                        <?php endif; ?>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent fw-bold">
                            <i class="bi bi-clipboard2-pulse me-2"></i>التشخيصات والأمراض المزمنة والمرفقات
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="text-muted small mb-1">التشخيصات</div>
                                <?php $__empty_1 = true; $__currentLoopData = $room->diagnoses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $diagnosis): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <span class="badge bg-info-subtle text-info me-1 mb-1"><?php echo e($diagnosis->name); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <span class="text-muted small">لا يوجد</span>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <div class="text-muted small mb-1">الأمراض المزمنة</div>
                                <?php $__empty_1 = true; $__currentLoopData = $room->chronicDiseases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chronicDisease): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <span class="badge bg-secondary-subtle text-secondary me-1 mb-1"><?php echo e($chronicDisease->name); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <span class="text-muted small">لا يوجد</span>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="text-muted small mb-1">المرفقات</div>
                                <?php $__empty_1 = true; $__currentLoopData = $room->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <a href="<?php echo e($attachment->url); ?>" target="_blank" class="btn btn-sm btn-outline-secondary me-1 mb-1">
                                        <i class="bi bi-file-earmark me-1"></i> <?php echo e($attachment->original_name ?? 'مرفق ' . $loop->iteration); ?>

                                    </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <span class="text-muted small">لا يوجد</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="tab-pane fade" id="tab-members">
            <div class="row g-4">
                
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent fw-bold">
                            <i class="bi bi-people me-2"></i>أعضاء الغرفة
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>الاسم</th>
                                        <th>الهاتف</th>
                                        <th>الدور</th>
                                        <th class="text-end">إزالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php
                                        $roleColors = ['doctor' => 'primary', 'nurse' => 'info', 'patient_family' => 'secondary'];
                                        $rc = $roleColors[$member->role] ?? 'light';
                                    ?>
                                    <tr>
                                        <td class="text-muted small"><?php echo e($loop->iteration); ?></td>
                                        <td class="fw-semibold"><?php echo e($member->user?->name ?? '—'); ?></td>
                                        <td class="small text-muted"><?php echo e($member->user?->phone ?? '—'); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo e($rc); ?>-subtle text-<?php echo e($rc); ?>">
                                                <?php echo e($member->role_label); ?>

                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <form action="<?php echo e(route('admin.sihati.rooms.members.remove', [$room, $member])); ?>"
                                                method="POST"
                                                onsubmit="return confirm('إزالة <?php echo e($member->user?->name); ?> من الغرفة؟')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-person-dash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">لا يوجد أعضاء بعد</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent fw-bold">
                            <i class="bi bi-person-plus me-2"></i>إضافة عضو
                        </div>
                        <div class="card-body">
                            <form action="<?php echo e(route('admin.sihati.rooms.members.add', $room)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">رقم الهاتف <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" value="<?php echo e(old('phone')); ?>"
                                        class="form-control form-control-sm <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        placeholder="07xxxxxxxx">
                                    <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">الدور <span class="text-danger">*</span></label>
                                    <select name="role" class="form-select form-select-sm <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <option value="doctor"         <?php echo e(old('role') === 'doctor'         ? 'selected' : ''); ?>>طبيب</option>
                                        <option value="nurse"          <?php echo e(old('role') === 'nurse'          ? 'selected' : ''); ?>>ممرض</option>
                                        <option value="patient_family" <?php echo e(old('role') === 'patient_family' ? 'selected' : ''); ?>>عيلة المريض</option>
                                    </select>
                                    <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                    <i class="bi bi-person-plus me-1"></i> إضافة
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="tab-pane fade" id="tab-templates">
            <div class="row g-4">

                
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent fw-bold">
                            <i class="bi bi-check2-circle me-2 text-success"></i>القوالب النشطة الحالية
                        </div>
                        <div class="card-body p-0">
                            <?php $__empty_1 = true; $__currentLoopData = $activeAssignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="p-3 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-<?php echo e($assignment->template?->type_color); ?>-subtle text-<?php echo e($assignment->template?->type_color); ?>">
                                        <?php echo e($assignment->template?->type_label); ?>

                                    </span>
                                    <span class="fw-semibold"><?php echo e($assignment->template?->name); ?></span>
                                </div>
                                <div class="small text-muted mt-1">
                                    عُيِّن في <?php echo e($assignment->assigned_at?->format('Y/m/d H:i')); ?>

                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-journal-x fs-3 d-block mb-2"></i>
                                لا يوجد قالب نشط مُعيَّن
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-header bg-transparent fw-bold">
                            <i class="bi bi-arrow-repeat me-2"></i>تعيين قالب
                        </div>
                        <div class="card-body">
                            <form action="<?php echo e(route('admin.sihati.rooms.assign-template', $room)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">اختر قالباً</label>
                                    <select name="report_template_id"
                                        class="form-select form-select-sm <?php $__errorArgs = ['report_template_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <option value="">— اختر —</option>
                                        <?php $__currentLoopData = $availableTemplates->where('template_type', 'nurse'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($t->id); ?>">تمريض: <?php echo e($t->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php $__currentLoopData = $availableTemplates->where('template_type', 'doctor'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($t->id); ?>">دكتور: <?php echo e($t->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['report_template_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="alert alert-warning py-2 small">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    تعيين قالب جديد سيؤرشف القالب الحالي من نفس النوع ويبدأ بالجديد.
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                    <i class="bi bi-journal-plus me-1"></i> تعيين القالب
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent fw-bold">
                            <i class="bi bi-clock-history me-2"></i>سجل القوالب السابقة
                        </div>
                        <div class="card-body p-0">
                            <?php $__empty_1 = true; $__currentLoopData = $assignmentHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="p-3 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-light text-secondary border">
                                        <?php echo e($history->template?->type_label); ?>

                                    </span>
                                    <span class="text-muted"><?php echo e($history->template?->name); ?></span>
                                </div>
                                <div class="small text-muted mt-1">
                                    من <?php echo e($history->assigned_at?->format('Y/m/d')); ?>

                                    إلى <?php echo e($history->unassigned_at?->format('Y/m/d')); ?>

                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="text-center text-muted py-4">لا يوجد سجل سابق</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        
        <div class="tab-pane fade" id="tab-reports">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-clipboard2-pulse me-2"></i>تقارير الغرفة
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>النوع</th>
                                <th>قدّمه</th>
                                <th>تاريخ التقديم</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="text-muted small"><?php echo e($loop->iteration); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo e($report->report_type_color); ?>-subtle text-<?php echo e($report->report_type_color); ?>">
                                        <?php echo e($report->report_type_label); ?>

                                    </span>
                                </td>
                                <td><?php echo e($report->submittedBy?->name ?? '—'); ?></td>
                                <td class="small"><?php echo e($report->submitted_at?->format('Y/m/d H:i') ?? '—'); ?></td>
                                <td class="text-end">
                                    <a href="<?php echo e(route('admin.sihati.rooms.reports.show', [$room, $report])); ?>"
                                       class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="bi bi-eye"></i> عرض
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="bi bi-clipboard2 fs-3 d-block mb-2"></i>
                                    لا توجد تقارير بعد
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        
        <div class="tab-pane fade" id="tab-orders">
            <div class="d-flex flex-column gap-3">
                <?php $__empty_1 = true; $__currentLoopData = $doctorOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div>
                                <span class="fw-bold text-primary">د. <?php echo e($order->doctor?->name ?? '—'); ?></span>
                                <span class="text-muted small ms-2"><?php echo e($order->created_at->diffForHumans()); ?></span>
                            </div>
                            <?php if($order->is_executed): ?>
                                <span class="badge bg-success-subtle text-success">
                                    <i class="bi bi-check2 me-1"></i>مُنفَّذ
                                </span>
                            <?php else: ?>
                                <span class="badge bg-warning-subtle text-warning">بانتظار التنفيذ</span>
                            <?php endif; ?>
                        </div>
                        <p class="mb-2"><?php echo e($order->order_text); ?></p>

                        
                        <?php if($order->replies->count()): ?>
                        <div class="bg-light rounded p-3 mt-2">
                            <?php $__currentLoopData = $order->replies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reply): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="d-flex gap-2 mb-2">
                                <i class="bi bi-reply text-muted mt-1"></i>
                                <div>
                                    <span class="fw-semibold small"><?php echo e($reply->nurse?->name ?? '—'); ?></span>
                                    <span class="text-muted small ms-1"><?php echo e($reply->created_at->format('Y/m/d H:i')); ?></span>
                                    <div><?php echo e($reply->reply_text); ?></div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center text-muted py-5">
                        <i class="bi bi-chat-left-text fs-3 d-block mb-2"></i>
                        لا توجد أوامر طبية بعد
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="tab-pane fade" id="tab-medications">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-capsule me-2"></i>أدوية الغرفة
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>الدواء</th>
                                <th>الجرعة</th>
                                <th>التكرار</th>
                                <th>من</th>
                                <th>حتى</th>
                                <th>أضافه</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $medications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $med): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="text-muted small"><?php echo e($loop->iteration); ?></td>
                                <td class="fw-semibold"><?php echo e($med->medication_name); ?></td>
                                <td><?php echo e($med->dosage ?? '—'); ?></td>
                                <td><?php echo e($med->frequency ?? '—'); ?></td>
                                <td class="small"><?php echo e($med->start_date?->format('Y/m/d') ?? '—'); ?></td>
                                <td class="small"><?php echo e($med->end_date?->format('Y/m/d') ?? '—'); ?></td>
                                <td class="small text-muted"><?php echo e($med->addedBy?->name ?? '—'); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="bi bi-capsule fs-3 d-block mb-2"></i>
                                    لا توجد أدوية مُسجَّلة
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\green\resources\views/admin/sihati/rooms/show.blade.php ENDPATH**/ ?>