@extends('admin.layouts.app')
@section('title', 'أقسام المنتدى')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">أقسام المنتدى</h4>
        <a href="{{ route('admin.forum.categories.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> إضافة قسم
        </a>
    </div>

    @include('admin.includes.alerts.success')

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>الأيقونة</th>
                        <th>الاسم</th>
                        <th>الأقسام الفرعية</th>
                        <th>الترتيب</th>
                        <th>الحالة</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td class="text-muted small">{{ $loop->iteration }}</td>
                        <td>
                            @if($category->icon)
                                <img src="{{ Storage::disk('public')->url($category->icon) }}"
                                    class="rounded"
                                    style="width:40px;height:40px;object-fit:cover;">
                            @else
                                <div class="rounded bg-light d-flex align-items-center justify-content-center"
                                    style="width:40px;height:40px;">
                                    <i class="bi bi-grid text-muted"></i>
                                </div>
                            @endif
                        </td>
                        <td class="fw-semibold">{{ $category->name }}</td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary">
                                {{ $category->sub_categories_count ?? $category->subCategories?->count() ?? 0 }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ $category->sort_order ?? 0 }}</td>
                        <td>
                            @if($category->is_active)
                                <span class="badge bg-success-subtle text-success">نشط</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary">معطل</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.forum.categories.edit', $category) }}"
                                class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.forum.categories.destroy', $category) }}" method="POST"
                                class="d-inline"
                                onsubmit="return confirm('هل أنت متأكد من حذف هذا القسم؟')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-grid fs-3 d-block mb-2"></i>
                            لا توجد أقسام
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($categories->hasPages())
        <div class="card-footer bg-transparent d-flex justify-content-end">
            {{ $categories->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
