@extends('admin.layouts.app')
@section('title', 'حجوزات الأطباء')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">حجوزات الأطباء</h4>
        <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right me-1"></i> الأطباء
        </a>
    </div>

    @include('admin.includes.alerts.success')

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.doctor-bookings.index') }}" class="row g-3">
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">-- جميع الحالات --</option>
                        <option value="pending"     @selected(request('status') === 'pending')>بانتظار التأكيد</option>
                        <option value="confirmed"   @selected(request('status') === 'confirmed')>مؤكد</option>
                        <option value="in_progress" @selected(request('status') === 'in_progress')>قيد التنفيذ</option>
                        <option value="completed"   @selected(request('status') === 'completed')>مكتمل</option>
                        <option value="cancelled"   @selected(request('status') === 'cancelled')>ملغي</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="visit_type" class="form-select">
                        <option value="">-- جميع أنواع الزيارة --</option>
                        <option value="home_visit"   @selected(request('visit_type') === 'home_visit')>زيارة منزلية</option>
                        <option value="appointment"  @selected(request('visit_type') === 'appointment')>موعد عيادة</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control"
                        placeholder="بحث باسم المريض أو الطبيب..."
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('admin.doctor-bookings.index') }}" class="btn btn-outline-secondary w-100">
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
                            <th>الطبيب</th>
                            <th>نوع الزيارة</th>
                            <th>السعر</th>
                            <th>تاريخ الحجز</th>
                            <th>الوقت</th>
                            <th>الحالة</th>
                            <th>تحديث الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                        @php
                            $statusMap = [
                                'pending'     => ['label' => 'بانتظار التأكيد', 'class' => 'bg-warning-subtle text-warning'],
                                'confirmed'   => ['label' => 'مؤكد',            'class' => 'bg-info-subtle text-info'],
                                'in_progress' => ['label' => 'قيد التنفيذ',     'class' => 'bg-primary-subtle text-primary'],
                                'completed'   => ['label' => 'مكتمل',           'class' => 'bg-success-subtle text-success'],
                                'cancelled'   => ['label' => 'ملغي',            'class' => 'bg-danger-subtle text-danger'],
                            ];
                            $st = $statusMap[$booking->status] ?? ['label' => $booking->status, 'class' => 'bg-secondary-subtle text-secondary'];
                        @endphp
                        <tr>
                            <td class="text-muted small">{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-semibold">{{ $booking->user?->name ?? '—' }}</div>
                                <div class="small text-muted">{{ $booking->user?->phone }}</div>
                            </td>
                            <td class="fw-semibold">{{ $booking->doctor?->name ?? '—' }}</td>
                            <td>
                                @if($booking->visit_type === 'home_visit')
                                    <span class="badge bg-info-subtle text-info">زيارة منزلية</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">موعد عيادة</span>
                                @endif
                            </td>
                            <td class="small">{{ number_format($booking->price, 2) }} د.أ</td>
                            <td class="small">
                                {{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('Y/m/d') : '—' }}
                            </td>
                            <td class="small">{{ $booking->booking_time ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $st['class'] }}">{{ $st['label'] }}</span>
                            </td>
                            <td>
                                <form action="{{ route('admin.doctor-bookings.status', $booking) }}" method="POST"
                                    class="d-flex gap-1 align-items-center">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="form-select form-select-sm" style="min-width:130px">
                                        <option value="pending"     @selected($booking->status === 'pending')>بانتظار التأكيد</option>
                                        <option value="confirmed"   @selected($booking->status === 'confirmed')>مؤكد</option>
                                        <option value="in_progress" @selected($booking->status === 'in_progress')>قيد التنفيذ</option>
                                        <option value="completed"   @selected($booking->status === 'completed')>مكتمل</option>
                                        <option value="cancelled"   @selected($booking->status === 'cancelled')>ملغي</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>
                                لا توجد حجوزات
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($bookings->hasPages())
        <div class="card-footer bg-transparent d-flex justify-content-center">
            {{ $bookings->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
