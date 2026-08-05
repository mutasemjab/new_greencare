@extends('admin.layouts.app')
@section('title', 'طلبات نقل المرضى')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">طلبات نقل المرضى</h4>
    </div>

    @include('admin.includes.alerts.success')

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.transfers.index') }}" class="row g-3">
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
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control"
                        placeholder="بحث باسم المريض أو المنطقة..."
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search me-1"></i> بحث
                    </button>
                    <a href="{{ route('admin.transfers.index') }}" class="btn btn-outline-secondary w-100">
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
                            <th>المريض</th>
                            <th>من المنطقة</th>
                            <th>إلى المنطقة</th>
                            <th>السعر</th>
                            <th>تاريخ الحجز</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transfers as $transfer)
                        @php
                            $statusMap = [
                                'pending'     => ['label' => 'بانتظار التأكيد', 'class' => 'bg-warning-subtle text-warning'],
                                'confirmed'   => ['label' => 'مؤكد',            'class' => 'bg-info-subtle text-info'],
                                'in_progress' => ['label' => 'قيد التنفيذ',     'class' => 'bg-primary-subtle text-primary'],
                                'completed'   => ['label' => 'مكتمل',           'class' => 'bg-success-subtle text-success'],
                                'cancelled'   => ['label' => 'ملغي',            'class' => 'bg-danger-subtle text-danger'],
                            ];
                            $st = $statusMap[$transfer->status] ?? ['label' => $transfer->status, 'class' => 'bg-secondary-subtle text-secondary'];
                        @endphp
                        <tr>
                            <td class="text-muted small">{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-semibold">{{ $transfer->user?->name ?? '—' }}</div>
                                <div class="small text-muted">{{ $transfer->user?->phone }}</div>
                            </td>
                            <td>{{ $transfer->fromZone?->name ?? '—' }}</td>
                            <td>{{ $transfer->toZone?->name ?? '—' }}</td>
                            <td class="small">{{ $transfer->price ? number_format($transfer->price, 2) . ' د.أ' : '—' }}</td>
                            <td class="small">
                                {{ $transfer->booking_date ? \Carbon\Carbon::parse($transfer->booking_date)->format('Y/m/d') : '—' }}
                            </td>
                            <td>
                                <span class="badge {{ $st['class'] }}">{{ $st['label'] }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.transfers.show', $transfer) }}"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                لا توجد طلبات نقل
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($transfers->hasPages())
        <div class="card-footer bg-transparent d-flex justify-content-center">
            {{ $transfers->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
