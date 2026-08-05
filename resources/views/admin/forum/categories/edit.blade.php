@extends('admin.layouts.app')
@section('title', 'تعديل قسم المنتدى')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('admin.forum.categories.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-right"></i>
        </a>
        <h4 class="mb-0 fw-bold">تعديل القسم: {{ $category->name }}</h4>
    </div>

    <div class="card shadow-sm border-0" style="max-width:750px">
        <div class="card-body p-4">
            @include('admin.includes.alerts.error')

            <form action="{{ route('admin.forum.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="mb-3">
                    <label class="form-label fw-semibold">اسم القسم <span class="text-danger">*</span></label>
                    <input type="text" name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $category->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Current Icon --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">الأيقونة الحالية</label>
                    @if($category->icon)
                        <div class="mb-2">
                            <img src="{{ Storage::url($category->icon) }}"
                                class="rounded border"
                                style="width:60px;height:60px;object-fit:cover;">
                        </div>
                    @else
                        <p class="text-muted small mb-2">لا توجد أيقونة</p>
                    @endif
                    <label class="form-label fw-semibold">استبدال الأيقونة</label>
                    <input type="file" name="icon"
                        class="form-control @error('icon') is-invalid @enderror"
                        accept="image/*">
                    @error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">الترتيب</label>
                    <input type="number" name="sort_order" min="0"
                        class="form-control @error('sort_order') is-invalid @enderror"
                        value="{{ old('sort_order', $category->sort_order) }}">
                    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                            id="is_active" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_active">نشط</label>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i> حفظ التعديلات
                    </button>
                    <a href="{{ route('admin.forum.categories.index') }}" class="btn btn-outline-secondary px-4">إلغاء</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
