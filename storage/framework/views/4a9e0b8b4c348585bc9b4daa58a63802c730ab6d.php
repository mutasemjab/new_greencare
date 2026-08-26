<?php $dir = 'rtl'; ?>
<!DOCTYPE html>
<html lang="ar" dir="<?php echo e($dir); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($report->report_type_label); ?> — <?php echo e($room->name); ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body { font-family: 'Inter', Tahoma, sans-serif; background: #f4f6f8; }
        .sheet { max-width: 860px; margin: 2rem auto; background: #fff; border-radius: .5rem; box-shadow: 0 0 20px rgba(0,0,0,.06); }
        .sheet-header { border-bottom: 2px solid #eee; padding: 1.5rem; }
        .meta-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: .75rem 1.5rem; }
        .meta-item .label { font-size: .78rem; color: #888; }
        .meta-item .value { font-weight: 600; }
        .answer-img { max-width: 220px; max-height: 220px; border-radius: .375rem; border: 1px solid #ddd; }
        .toolbar { max-width: 860px; margin: 1rem auto 0; }

        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            .sheet { box-shadow: none; margin: 0; max-width: 100%; border-radius: 0; }
        }
    </style>
</head>
<body>

    <div class="toolbar no-print d-flex justify-content-between">
        <a href="<?php echo e(route('admin.sihati.rooms.show', $room)); ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-right me-1"></i> رجوع للغرفة
        </a>
        <button onclick="window.print()" class="btn btn-primary">
            <i class="bi bi-printer me-1"></i> طباعة / حفظ PDF
        </button>
    </div>

    <div class="sheet">
        <div class="sheet-header">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h4 class="mb-0"><?php echo e($report->report_type_label); ?></h4>
                    <div class="text-muted small"><?php echo e($room->name); ?></div>
                </div>
                <span class="badge bg-<?php echo e($report->report_type_color); ?>-subtle text-<?php echo e($report->report_type_color); ?> fs-6">
                    <?php echo e($report->report_type_label); ?>

                </span>
            </div>

            <div class="meta-grid">
                <div class="meta-item">
                    <div class="label">المريض</div>
                    <div class="value"><?php echo e($room->patient?->name ?? '—'); ?></div>
                </div>
                <div class="meta-item">
                    <div class="label">كود المريض</div>
                    <div class="value"><?php echo e($room->patient_code ?? '—'); ?></div>
                </div>
                <div class="meta-item">
                    <div class="label">قدّمه</div>
                    <div class="value"><?php echo e($report->submittedBy?->name ?? '—'); ?></div>
                </div>
                <div class="meta-item">
                    <div class="label">تاريخ التقديم</div>
                    <div class="value"><?php echo e($report->submitted_at?->format('Y/m/d H:i') ?? '—'); ?></div>
                </div>
                <?php if($report->report_hour): ?>
                <div class="meta-item">
                    <div class="label">الساعة</div>
                    <div class="value"><?php echo e($report->report_hour); ?></div>
                </div>
                <?php endif; ?>
                <?php if($report->report_month): ?>
                <div class="meta-item">
                    <div class="label">الشهر</div>
                    <div class="value"><?php echo e($report->report_month); ?></div>
                </div>
                <?php endif; ?>
            </div>

            <?php if($report->note): ?>
            <div class="mt-3 p-2 bg-light rounded small">
                <i class="bi bi-sticky me-1"></i> <?php echo e($report->note); ?>

            </div>
            <?php endif; ?>
        </div>

        <div class="p-4">
            <?php $__empty_1 = true; $__currentLoopData = $report->answers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $answer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="mb-3 pb-3 border-bottom">
                    <div class="fw-semibold mb-1"><?php echo e($answer->field_question); ?></div>

                    <?php if($answer->field_answer_type === 'image' && $answer->answer_image_url): ?>
                        <a href="<?php echo e($answer->answer_image_url); ?>" target="_blank">
                            <img src="<?php echo e($answer->answer_image_url); ?>" class="answer-img" alt="مرفق">
                        </a>
                    <?php else: ?>
                        <div class="text-muted"><?php echo e($answer->display_answer); ?></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center text-muted py-5">
                    <i class="bi bi-clipboard2 fs-3 d-block mb-2"></i>
                    لا توجد إجابات مسجّلة لهذا التقرير
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\green\resources\views/admin/sihati/rooms/report.blade.php ENDPATH**/ ?>