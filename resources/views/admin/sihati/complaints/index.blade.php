@extends('admin.layouts.app')
@section('title', 'الشكاوى')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">الشكاوى</h4>
    </div>

    @include('admin.includes.alerts.success')

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.sihati.complaints.index') }}" class="row g-3">
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">-- جميع الحالات --</option>
                        <option value="pending"  @selected(request('status') === 'pending')>قيد المراجعة</option>
                        <option value="reviewed" @selected(request('status') === 'reviewed')>تمت المراجعة</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control"
                        placeholder="بحث باسم المريض..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search me-1"></i> بحث
                    </button>
                    <a href="{{ route('admin.sihati.complaints.index') }}" class="btn btn-outline-secondary w-100">
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
                            <th>الغرفة</th>
                            <th>المريض</th>
                            <th>مقدّم الشكوى</th>
                            <th>نص الشكوى</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($complaints as $complaint)
                        <tr>
                            <td class="text-muted small">{{ $loop->iteration }}</td>
                            <td>{{ $complaint->room?->name ?? '—' }}</td>
                            <td>{{ $complaint->patient?->name ?? '—' }}</td>
                            <td>{{ $complaint->submittedBy?->name ?? '—' }}</td>
                            <td class="small">{{ \Illuminate\Support\Str::limit($complaint->complaint_text, 60) }}</td>
                            <td>
                                @if($complaint->status === 'reviewed')
                                    <span class="badge bg-success-subtle text-success">تمت المراجعة</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning">قيد المراجعة</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $complaint->created_at->format('Y/m/d H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.sihati.complaints.show', $complaint) }}"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                لا توجد شكاوى بعد
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($complaints->hasPages())
        <div class="card-footer bg-transparent d-flex justify-content-center">
            {{ $complaints->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
