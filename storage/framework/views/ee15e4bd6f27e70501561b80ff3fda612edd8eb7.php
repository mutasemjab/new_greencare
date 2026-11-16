<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سند قبض رقم <?php echo e($receipt->number); ?> — Green Care</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root { --brand: #1a9a3c; --brand-dark: #0f7a2c; --ink: #1f2933; --muted: #6b7280; --line: #d9e4dc; }
        body { font-family: 'Cairo', Tahoma, sans-serif; background: #eef2ef; color: var(--ink); }
        .toolbar { max-width: 210mm; margin: 1rem auto 0; }

        .voucher {
            width: 210mm; min-height: 148mm; margin: 1rem auto 2rem; background: #fff;
            padding: 10mm 12mm; position: relative; box-shadow: 0 0 24px rgba(0,0,0,.08);
            border-top: 6px solid var(--brand);
        }
        .voucher-head { display: flex; align-items: center; justify-content: space-between; gap: 1rem;
            padding-bottom: 5mm; border-bottom: 1px solid var(--line); }
        .voucher-head img { height: 30mm; width: auto; }
        .voucher-title { text-align: center; flex: 1; }
        .voucher-title h1 { font-size: 26px; font-weight: 800; color: var(--brand-dark); margin: 0; }
        .voucher-title .en { font-size: 13px; letter-spacing: 3px; color: var(--muted); text-transform: uppercase; }
        .voucher-meta { min-width: 48mm; font-size: 14px; }
        .voucher-meta div { display: flex; justify-content: space-between; gap: .75rem; padding: 2px 0; }
        .voucher-meta .label { color: var(--muted); }
        .voucher-meta .no { color: #c0392b; font-weight: 800; font-size: 17px; letter-spacing: 1px; }

        .amount-box { display: inline-flex; align-items: center; gap: .6rem; border: 2px solid var(--brand);
            border-radius: 8px; padding: 6px 16px; font-size: 22px; font-weight: 800; color: var(--brand-dark); }
        .amount-box small { font-size: 13px; color: var(--muted); font-weight: 600; }

        .field { display: flex; align-items: baseline; gap: .75rem; margin-top: 6mm; font-size: 16px; }
        .field .label { white-space: nowrap; font-weight: 700; }
        .field .value { flex: 1; border-bottom: 1px dotted #9aa5a0; padding: 0 .5rem 2px; min-height: 1.6em; }

        .signatures { display: flex; justify-content: space-between; margin-top: 16mm; font-size: 15px; }
        .signatures div { width: 60mm; text-align: center; border-top: 1px solid var(--ink); padding-top: 4px; font-weight: 700; }

        .watermark { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
            pointer-events: none; }
        .watermark img { width: 75mm; opacity: .05; }
        .footer-note { position: absolute; bottom: 5mm; left: 12mm; right: 12mm; text-align: center;
            font-size: 11px; color: var(--muted); }

        @page { size: A5 landscape; margin: 0; }
        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            .voucher { margin: 0; box-shadow: none; width: 100%; min-height: 100vh; }
            * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
        @media (max-width: 860px) {
            .voucher { width: auto; margin: 1rem; padding: 6mm; }
            .voucher-head { flex-wrap: wrap; }
        }
    </style>
</head>
<body>

    <div class="toolbar no-print d-flex justify-content-between flex-wrap gap-2 px-3">
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('admin.receipts.index')); ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-1"></i> سندات القبض
            </a>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('receipt-edit')): ?>
            <a href="<?php echo e(route('admin.receipts.edit', $receipt)); ?>" class="btn btn-outline-warning">
                <i class="bi bi-pencil me-1"></i> تعديل
            </a>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('receipt-add')): ?>
            <a href="<?php echo e(route('admin.receipts.create')); ?>" class="btn btn-outline-primary">
                <i class="bi bi-plus-lg me-1"></i> سند جديد
            </a>
            <?php endif; ?>
        </div>
        <button onclick="window.print()" class="btn btn-success">
            <i class="bi bi-printer me-1"></i> طباعة / حفظ PDF
        </button>
    </div>

    <?php if(session('success')): ?>
    <div class="toolbar no-print px-3 mt-2">
        <div class="alert alert-success mb-0"><?php echo e(session('success')); ?></div>
    </div>
    <?php endif; ?>

    <div class="voucher">
        <div class="watermark"><img src="<?php echo e(asset('assets/admin/images/logo.png')); ?>" alt=""></div>

        <div class="voucher-head">
            <img src="<?php echo e(asset('assets/admin/images/logo.png')); ?>" alt="Green Care">
            <div class="voucher-title">
                <h1>سند قبض</h1>
                <div class="en">Receipt Voucher</div>
            </div>
            <div class="voucher-meta">
                <div><span class="label">رقم السند</span><span class="no"><?php echo e($receipt->number); ?></span></div>
                <div><span class="label">التاريخ</span><span class="fw-bold"><?php echo e($receipt->receipt_date->format('Y/m/d')); ?></span></div>
            </div>
        </div>

        <div class="mt-4">
            <span class="amount-box">
                <?php echo e(number_format($receipt->amount, 3)); ?>

                <small>دينار أردني</small>
            </span>
        </div>

        <div class="field">
            <span class="label">استلمنا من السيد/ة:</span>
            <span class="value"><?php echo e($receipt->person_name); ?></span>
        </div>

        <div class="field">
            <span class="label">مبلغ وقدره:</span>
            <span class="value"><?php echo e($receipt->amount_in_words); ?></span>
        </div>

        <div class="field">
            <span class="label">وذلك عن:</span>
            <span class="value"><?php echo e($receipt->description); ?></span>
        </div>

        <div class="field">
            <span class="label">طريقة الدفع:</span>
            <span class="value"><?php echo e($receipt->payment_method_label); ?></span>
        </div>

        <div class="signatures">
            <div>توقيع المستلم</div>
            <div>المحاسب<?php echo e($receipt->createdBy ? ': ' . $receipt->createdBy->name : ''); ?></div>
        </div>

        <div class="footer-note">Green Care — Health care · greencare-jo.com</div>
    </div>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\green\resources\views/admin/receipts/print.blade.php ENDPATH**/ ?>