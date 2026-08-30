@extends('admin.layouts.app')
@section('title', 'تفاصيل الدواء')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.sihati.medications.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right"></i>
        </a>
        <h4 class="mb-0 fw-bold">{{ $medication->medication_name }}</h4>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-capsule me-2"></i>تفاصيل الدواء
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">المريض</span>
                            <span class="fw-semibold">{{ $medication->patient?->name ?? '—' }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">الهاتف</span>
                            <span>{{ $medication->patient?->phone ?? '—' }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">اسم الدواء</span>
                            <span class="fw-semibold">{{ $medication->medication_name }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">الجرعة</span>
                            <span>{{ $medication->dosage ?? '—' }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">طريقة الإعطاء</span>
                            <span>{{ $medication->route_label ?? '—' }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">التكرار</span>
                            <span>{{ $medication->frequency ?? '—' }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">تاريخ البدء</span>
                            <span>{{ $medication->start_date?->format('Y/m/d') ?? '—' }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">تاريخ الانتهاء</span>
                            <span>
                                @if($medication->end_date)
                                    @if($medication->end_date->isPast())
                                        <span class="text-danger">{{ $medication->end_date->format('Y/m/d') }} (منتهي)</span>
                                    @else
                                        {{ $medication->end_date->format('Y/m/d') }}
                                    @endif
                                @else
                                    —
                                @endif
                            </span>
                        </li>
                        @if($medication->notes)
                        <li class="py-2">
                            <div class="text-muted mb-1">ملاحظات</div>
                            <div>{{ $medication->notes }}</div>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
