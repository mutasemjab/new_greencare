@extends('admin.layouts.app')
@section('title', $section === 'store' ? 'بنر المتجر' : 'بنر الرئيسية')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center gap-3">
            <h4 class="mb-0 fw-bold">{{ $section === 'store' ? 'بنر المتجر' : 'بنر الرئيسية' }}</h4>
            <div class="btn-group btn-group-sm">
                <a href="{{ route('admin.banners.index', ['section' => 'home']) }}"
                    class="btn {{ $section === 'home' ? 'btn-primary' : 'btn-outline-primary' }}">الرئيسية</a>
                <a href="{{ route('admin.banners.index', ['section' => 'store']) }}"
                    class="btn {{ $section === 'store' ? 'btn-primary' : 'btn-outline-primary' }}">المتجر</a>
            </div>
        </div>
        <a href="{{ route('admin.banners.create', ['section' => $section]) }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> إضافة بنر
        </a>
    </div>

    @include('admin.includes.alerts.success')

    <div class="row g-3">
        @forelse($banners as $banner)
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <img src="{{ Storage::url($banner->image) }}" class="card-img-top"
                    style="height:180px;object-fit:cover;" alt="{{ $banner->title }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold">{{ $banner->title ?? 'بدون عنوان' }}</div>
                            <div class="text-muted small">ترتيب: {{ $banner->sort_order }}</div>
                        </div>
                        @if($banner->is_active)
                            <span class="badge bg-success">نشط</span>
                        @else
                            <span class="badge bg-danger">مخفي</span>
                        @endif
                    </div>
                </div>
                <div class="card-footer bg-transparent d-flex gap-2">
                    <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-sm btn-outline-primary flex-fill">
                        <i class="bi bi-pencil me-1"></i> تعديل
                    </a>
                    <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST"
                        onsubmit="return confirm('حذف هذا البنر؟')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center text-muted py-5">
            <i class="bi bi-image fs-1 d-block mb-2"></i>
            لا يوجد بنرات لهذا القسم
        </div>
        @endforelse
    </div>
</div>
@endsection
