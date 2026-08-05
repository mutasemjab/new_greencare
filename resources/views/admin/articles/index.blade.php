@extends('admin.layouts.app')
@section('title', 'المقالات')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">المقالات</h4>
        <a href="{{ route('admin.articles.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> إضافة مقال
        </a>
    </div>

    @include('admin.includes.alerts.success')

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>الصورة</th>
                        <th>العنوان</th>
                        <th>تاريخ النشر</th>
                        <th>الحالة</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($articles as $article)
                    <tr>
                        <td class="text-muted small">{{ $loop->iteration }}</td>
                        <td>
                            @if($article->image)
                                <img src="{{ Storage::url($article->image) }}"
                                    class="rounded border"
                                    style="width:60px;height:60px;object-fit:cover;">
                            @else
                                <div class="rounded bg-light d-flex align-items-center justify-content-center"
                                    style="width:60px;height:60px;">
                                    <i class="bi bi-image text-muted fs-5"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold">{{ Str::limit($article->title, 60) }}</div>
                        </td>
                        <td class="small text-muted">
                            {{ $article->published_at ? \Carbon\Carbon::parse($article->published_at)->format('Y/m/d H:i') : '—' }}
                        </td>
                        <td>
                            @if($article->is_active)
                                <span class="badge bg-success-subtle text-success">منشور</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary">مخفي</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.articles.edit', $article) }}"
                                class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.articles.destroy', $article) }}" method="POST"
                                class="d-inline"
                                onsubmit="return confirm('هل أنت متأكد من حذف هذا المقال؟')">
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
                            <i class="bi bi-newspaper fs-3 d-block mb-2"></i>
                            لا توجد مقالات
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($articles->hasPages())
        <div class="card-footer bg-transparent d-flex justify-content-end">
            {{ $articles->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
