@extends('admin.layouts.app')
@section('title', 'إضافة قسم منتدى')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('admin.forum.categories.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-right"></i>
        </a>
        <h4 class="mb-0 fw-bold">إضافة قسم جديد</h4>
    </div>

    <div class="card shadow-sm border-0" style="max-width:750px">
        <div class="card-body p-4">
            @include('admin.includes.alerts.error')

            <form action="{{ route('admin.forum.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">اسم القسم <span class="text-danger">*</span></label>
                    <input type="text" name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">الأيقونة (صورة)</label>
                    <input type="file" name="icon"
                        class="form-control @error('icon') is-invalid @enderror"
                        accept="image/*">
                    @error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">الترتيب</label>
                    <input type="number" name="sort_order" min="0"
                        class="form-control @error('sort_order') is-invalid @enderror"
                        value="{{ old('sort_order', 0) }}">
                    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                            id="is_active" {{ old('is_active', '1') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_active">نشط</label>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i> حفظ
                    </button>
                    <a href="{{ route('admin.forum.categories.index') }}" class="btn btn-outline-secondary px-4">إلغاء</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
