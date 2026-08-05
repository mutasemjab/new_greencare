@extends('admin.layouts.app')
@section('title', 'منشورات المنتدى')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">منشورات المنتدى</h4>
    </div>

    @include('admin.includes.alerts.success')

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.forum.posts.index') }}" class="row g-3">
                <div class="col-md-2">
                    <select name="type" class="form-select">
                        <option value="">-- النوع --</option>
                        <option value="experience" @selected(request('type') === 'experience')>تجربة أم</option>
                        <option value="question"   @selected(request('type') === 'question')>سؤال وجواب</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="sub_category_id" class="form-select">
                        <option value="">-- جميع الأقسام --</option>
                        @foreach($subCategories as $sub)
                            <option value="{{ $sub->id }}"
                                @selected(request('sub_category_id') == $sub->id)>
                                {{ $sub->forumCategory?->name }} &rsaquo; {{ $sub->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control"
                        placeholder="بحث بالعنوان أو اسم الكاتب..."
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search me-1"></i> بحث
                    </button>
                    <a href="{{ route('admin.forum.posts.index') }}" class="btn btn-outline-secondary w-100">
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
                            <th>الكاتب</th>
                            <th>القسم</th>
                            <th>النوع</th>
                            <th>العنوان</th>
                            <th>الردود</th>
                            <th>مثبت</th>
                            <th>الحالة</th>
                            <th>تاريخ النشر</th>
                            <th class="text-end">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($posts as $post)
                        <tr>
                            <td class="text-muted small">{{ $loop->iteration }}</td>
                            <td class="fw-semibold small">{{ $post->user?->name ?? '—' }}</td>
                            <td class="small text-muted">{{ $post->subCategory?->name ?? '—' }}</td>
                            <td>
                                @if($post->type === 'experience')
                                    <span class="badge bg-success-subtle text-success">تجربة أم</span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary">سؤال وجواب</span>
                                @endif
                            </td>
                            <td>
                                <div style="max-width:200px" class="text-truncate fw-semibold small">
                                    {{ $post->title }}
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">
                                    {{ $post->replies_count ?? 0 }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($post->is_pinned)
                                    <i class="bi bi-pin-fill text-warning fs-5" title="مثبت"></i>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($post->is_active)
                                    <span class="badge bg-success-subtle text-success">ظاهر</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">مخفي</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $post->created_at->format('Y/m/d') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.forum.posts.show', $post) }}"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form action="{{ route('admin.forum.posts.destroy', $post) }}" method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('هل أنت متأكد من حذف هذا المنشور؟')">
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
                            <td colspan="10" class="text-center text-muted py-5">
                                <i class="bi bi-chat-square-dots fs-3 d-block mb-2"></i>
                                لا توجد منشورات
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($posts->hasPages())
        <div class="card-footer bg-transparent d-flex justify-content-center">
            {{ $posts->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
