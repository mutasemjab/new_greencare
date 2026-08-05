@extends('admin.layouts.app')
@section('title', 'تفاصيل المنشور')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.forum.posts.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right"></i>
        </a>
        <h4 class="mb-0 fw-bold">تفاصيل المنشور</h4>
    </div>

    @include('admin.includes.alerts.success')

    {{-- Post Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            {{-- Type + Meta --}}
            <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                @if($post->type === 'experience')
                    <span class="badge bg-success-subtle text-success fs-6">تجربة أم</span>
                @else
                    <span class="badge bg-primary-subtle text-primary fs-6">سؤال وجواب</span>
                @endif
                @if($post->is_pinned)
                    <span class="badge bg-warning-subtle text-warning">
                        <i class="bi bi-pin-fill me-1"></i> مثبت
                    </span>
                @endif
                @if(!$post->is_active)
                    <span class="badge bg-secondary-subtle text-secondary">مخفي</span>
                @endif
            </div>

            {{-- Title --}}
            <h5 class="fw-bold mb-2">{{ $post->title }}</h5>

            {{-- Content --}}
            <div class="mb-3" style="white-space:pre-line">{{ $post->content }}</div>

            {{-- Image --}}
            @if($post->image)
            <div class="mb-3">
                <img src="{{ Storage::url($post->image) }}"
                    class="rounded border img-fluid"
                    style="max-height:300px;object-fit:contain;">
            </div>
            @endif

            {{-- Author & Path --}}
            <div class="d-flex align-items-center gap-3 flex-wrap border-top pt-3 text-muted small">
                <div>
                    <i class="bi bi-person me-1"></i>
                    <strong>{{ $post->user?->name ?? '—' }}</strong>
                </div>
                <div>
                    <i class="bi bi-calendar me-1"></i>
                    {{ $post->created_at->format('Y/m/d H:i') }}
                </div>
                <div>
                    <i class="bi bi-folder2-open me-1"></i>
                    {{ $post->subCategory?->forumCategory?->name ?? '—' }}
                    @if($post->subCategory)
                        &rsaquo; {{ $post->subCategory->name }}
                    @endif
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="card-footer bg-transparent d-flex gap-2 flex-wrap">
            {{-- Toggle Active --}}
            <form action="{{ route('admin.forum.posts.toggle-status', $post) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit"
                    class="btn btn-sm {{ $post->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }}">
                    <i class="bi bi-{{ $post->is_active ? 'eye-slash' : 'eye' }} me-1"></i>
                    {{ $post->is_active ? 'إخفاء' : 'إظهار' }}
                </button>
            </form>

            {{-- Toggle Pin --}}
            <form action="{{ route('admin.forum.posts.toggle-pin', $post) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit"
                    class="btn btn-sm {{ $post->is_pinned ? 'btn-outline-secondary' : 'btn-outline-warning' }}">
                    <i class="bi bi-pin{{ $post->is_pinned ? '-angle' : '' }} me-1"></i>
                    {{ $post->is_pinned ? 'إلغاء التثبيت' : 'تثبيت' }}
                </button>
            </form>

            {{-- Delete --}}
            <form action="{{ route('admin.forum.posts.destroy', $post) }}" method="POST"
                onsubmit="return confirm('هل أنت متأكد من حذف هذا المنشور نهائياً؟')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-trash me-1"></i> حذف المنشور
                </button>
            </form>
        </div>
    </div>

    {{-- Replies Section --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent fw-bold">
            <i class="bi bi-chat-dots me-2"></i>الردود ({{ $post->replies_count ?? $post->replies?->count() ?? 0 }})
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>الكاتب</th>
                            <th>الرد</th>
                            <th>التاريخ</th>
                            <th>الحالة</th>
                            <th class="text-end">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($post->replies ?? [] as $reply)
                        <tr>
                            <td class="text-muted small">{{ $loop->iteration }}</td>
                            <td class="fw-semibold small">{{ $reply->user?->name ?? '—' }}</td>
                            <td class="small">{{ Str::limit($reply->content, 100) }}</td>
                            <td class="small text-muted">{{ $reply->created_at->format('Y/m/d H:i') }}</td>
                            <td>
                                @if($reply->is_active)
                                    <span class="badge bg-success-subtle text-success">ظاهر</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">مخفي</span>
                                @endif
                            </td>
                            <td class="text-end">
                                {{-- Toggle Reply Status --}}
                                <form action="{{ route('admin.forum.replies.toggle-status', $reply) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="btn btn-sm {{ $reply->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }}"
                                        title="{{ $reply->is_active ? 'إخفاء' : 'إظهار' }}">
                                        <i class="bi bi-{{ $reply->is_active ? 'eye-slash' : 'eye' }}"></i>
                                    </button>
                                </form>
                                {{-- Delete Reply --}}
                                <form action="{{ route('admin.forum.replies.destroy', $reply) }}" method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('حذف هذا الرد نهائياً؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-chat fs-3 d-block mb-2"></i>
                                لا توجد ردود
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
