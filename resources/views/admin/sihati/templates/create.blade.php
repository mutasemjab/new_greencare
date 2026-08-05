@extends('admin.layouts.app')
@section('title', 'إنشاء قالب تقرير')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.sihati.templates.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right"></i>
        </a>
        <h4 class="mb-0 fw-bold">إنشاء قالب تقرير</h4>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('admin.sihati.templates.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">اسم القالب <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="مثال: تقرير الممرض الأسبوعي">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">نوع القالب <span class="text-danger">*</span></label>
                            <select name="template_type" class="form-select @error('template_type') is-invalid @enderror">
                                <option value="">اختر النوع</option>
                                <option value="registration" {{ old('template_type') === 'registration' ? 'selected' : '' }}>
                                    قالب التسجيل (مرة واحدة عند إنشاء الغرفة)
                                </option>
                                <option value="nurse" {{ old('template_type') === 'nurse' ? 'selected' : '' }}>
                                    تقرير الممرض (كل ساعة)
                                </option>
                                <option value="doctor" {{ old('template_type') === 'doctor' ? 'selected' : '' }}>
                                    تقرير الدكتور (كل شهر)
                                </option>
                            </select>
                            @error('template_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">الوصف</label>
                            <textarea name="description" rows="3"
                                class="form-control @error('description') is-invalid @enderror"
                                placeholder="وصف اختياري للقالب">{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive"
                                value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActive">تفعيل القالب</label>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-lg me-1"></i> إنشاء القالب
                            </button>
                            <a href="{{ route('admin.sihati.templates.index') }}" class="btn btn-outline-secondary">
                                إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
