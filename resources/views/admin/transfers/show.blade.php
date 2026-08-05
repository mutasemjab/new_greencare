@extends('admin.layouts.app')
@section('title', 'تفاصيل طلب النقل')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.transfers.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right"></i>
        </a>
        <h4 class="mb-0 fw-bold">طلب نقل المريض #{{ $transfer->id }}</h4>
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
        <span class="badge {{ $st['class'] }} fs-6">{{ $st['label'] }}</span>
    </div>

    @include('admin.includes.alerts.success')

    <div class="row g-4">

        <div class="col-lg-8">

            {{-- User Info --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-person-circle me-2"></i>معلومات المريض
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="text-muted small">الاسم</div>
                            <div class="fw-semibold">{{ $transfer->user?->name ?? '—' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small">الهاتف</div>
                            <div class="fw-semibold">{{ $transfer->user?->phone ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Transfer Details --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-geo-alt me-2"></i>تفاصيل الرحلة
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small mb-1">
                                    <i class="bi bi-geo me-1 text-success"></i>من المنطقة
                                </div>
                                <div class="fw-bold fs-6">{{ $transfer->fromZone?->name ?? '—' }}</div>
                                @if($transfer->from_location)
                                    <div class="text-muted small mt-1">{{ $transfer->from_location }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small mb-1">
                                    <i class="bi bi-geo-fill me-1 text-danger"></i>إلى المنطقة
                                </div>
                                <div class="fw-bold fs-6">{{ $transfer->toZone?->name ?? '—' }}</div>
                                @if($transfer->to_location)
                                    <div class="text-muted small mt-1">{{ $transfer->to_location }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="text-muted small">تاريخ الحجز</div>
                            <div class="fw-semibold">
                                {{ $transfer->booking_date ? \Carbon\Carbon::parse($transfer->booking_date)->format('Y/m/d') : '—' }}
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="text-muted small">الوقت</div>
                            <div class="fw-semibold">{{ $transfer->booking_time ?? '—' }}</div>
                        </div>
                        <div class="col-sm-4">
                            <div class="text-muted small">السعر</div>
                            <div class="fw-semibold">
                                {{ $transfer->price ? number_format($transfer->price, 2) . ' د.أ' : '—' }}
                            </div>
                        </div>
                        @if($transfer->case_description)
                        <div class="col-12">
                            <div class="text-muted small">وصف الحالة</div>
                            <div class="fw-semibold">{{ $transfer->case_description }}</div>
                        </div>
                        @endif
                        @if($transfer->notes)
                        <div class="col-12">
                            <div class="text-muted small">ملاحظات</div>
                            <div>{{ $transfer->notes }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        {{-- Status Update --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-arrow-repeat me-2"></i>تحديث الحالة
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.transfers.status', $transfer) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label class="form-label fw-semibold">الحالة</label>
                            <select name="status" class="form-select">
                                <option value="pending"     @selected($transfer->status === 'pending')>بانتظار التأكيد</option>
                                <option value="confirmed"   @selected($transfer->status === 'confirmed')>مؤكد</option>
                                <option value="in_progress" @selected($transfer->status === 'in_progress')>قيد التنفيذ</option>
                                <option value="completed"   @selected($transfer->status === 'completed')>مكتمل</option>
                                <option value="cancelled"   @selected($transfer->status === 'cancelled')>ملغي</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-lg me-1"></i> تحديث
                        </button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <div class="mb-2">
                        <span class="text-muted small">تاريخ الإنشاء:</span>
                        <div class="fw-semibold">{{ $transfer->created_at->format('Y/m/d H:i') }}</div>
                    </div>
                    <div>
                        <span class="text-muted small">آخر تحديث:</span>
                        <div class="fw-semibold">{{ $transfer->updated_at->format('Y/m/d H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
