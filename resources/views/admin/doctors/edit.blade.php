@extends('admin.layouts.app')
@section('title', 'تعديل الطبيب')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('admin.doctors.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-right"></i>
        </a>
        <h4 class="mb-0 fw-bold">تعديل الطبيب: {{ $doctor->name }}</h4>
    </div>

    <div class="card shadow-sm border-0" style="max-width:750px">
        <div class="card-body p-4">
            @include('admin.includes.alerts.error')

            <form action="{{ route('admin.doctors.update', $doctor) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">الاسم <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $doctor->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">التخصص <span class="text-danger">*</span></label>
                        <input type="text" name="specialty"
                            class="form-control @error('specialty') is-invalid @enderror"
                            value="{{ old('specialty', $doctor->specialty) }}" required>
                        @error('specialty')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Current Photo --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">الصورة الحالية</label>
                        @if($doctor->photo)
                            <div class="mb-2">
                                <img src="{{ Storage::url($doctor->photo) }}"
                                    class="rounded border"
                                    style="width:100px;height:100px;object-fit:cover;">
                            </div>
                        @else
                            <p class="text-muted small mb-2">لا توجد صورة</p>
                        @endif
                        <label class="form-label fw-semibold">استبدال الصورة</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                        @error('photo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">سعر الزيارة المنزلية (د.أ) <span class="text-danger">*</span></label>
                        <input type="number" name="home_visit_price" step="0.01" min="0"
                            class="form-control @error('home_visit_price') is-invalid @enderror"
                            value="{{ old('home_visit_price', $doctor->home_visit_price) }}" required>
                        @error('home_visit_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">سعر موعد العيادة (د.أ) <span class="text-danger">*</span></label>
                        <input type="number" name="appointment_price" step="0.01" min="0"
                            class="form-control @error('appointment_price') is-invalid @enderror"
                            value="{{ old('appointment_price', $doctor->appointment_price) }}" required>
                        @error('appointment_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">التقييم (0-5) <span class="text-danger">*</span></label>
                        <input type="number" name="rating" step="0.1" min="0" max="5"
                            class="form-control @error('rating') is-invalid @enderror"
                            value="{{ old('rating', $doctor->rating) }}" required>
                        @error('rating')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">سنوات الخبرة <span class="text-danger">*</span></label>
                        <input type="number" name="years_experience" min="0"
                            class="form-control @error('years_experience') is-invalid @enderror"
                            value="{{ old('years_experience', $doctor->years_experience) }}" required>
                        @error('years_experience')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">رقم الحجز</label>
                        <input type="text" name="booking_phone" class="form-control"
                            value="{{ old('booking_phone', $doctor->booking_phone) }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">وصف الطبيب</label>
                        <textarea name="description" class="form-control" rows="4">{{ old('description', $doctor->description) }}</textarea>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">الترتيب</label>
                        <input type="number" name="sort_order" min="0" class="form-control"
                            value="{{ old('sort_order', $doctor->sort_order) }}">
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                id="is_active" {{ old('is_active', $doctor->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">نشط</label>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i> حفظ التعديلات
                    </button>
                    <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline-secondary px-4">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
