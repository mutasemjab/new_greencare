@extends('admin.layouts.app')

@section('title', 'إضافة فحص مختبر')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.lab.tests') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right"></i>
        </a>
        <h4 class="mb-0 fw-bold">إضافة فحص مختبر</h4>
    </div>

    @include('admin.includes.alerts.error')

    <div class="card border-0 shadow-sm mx-auto" style="max-width:700px;">
        <div class="card-body p-4">
            <form action="{{ route('admin.lab.tests.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">الفئة <span class="text-danger">*</span></label>
                    <select name="lab_category_id" class="form-select @error('lab_category_id') is-invalid @enderror" required>
                        <option value="">-- اختر الفئة --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(old('lab_category_id') == $cat->id)>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('lab_category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">الاسم <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">الوصف</label>
                    <textarea name="description" rows="3"
                        class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">السعر (JD) <span class="text-danger">*</span></label>
                    <input type="number" name="price" step="0.01" min="0"
                        class="form-control @error('price') is-invalid @enderror"
                        value="{{ old('price') }}" required>
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                            value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_active">مفعّل</label>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i> حفظ
                    </button>
                    <a href="{{ route('admin.lab.tests') }}" class="btn btn-outline-secondary px-4">إلغاء</a>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection
