@extends('admin.layouts.app')

@section('title', 'فئات المختبر')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">فئات المختبر</h4>
        <a href="{{ route('admin.lab.categories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> إضافة فئة
        </a>
    </div>

    @include('admin.includes.alerts.success')

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.lab.categories') }}" class="row g-3">
                <div class="col-md-7">
                    <input type="text" name="search" class="form-control" placeholder="بحث بالاسم..."
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-5 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search me-1"></i> بحث
                    </button>
                    <a href="{{ route('admin.lab.categories') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>الأيقونة</th>
                            <th>الاسم</th>
                            <th>عدد الفحوصات</th>
                            <th>الترتيب</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr>
                            <td class="text-muted small">{{ $loop->iteration }}</td>
                            <td>
                                @if($category->icon)
                                    <img src="{{ Storage::url($category->icon) }}" alt="{{ $category->name }}"
                                        class="rounded" style="width:48px;height:48px;object-fit:cover;">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                        style="width:48px;height:48px;">
                                        <i class="bi bi-image text-secondary"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $category->name }}</td>
                            <td>
                                <span class="badge bg-secondary rounded-pill">{{ $category->tests_count ?? $category->tests->count() }}</span>
                            </td>
                            <td>{{ $category->sort_order }}</td>
                            <td>
                                @if($category->is_active)
                                    <span class="badge bg-success-subtle text-success">مفعّل</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">معطّل</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.lab.categories.edit', $category) }}"
                                        class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.lab.categories.destroy', $category) }}"
                                        method="POST"
                                        onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                لا توجد فئات بعد
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($categories->hasPages())
        <div class="card-footer bg-transparent d-flex justify-content-center">
            {{ $categories->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
