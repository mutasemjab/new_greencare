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
