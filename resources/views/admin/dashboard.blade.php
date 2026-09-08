@extends('admin.layouts.app')
@section('title', 'لوحة التحكم')

@push('styles')
<style>
    .stat-card { border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.07); transition: transform .15s; }
    .stat-card:hover { transform: translateY(-2px); }
    .stat-icon { width: 52px; height: 52px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; }
    .pending-item { display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0; }
    .pending-item:last-child { border-bottom: none; }
    .pending-badge { min-width: 32px; height: 28px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; font-size: .82rem; font-weight: 600; }
    .role-badge { font-size: .72rem; padding: 3px 8px; border-radius: 20px; font-weight: 500; }
    .table th { font-weight: 600; font-size: .8rem; text-transform: uppercase; letter-spacing: .4px; color: #6c757d; }
    .table td { vertical-align: middle; font-size: .88rem; }
    .section-title { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: #adb5bd; margin-bottom: .5rem; }
</style>
@endpush

@section('content')
<div class="container-fluid py-3 px-4">

    {{-- ── Header ─────────────────────────────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0">لوحة التحكم</h4>
            <p class="text-muted small mb-0">{{ now()->format('l، d F Y') }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════
         ROW 1 — أهم 4 أرقام
    ══════════════════════════════════════════════ --}}
    <div class="row g-3 mb-4">

        <div class="col-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-cart3"></i>
                    </div>
                    <div>
                        <div class="text-muted small">طلبات المتجر المعلقة</div>
                        <div class="fs-3 fw-bold lh-1 mt-1">{{ $pendingOrders }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-house-heart"></i>
                    </div>
                    <div>
                        <div class="text-muted small">إجمالي الغرف</div>
                        <div class="fs-3 fw-bold lh-1 mt-1">{{ $totalRooms }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-people"></i>
                    </div>
                    <div>
                        <div class="text-muted small">إجمالي المرضى</div>
                        <div class="fs-3 fw-bold lh-1 mt-1">{{ $totalPatients }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-person-badge"></i>
                    </div>
                    <div>
                        <div class="text-muted small">الأطباء والممرضون</div>
                        <div class="fs-3 fw-bold lh-1 mt-1">{{ $totalDoctors + $totalNurses }}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════
         ROW 2 — الطلبات المعلقة + المستخدمون الجدد
    ══════════════════════════════════════════════ --}}
    <div class="row g-3 mb-4">

        {{-- الطلبات المعلقة --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius:12px;">
                <div class="card-body">
                    <p class="section-title mb-3">الطلبات المعلقة</p>

                    @php
                        $pendingItems = [
                            ['label' => 'طلبات المتجر',     'count' => $pendingOrders,   'color' => 'primary',  'icon' => 'bi-cart3'],
                            ['label' => 'حجوزات الأطباء',   'count' => $pendingDoctors,  'color' => 'info',     'icon' => 'bi-person-lines-fill'],
                            ['label' => 'فحوصات المختبر',   'count' => $pendingLab,      'color' => 'danger',   'icon' => 'bi-flask'],
                            ['label' => 'فحوصات الأشعة',    'count' => $pendingXray,     'color' => 'purple',   'icon' => 'bi-radioactive'],
                            ['label' => 'خدمات التمريض',    'count' => $pendingNursing,  'color' => 'success',  'icon' => 'bi-activity'],
                            ['label' => 'خدمات الرعاية',    'count' => $pendingCare,     'color' => 'warning',  'icon' => 'bi-heart-pulse'],
                            ['label' => 'خدمات الاستحمام',  'count' => $pendingBathing,  'color' => 'teal',     'icon' => 'bi-droplet'],
                            ['label' => 'نقل المرضى',       'count' => $pendingTransfer, 'color' => 'danger',   'icon' => 'bi-truck'],
                        ];
                    @endphp

                    @foreach($pendingItems as $item)
                    <div class="pending-item">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi {{ $item['icon'] }} text-{{ $item['color'] === 'purple' ? 'primary' : ($item['color'] === 'teal' ? 'info' : $item['color']) }} small"></i>
                            <span class="text-dark small">{{ $item['label'] }}</span>
                        </div>
                        <span class="pending-badge bg-{{ $item['color'] === 'purple' ? 'primary' : ($item['color'] === 'teal' ? 'info' : $item['color']) }} bg-opacity-10 text-{{ $item['color'] === 'purple' ? 'primary' : ($item['color'] === 'teal' ? 'info' : $item['color']) }}">
                            {{ $item['count'] }}
                        </span>
                    </div>
                    @endforeach

                </div>
            </div>
        </div>

        {{-- آخر المستخدمين المسجلين --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100" style="border-radius:12px;">
                <div class="card-body pb-0">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <p class="section-title mb-0">آخر المستخدمين المسجلين</p>
                        <a href="{{ route('admin.users.index') }}" class="text-primary small text-decoration-none">
                            عرض الكل <i class="bi bi-arrow-left"></i>
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>الاسم</th>
                                <th>رقم الهاتف</th>
                                <th>الدور</th>
                                <th>الحالة</th>
                                <th>تاريخ التسجيل</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentUsers as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center"
                                            style="width:32px;height:32px;font-size:.8rem;font-weight:600;color:#6c757d;">
                                            {{ mb_substr($user->name, 0, 1) }}
                                        </div>
                                        <span class="fw-medium">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="text-muted" dir="ltr">{{ $user->phone ?? '—' }}</td>
                                <td>
                                    @php
                                        $roleColors = [
                                            'patient'        => 'success',
                                            'doctor'         => 'primary',
                                            'nurse'          => 'info',
                                            'super_nurse'    => 'info',
                                            'patient_family' => 'secondary',
                                            'university_manager' => 'warning',
                                        ];
                                        $rc = $roleColors[$user->role] ?? 'secondary';
                                    @endphp
                                    <span class="role-badge bg-{{ $rc }} bg-opacity-10 text-{{ $rc }}">
                                        {{ $user->role_label }}
                                    </span>
                                </td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill">نشط</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill">موقوف</span>
                                    @endif
                                </td>
                                <td class="text-muted small">{{ $user->created_at->format('Y/m/d') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="bi bi-people fs-4 d-block mb-1 opacity-25"></i>
                                    لا يوجد مستخدمون بعد
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════
         ROW 3 — آخر الطلبات + حجوزات الأطباء
    ══════════════════════════════════════════════ --}}
    <div class="row g-3">

        {{-- آخر طلبات المتجر --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-body pb-0">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <p class="section-title mb-0">آخر طلبات المتجر</p>
                        <a href="{{ route('admin.orders.index') }}" class="text-primary small text-decoration-none">
                            عرض الكل <i class="bi bi-arrow-left"></i>
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>العميل</th>
                                <th>المبلغ</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr>
                                <td class="text-muted">{{ $order->id }}</td>
                                <td>{{ $order->user->name ?? '—' }}</td>
                                <td class="fw-medium">{{ number_format($order->total, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $order->status_color }} bg-opacity-10 text-{{ $order->status_color }} rounded-pill">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="bi bi-cart-x fs-4 d-block mb-1 opacity-25"></i>
                                    لا توجد طلبات
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- آخر حجوزات الأطباء --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-body pb-0">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <p class="section-title mb-0">آخر حجوزات الأطباء</p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>المريض</th>
                                <th>الطبيب</th>
                                <th>النوع</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentDoctors as $booking)
                            <tr>
                                <td>{{ $booking->user->name ?? '—' }}</td>
                                <td class="text-muted">{{ $booking->doctor->name ?? '—' }}</td>
                                <td>{{ $booking->visit_type_label }}</td>
                                <td>
                                    <span class="badge bg-{{ $booking->status_color }} bg-opacity-10 text-{{ $booking->status_color }} rounded-pill">
                                        {{ $booking->status_label }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="bi bi-calendar-x fs-4 d-block mb-1 opacity-25"></i>
                                    لا توجد حجوزات
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
