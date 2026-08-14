@extends('admin.layouts.app')
@section('title', 'الملاحظة التوضيحية لنقل المرضى')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.transfers.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right"></i>
        </a>
        <h4 class="mb-0 fw-bold">الملاحظة التوضيحية لنقل المرضى</h4>
    </div>

    @include('admin.includes.alerts.success')

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="alert alert-info small">
                        <i class="bi bi-info-circle me-1"></i>
                        هذا النص يظهر لليوزر بتطبيق الموبايل بدل حقول اختيار نقطة الانطلاق والوصول بشاشة طلب نقل المريض.
                    </div>

                    <form action="{{ route('admin.transfers.note.update') }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label class="form-label fw-semibold">النص بالعربي <span class="text-danger">*</span></label>
                            <textarea name="name_ar" rows="4"
                                class="form-control @error('name_ar') is-invalid @enderror"
                                placeholder="مثال: يرجى التواصل مع فريق الدعم لتحديد نقطة الانطلاق والوصول">{{ old('name_ar', $note->name_ar) }}</textarea>
                            @error('name_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">النص بالإنجليزي <span class="text-danger">*</span></label>
                            <textarea name="name_en" rows="4"
                                class="form-control @error('name_en') is-invalid @enderror"
                                placeholder="e.g. Please contact support to arrange pickup and drop-off">{{ old('name_en', $note->name_en) }}</textarea>
                            @error('name_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-lg me-1"></i> حفظ
                            </button>
                            <a href="{{ route('admin.transfers.index') }}" class="btn btn-outline-secondary px-4">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
