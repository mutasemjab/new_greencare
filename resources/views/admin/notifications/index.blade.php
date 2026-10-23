@extends('admin.layouts.app')
@section('title', 'الإشعارات')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">الإشعارات المُرسلة</h4>
        <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary">
            <i class="bi bi-send me-1"></i> إرسال إشعار
        </a>
    </div>

    @include('admin.includes.alerts.success')

    @if($broadcasts->isNotEmpty())
    @php $inProgress = $broadcasts->whereIn('status', ['pending', 'sending'])->isNotEmpty(); @endphp
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent fw-bold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-broadcast me-2"></i>الإرسال لجميع المستخدمين (في الخلفية)</span>
            @if($inProgress)
                <span class="small text-muted fw-normal">
                    <span class="spinner-border spinner-border-sm me-1"></span> يتم التحديث تلقائياً
                </span>
            @endif
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>العنوان</th>
                        <th>الحالة</th>
                        <th style="min-width: 200px">التقدّم</th>
                        <th>وصل</th>
                        <th>ما وصل</th>
                        <th>أرسلها</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($broadcasts as $broadcast)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $broadcast->title }}</div>
                            <div class="small text-muted">{{ \Illuminate\Support\Str::limit($broadcast->body, 50) }}</div>
                        </td>
                        <td>
                            <span class="badge bg-{{ $broadcast->status_color }}-subtle text-{{ $broadcast->status_color }}">
                                {{ $broadcast->status_label }}
                            </span>
                            @if($broadcast->status === 'failed' && $broadcast->error)
                                <div class="small text-danger mt-1">{{ \Illuminate\Support\Str::limit($broadcast->error, 60) }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="progress" style="height: 8px">
                                <div class="progress-bar bg-{{ $broadcast->status_color }}" style="width: {{ $broadcast->progress_percent }}%"></div>
                            </div>
                            <div class="small text-muted mt-1">
                                {{ number_format($broadcast->processed_count) }} / {{ number_format($broadcast->total_users) }}
                                ({{ $broadcast->progress_percent }}%)
                            </div>
                        </td>
                        <td class="text-success fw-semibold">{{ number_format($broadcast->sent_count) }}</td>
                        <td class="text-muted">{{ number_format($broadcast->failed_count) }}</td>
                        <td class="small text-muted">{{ $broadcast->sentBy?->name ?? '—' }}</td>
                        <td class="small text-muted">{{ $broadcast->created_at->format('Y/m/d H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if($inProgress)
    @push('scripts')
    <script>
        // Refresh progress while a background send is running.
        setTimeout(function () { window.location.reload(); }, 15000);
    </script>
    @endpush
    @endif
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.notifications.index') }}" class="row g-3">
                <div class="col-md-8">
                    <input type="text" name="search" class="form-control"
                        placeholder="بحث بالعنوان أو اسم المستخدم..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search me-1"></i> بحث
                    </button>
                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary w-100">
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
                            <th>العنوان</th>
                            <th>النص</th>
                            <th>المستلم</th>
                            <th>النوع</th>
                            <th>أرسلها</th>
                            <th>حالة الإرسال</th>
                            <th>مقروء؟</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifications as $notification)
                        <tr>
                            <td class="text-muted small">{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $notification->title }}</td>
                            <td class="small">{{ \Illuminate\Support\Str::limit($notification->body, 40) }}</td>
                            <td>
                                <div class="fw-semibold">{{ $notification->user?->name ?? '—' }}</div>
                                <div class="small text-muted">{{ $notification->user?->phone }}</div>
                            </td>
                            <td>
                                @if($notification->type === 'broadcast')
                                    <span class="badge bg-info-subtle text-info">للجميع</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">شخصي</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $notification->sentBy?->name ?? '—' }}</td>
                            <td>
                                @if($notification->fcm_sent)
                                    <span class="badge bg-success-subtle text-success">وصل الإشعار</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning">ما وصل (لا يوجد جهاز)</span>
                                @endif
                            </td>
                            <td>
                                @if($notification->is_read)
                                    <span class="badge bg-success-subtle text-success">مقروء</span>
                                @else
                                    <span class="badge bg-light text-muted">غير مقروء</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $notification->created_at->format('Y/m/d H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <i class="bi bi-bell-slash fs-3 d-block mb-2"></i>
                                ما في إشعارات مُرسلة من لوحة التحكم بعد
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($notifications->hasPages())
        <div class="card-footer bg-transparent d-flex justify-content-center">
            {{ $notifications->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
