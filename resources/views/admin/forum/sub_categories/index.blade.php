@extends('admin.layouts.app')
@section('title', 'الأقسام الفرعية للمنتدى')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">الأقسام الفرعية للمنتدى</h4>
        <a href="{{ route('admin.forum.sub-categories.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> إضافة قسم فرعي
        </a>
    </div>

    @include('admin.includes.alerts.success')

    {{-- Filter by Category --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.forum.sub-categories.index') }}" class="row g-3">
                <div class="col-md-5">
                    <select name="forum_category_id" class="form-select">
                        <option value="">-- جميع الأقسام الرئيسية --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                @selected(request('forum_category_id') == $cat->id)>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search me-1"></i> تصفية
                    </button>
                    <a href="{{ route('admin.forum.sub-categories.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>القسم الرئيسي</th>
                        <th>الاسم</th>
                        <th>الترتيب</th>
                        <th>الحالة</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subCategories as $subCategory)
                    <tr>
                        <td class="text-muted small">{{ $loop->iteration }}</td>
                        <td class="text-muted small">{{ $subCategory->forumCategory?->name ?? '—' }}</td>
                        <td class="fw-semibold">{{ $subCategory->name }}</td>
                        <td class="text-muted small">{{ $subCategory->sort_order ?? 0 }}</td>
                        <td>
                            @if($subCategory->is_active)
                                <span class="badge bg-success-subtle text-success">نشط</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary">معطل</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.forum.sub-categories.edit', $subCategory) }}"
                                class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.forum.sub-categories.destroy', $subCategory) }}" method="POST"
                                class="d-inline"
                                onsubmit="return confirm('هل أنت متأكد من حذف هذا القسم الفرعي؟')">
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
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-diagram-3 fs-3 d-block mb-2"></i>
                            لا توجد أقسام فرعية
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($subCategories->hasPages())
        <div class="card-footer bg-transparent d-flex justify-content-end">
            {{ $subCategories->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
