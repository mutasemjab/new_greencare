@extends('admin.layouts.app')
@section('title', 'إضافة مقال')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('admin.articles.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-right"></i>
        </a>
        <h4 class="mb-0 fw-bold">إضافة مقال جديد</h4>
    </div>

    <div class="card shadow-sm border-0" style="max-width:750px">
        <div class="card-body p-4">
            @include('admin.includes.alerts.error')

            <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">العنوان <span class="text-danger">*</span></label>
                    <input type="text" name="title"
                        class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title') }}" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">المحتوى <span class="text-danger">*</span></label>
                    <textarea name="description" rows="8"
                        class="form-control @error('description') is-invalid @enderror"
                        required>{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">الصورة</label>
                    <input type="file" name="image"
                        class="form-control @error('image') is-invalid @enderror"
                        accept="image/*">
                    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">تاريخ ووقت النشر</label>
                    <input type="datetime-local" name="published_at"
                        class="form-control @error('published_at') is-invalid @enderror"
                        value="{{ old('published_at') }}">
                    @error('published_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                            id="is_active" {{ old('is_active', '1') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_active">منشور</label>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i> حفظ
                    </button>
                    <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary px-4">إلغاء</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
