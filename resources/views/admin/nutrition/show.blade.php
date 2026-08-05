@extends('admin.layouts.app')
@section('title', 'تفاصيل طلب التغذية')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.nutrition.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right"></i>
        </a>
        <h4 class="mb-0 fw-bold">تفاصيل طلب التغذية #{{ $nutrition->id }}</h4>
        @php
            $statusMap = [
                'pending'     => ['label' => 'بانتظار التأكيد', 'class' => 'bg-warning-subtle text-warning'],
                'confirmed'   => ['label' => 'مؤكد',            'class' => 'bg-info-subtle text-info'],
                'in_progress' => ['label' => 'قيد التنفيذ',     'class' => 'bg-primary-subtle text-primary'],
                'completed'   => ['label' => 'مكتمل',           'class' => 'bg-success-subtle text-success'],
                'cancelled'   => ['label' => 'ملغي',            'class' => 'bg-danger-subtle text-danger'],
            ];
            $st = $statusMap[$nutrition->status] ?? ['label' => $nutrition->status, 'class' => 'bg-secondary-subtle text-secondary'];
            $goalMap = [
                'lose_weight'    => 'إنقاص الوزن',
                'gain_weight'    => 'زيادة الوزن',
                'maintain'       => 'المحافظة على الوزن',
                'build_muscle'   => 'بناء العضلات',
                'improve_health' => 'تحسين الصحة العامة',
            ];
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
                        <div class="col-sm-4">
                            <div class="text-muted small">الاسم</div>
                            <div class="fw-semibold">{{ $nutrition->user?->name ?? '—' }}</div>
                        </div>
                        <div class="col-sm-4">
                            <div class="text-muted small">الهاتف</div>
                            <div class="fw-semibold">{{ $nutrition->user?->phone ?? '—' }}</div>
                        </div>
                        <div class="col-sm-4">
                            <div class="text-muted small">البريد الإلكتروني</div>
                            <div class="fw-semibold">{{ $nutrition->user?->email ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Request Details --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-clipboard2-heart me-2"></i>تفاصيل الطلب
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <div class="text-muted small">الطول</div>
                            <div class="fw-semibold">{{ $nutrition->height ? $nutrition->height . ' سم' : '—' }}</div>
                        </div>
                        <div class="col-sm-4">
                            <div class="text-muted small">الوزن</div>
                            <div class="fw-semibold">{{ $nutrition->weight ? $nutrition->weight . ' كغ' : '—' }}</div>
                        </div>
                        <div class="col-sm-4">
                            <div class="text-muted small">مؤشر كتلة الجسم (BMI)</div>
                            <div class="fw-semibold">{{ $nutrition->bmi ? number_format($nutrition->bmi, 1) : '—' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small">الهدف</div>
                            <div class="fw-semibold">{{ $goalMap[$nutrition->goal] ?? $nutrition->goal ?? '—' }}</div>
                        </div>
                        @if($nutrition->chronic_diseases)
                        <div class="col-12">
                            <div class="text-muted small">الأمراض المزمنة</div>
                            <div class="fw-semibold">{{ $nutrition->chronic_diseases }}</div>
                        </div>
                        @endif
                        @if($nutrition->food_allergies)
                        <div class="col-12">
                            <div class="text-muted small">حساسية الطعام</div>
                            <div class="fw-semibold">{{ $nutrition->food_allergies }}</div>
                        </div>
                        @endif
                        @if($nutrition->medicine_allergies)
                        <div class="col-12">
                            <div class="text-muted small">حساسية الأدوية</div>
                            <div class="fw-semibold">{{ $nutrition->medicine_allergies }}</div>
                        </div>
                        @endif
                        @if($nutrition->current_medications)
                        <div class="col-12">
                            <div class="text-muted small">الأدوية الحالية</div>
                            <div class="fw-semibold">{{ $nutrition->current_medications }}</div>
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
                    <form action="{{ route('admin.nutrition.status', $nutrition) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label class="form-label fw-semibold">الحالة</label>
                            <select name="status" class="form-select">
                                <option value="pending"     @selected($nutrition->status === 'pending')>بانتظار التأكيد</option>
                                <option value="confirmed"   @selected($nutrition->status === 'confirmed')>مؤكد</option>
                                <option value="in_progress" @selected($nutrition->status === 'in_progress')>قيد التنفيذ</option>
                                <option value="completed"   @selected($nutrition->status === 'completed')>مكتمل</option>
                                <option value="cancelled"   @selected($nutrition->status === 'cancelled')>ملغي</option>
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
                        <div class="fw-semibold">{{ $nutrition->created_at->format('Y/m/d H:i') }}</div>
                    </div>
                    <div>
                        <span class="text-muted small">آخر تحديث:</span>
                        <div class="fw-semibold">{{ $nutrition->updated_at->format('Y/m/d H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
