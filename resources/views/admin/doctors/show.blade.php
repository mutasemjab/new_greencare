@extends('admin.layouts.app')
@section('title', 'تفاصيل الطبيب')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right"></i>
        </a>
        <h4 class="mb-0 fw-bold">{{ $doctor->name }}</h4>
        @if($doctor->is_active)
            <span class="badge bg-success-subtle text-success">نشط</span>
        @else
            <span class="badge bg-danger-subtle text-danger">معطل</span>
        @endif
    </div>

    @include('admin.includes.alerts.success')

    <div class="row g-4">

        {{-- Left Column: Doctor Info --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-person-badge me-2"></i>معلومات الطبيب
                </div>
                <div class="card-body text-center pb-3">
                    @if($doctor->photo)
                        <img src="{{ Storage::url($doctor->photo) }}"
                            class="rounded-circle mb-3 border"
                            style="width:110px;height:110px;object-fit:cover;">
                    @else
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3"
                            style="width:110px;height:110px;">
                            <i class="bi bi-person fs-1 text-muted"></i>
                        </div>
                    @endif
                    <h5 class="fw-bold mb-1">{{ $doctor->name }}</h5>
                    <p class="text-muted small mb-3">{{ $doctor->specialty }}</p>

                    {{-- Rating Stars --}}
                    <div class="mb-3">
                        <div class="text-warning fs-5">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($doctor->rating))
                                    <i class="bi bi-star-fill"></i>
                                @elseif($i - $doctor->rating < 1 && $i - $doctor->rating > 0)
                                    <i class="bi bi-star-half"></i>
                                @else
                                    <i class="bi bi-star"></i>
                                @endif
                            @endfor
                        </div>
                        <small class="text-muted">{{ $doctor->rating }} / 5</small>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted small">سعر الزيارة المنزلية</span>
                            <span class="fw-semibold">{{ number_format($doctor->home_visit_price, 2) }} د.أ</span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted small">سعر موعد العيادة</span>
                            <span class="fw-semibold">{{ number_format($doctor->appointment_price, 2) }} د.أ</span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted small">سنوات الخبرة</span>
                            <span class="fw-semibold">{{ $doctor->years_experience }} سنة</span>
                        </li>
                        @if($doctor->booking_phone)
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted small">رقم الحجز</span>
                            <span class="fw-semibold">{{ $doctor->booking_phone }}</span>
                        </li>
                        @endif
                    </ul>
                    @if($doctor->description)
                    <div class="mt-3">
                        <div class="text-muted small mb-1">الوصف</div>
                        <p class="small mb-0">{{ $doctor->description }}</p>
                    </div>
                    @endif
                    <div class="mt-3 d-flex gap-2">
                        <a href="{{ route('admin.doctors.edit', $doctor) }}" class="btn btn-primary btn-sm flex-fill">
                            <i class="bi bi-pencil me-1"></i> تعديل
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Bookings --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-calendar-check me-2"></i>حجوزات الطبيب
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>المريض</th>
                                    <th>نوع الزيارة</th>
                                    <th>التاريخ</th>
                                    <th>الوقت</th>
                                    <th>السعر</th>
                                    <th>الحالة</th>
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
                                    <td>
                                        @if($booking->visit_type === 'home_visit')
                                            <span class="badge bg-info-subtle text-info">زيارة منزلية</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">موعد عيادة</span>
                                        @endif
                                    </td>
                                    <td class="small">
                                        {{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('Y/m/d') : '—' }}
                                    </td>
                                    <td class="small">{{ $booking->booking_time ?? '—' }}</td>
                                    <td class="small">{{ number_format($booking->price, 2) }} د.أ</td>
                                    <td>
                                        <span class="badge {{ $st['class'] }}">{{ $st['label'] }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>
                                        لا توجد حجوزات لهذا الطبيب
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

    </div>
</div>
@endsection
