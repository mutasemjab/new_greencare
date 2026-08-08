@extends('admin.layouts.app')
@section('title', 'تعديل بنر')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('admin.banners.index', ['section' => $banner->section]) }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-right"></i>
        </a>
        <h4 class="mb-0 fw-bold">تعديل بنر</h4>
    </div>

    <div class="card shadow-sm border-0" style="max-width:600px">
        <div class="card-body p-4">
            @include('admin.includes.alerts.error')

            <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PATCH')

                <div class="mb-3">
                    <label class="form-label fw-semibold">الصورة الحالية</label>
                    <img src="{{ Storage::disk('public')->url($banner->image) }}" class="d-block rounded mb-2"
                        style="max-height:150px;object-fit:cover;">
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <div class="form-text">اتركه فارغاً للإبقاء على الصورة الحالية</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">العنوان</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $banner->title) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">القسم</label>
                    <select name="section" class="form-select">
                        <option value="home"  {{ old('section', $banner->section) === 'home'  ? 'selected' : '' }}>الرئيسية</option>
                        <option value="store" {{ old('section', $banner->section) === 'store' ? 'selected' : '' }}>المتجر</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">رابط (URL)</label>
                    <input type="url" name="url" class="form-control" value="{{ old('url', $banner->url) }}">
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold">الترتيب</label>
                        <input type="number" name="sort_order" class="form-control"
                            value="{{ old('sort_order', $banner->sort_order) }}" min="0">
                    </div>
                    <div class="col-6 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                id="is_active" {{ old('is_active', $banner->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">نشط</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                    <a href="{{ route('admin.banners.index', ['section' => $banner->section]) }}" class="btn btn-outline-secondary">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
