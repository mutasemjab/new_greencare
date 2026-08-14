@extends('lab.layouts.app')

@section('title', 'طلبات المختبر')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">طلبات المختبر</h4>
    </div>

    @include('admin.includes.alerts.success')

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('lab.requests') }}" class="row g-3">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control"
                        placeholder="بحث باسم المستخدم أو كود المريض..."
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">-- جميع الحالات --</option>
                        <option value="pending"     @selected(request('status') === 'pending')>بانتظار التأكيد</option>
                        <option value="confirmed"   @selected(request('status') === 'confirmed')>مؤكد</option>
                        <option value="in_progress" @selected(request('status') === 'in_progress')>قيد التنفيذ</option>
                        <option value="completed"   @selected(request('status') === 'completed')>مكتمل</option>
                        <option value="cancelled"   @selected(request('status') === 'cancelled')>ملغي</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search me-1"></i> بحث
                    </button>
                    <a href="{{ route('lab.requests') }}" class="btn btn-outline-secondary w-100">
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
                            <th>المستخدم</th>
                            <th>كود المريض</th>
                            <th>عدد الفحوصات</th>
                            <th>الإجمالي</th>
                            <th>تاريخ الحجز</th>
                            <th>النتيجة</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
                        @php
                            $statusMap = [
                                'pending'     => ['label' => 'بانتظار التأكيد', 'class' => 'bg-warning-subtle text-warning'],
                                'confirmed'   => ['label' => 'مؤكد',            'class' => 'bg-info-subtle text-info'],
                                'in_progress' => ['label' => 'قيد التنفيذ',     'class' => 'bg-primary-subtle text-primary'],
                                'completed'   => ['label' => 'مكتمل',           'class' => 'bg-success-subtle text-success'],
                                'cancelled'   => ['label' => 'ملغي',            'class' => 'bg-danger-subtle text-danger'],
                            ];
                            $st = $statusMap[$req->status] ?? ['label' => $req->status, 'class' => 'bg-secondary-subtle text-secondary'];
                        @endphp
                        <tr>
                            <td class="text-muted small">{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-semibold">{{ $req->user?->name ?? '—' }}</div>
                                <div class="small text-muted">{{ $req->user?->phone }}</div>
                            </td>
                            <td class="small text-muted">{{ $req->patient_code ?? '—' }}</td>
                            <td>
                                <span class="badge bg-secondary rounded-pill">
                                    {{ $req->tests->count() }} فحص
                                </span>
                            </td>
                            <td class="fw-semibold">{{ number_format($req->total, 2) }} JD</td>
                            <td>{{ $req->booking_date ? \Carbon\Carbon::parse($req->booking_date)->format('Y/m/d') : '—' }}</td>
                            <td>
                                @if($req->result_file)
                                    <span class="badge bg-success-subtle text-success"><i class="bi bi-file-earmark-pdf"></i> مرفوعة</span>
                                @else
                                    <span class="badge bg-light text-muted">لم تُرفع بعد</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $st['class'] }}">{{ $st['label'] }}</span>
                            </td>
                            <td>
                                <a href="{{ route('lab.requests.show', $req) }}"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                لا توجد طلبات بعد
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($requests->hasPages())
        <div class="card-footer bg-transparent d-flex justify-content-center">
            {{ $requests->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
