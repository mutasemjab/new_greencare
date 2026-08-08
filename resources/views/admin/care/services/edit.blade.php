@extends('admin.layouts.app')

@section('title', 'تعديل خدمة الرعاية')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.care.services') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right"></i>
        </a>
        <h4 class="mb-0 fw-bold">تعديل الخدمة: {{ $service->name }}</h4>
    </div>

    @include('admin.includes.alerts.error')

    <div class="card border-0 shadow-sm mx-auto" style="max-width:700px;">
        <div class="card-body p-4">
            <form action="{{ route('admin.care.services.update', $service) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="mb-3">
                    <label class="form-label fw-semibold">الاسم <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $service->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @if($service->icon)
                <div class="mb-3">
                    <label class="form-label fw-semibold">الأيقونة الحالية</label>
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ Storage::disk('public')->url($service->icon) }}" alt="{{ $service->name }}"
                            class="rounded border" style="width:80px;height:80px;object-fit:cover;">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remove_icon" id="remove_icon" value="1">
                            <label class="form-check-label text-danger" for="remove_icon">حذف الأيقونة</label>
                        </div>
                    </div>
                </div>
                @endif

                <div class="mb-3">
                    <label class="form-label fw-semibold">{{ $service->icon ? 'تغيير الأيقونة' : 'الأيقونة / الصورة' }}</label>
                    <input type="file" name="icon" class="form-control @error('icon') is-invalid @enderror"
                        accept="image/*">
                    @error('icon')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">السعر (JD) <span class="text-danger">*</span></label>
                    <input type="number" name="price" step="0.01" min="0"
                        class="form-control @error('price') is-invalid @enderror"
                        value="{{ old('price', $service->price) }}" required>
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">الترتيب</label>
                    <input type="number" name="sort_order" min="0"
                        class="form-control @error('sort_order') is-invalid @enderror"
                        value="{{ old('sort_order', $service->sort_order) }}">
                    @error('sort_order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                            value="1" {{ old('is_active', $service->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_active">مفعّل</label>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i> حفظ التعديلات
                    </button>
                    <a href="{{ route('admin.care.services') }}" class="btn btn-outline-secondary px-4">إلغاء</a>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection
