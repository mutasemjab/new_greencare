@extends('admin.layouts.app')

@section('title', 'تفاصيل طلب المختبر')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.lab.requests') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right"></i>
        </a>
        <h4 class="mb-0 fw-bold">طلب مختبر #{{ $request->id }}</h4>
        @php
            $statusMap = [
                'pending'     => ['label' => 'بانتظار التأكيد', 'class' => 'bg-warning-subtle text-warning'],
                'confirmed'   => ['label' => 'مؤكد',            'class' => 'bg-info-subtle text-info'],
                'in_progress' => ['label' => 'قيد التنفيذ',     'class' => 'bg-primary-subtle text-primary'],
                'completed'   => ['label' => 'مكتمل',           'class' => 'bg-success-subtle text-success'],
                'cancelled'   => ['label' => 'ملغي',            'class' => 'bg-danger-subtle text-danger'],
            ];
            $st = $statusMap[$request->status] ?? ['label' => $request->status, 'class' => 'bg-secondary-subtle text-secondary'];
        @endphp
        <span class="badge {{ $st['class'] }} fs-6">{{ $st['label'] }}</span>
    </div>

    @include('admin.includes.alerts.success')

    <div class="row g-4">
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-person-circle me-2"></i>معلومات العميل
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="text-muted small">المستخدم</div>
                            <div class="fw-semibold">{{ $request->user?->name ?? '—' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small">الهاتف</div>
                            <div class="fw-semibold">{{ $request->user?->phone ?? '—' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small">كود المريض</div>
                            <div class="fw-semibold">{{ $request->patient_code ?? '—' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small">تاريخ الحجز</div>
                            <div class="fw-semibold">
                                {{ $request->booking_date ? \Carbon\Carbon::parse($request->booking_date)->format('Y/m/d') : '—' }}
                            </div>
                        </div>
                        @if($request->address)
                        <div class="col-12">
                            <div class="text-muted small">العنوان</div>
                            <div class="fw-semibold">{{ $request->address }}</div>
                        </div>
                        @endif
                        @if($request->notes)
                        <div class="col-12">
                            <div class="text-muted small">ملاحظات</div>
                            <div>{{ $request->notes }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-clipboard2-pulse me-2"></i>الفحوصات المطلوبة
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>اسم الفحص</th>
                                    <th>الفئة</th>
                                    <th class="text-end">السعر</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($request->tests as $test)
                                <tr>
                                    <td class="fw-semibold">{{ $test->name }}</td>
                                    <td class="text-muted small">{{ $test->category?->name ?? '—' }}</td>
                                    <td class="text-end">{{ number_format($test->pivot->price ?? $test->price, 2) }} JD</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="2" class="fw-bold fs-5">الإجمالي</td>
                                    <td class="text-end fw-bold fs-5 text-primary">{{ number_format($request->total, 2) }} JD</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-arrow-repeat me-2"></i>تحديث الحالة
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.lab.requests.status', $request) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label class="form-label fw-semibold">الحالة</label>
                            <select name="status" class="form-select">
                                <option value="pending"     @selected($request->status === 'pending')>بانتظار التأكيد</option>
                                <option value="confirmed"   @selected($request->status === 'confirmed')>مؤكد</option>
                                <option value="in_progress" @selected($request->status === 'in_progress')>قيد التنفيذ</option>
                                <option value="completed"   @selected($request->status === 'completed')>مكتمل</option>
                                <option value="cancelled"   @selected($request->status === 'cancelled')>ملغي</option>
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
                        <div class="fw-semibold">{{ $request->created_at->format('Y/m/d H:i') }}</div>
                    </div>
                    <div>
                        <span class="text-muted small">آخر تحديث:</span>
                        <div class="fw-semibold">{{ $request->updated_at->format('Y/m/d H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
