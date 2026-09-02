@extends('admin.layouts.app')
@section('title', 'لوحة التحكم')

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">الرئيسية</li>
                    </ol>
                </div>
                <h4 class="page-title">لوحة التحكم</h4>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="mdi mdi-check-circle ml-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════
         Row 1 — Pending requests (8 cards)
    ═══════════════════════════════════════════ --}}
    <div class="row">

        <div class="col-sm-6 col-xl-3">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-right">
                        <i class="mdi mdi-cart-outline widget-icon bg-warning-lighten text-warning"></i>
                    </div>
                    <h5 class="text-muted font-weight-normal mt-0 mb-1">طلبات المتجر المعلقة</h5>
                    <h3 class="mt-3 mb-3">{{ $pendingOrders }}</h3>
                    <p class="mb-0 text-muted">
                        <span class="badge badge-soft-warning mr-1">معلق</span>
                        <a href="{{ route('admin.orders.index') }}" class="text-muted font-11">عرض الكل &larr;</a>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-right">
                        <i class="mdi mdi-doctor widget-icon bg-info-lighten text-info"></i>
                    </div>
                    <h5 class="text-muted font-weight-normal mt-0 mb-1">حجوزات الأطباء المعلقة</h5>
                    <h3 class="mt-3 mb-3">{{ $pendingDoctors }}</h3>
                    <p class="mb-0 text-muted">
                        <span class="badge badge-soft-info mr-1">معلق</span>
                        <span class="font-11">يحتاج مراجعة</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-right">
                        <i class="mdi mdi-flask-outline widget-icon bg-danger-lighten text-danger"></i>
                    </div>
                    <h5 class="text-muted font-weight-normal mt-0 mb-1">طلبات المختبر المعلقة</h5>
                    <h3 class="mt-3 mb-3">{{ $pendingLab }}</h3>
                    <p class="mb-0 text-muted">
                        <span class="badge badge-soft-danger mr-1">معلق</span>
                        <span class="font-11">يحتاج مراجعة</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-right">
                        <i class="mdi mdi-radiobox-marked widget-icon bg-primary-lighten text-primary"></i>
                    </div>
                    <h5 class="text-muted font-weight-normal mt-0 mb-1">طلبات الأشعة المعلقة</h5>
                    <h3 class="mt-3 mb-3">{{ $pendingXray }}</h3>
                    <p class="mb-0 text-muted">
                        <span class="badge badge-soft-primary mr-1">معلق</span>
                        <span class="font-11">يحتاج مراجعة</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-right">
                        <i class="mdi mdi-needle widget-icon bg-success-lighten text-success"></i>
                    </div>
                    <h5 class="text-muted font-weight-normal mt-0 mb-1">طلبات التمريض المعلقة</h5>
                    <h3 class="mt-3 mb-3">{{ $pendingNursing }}</h3>
                    <p class="mb-0 text-muted">
                        <span class="badge badge-soft-success mr-1">معلق</span>
                        <span class="font-11">يحتاج مراجعة</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-right">
                        <i class="mdi mdi-hand-heart widget-icon bg-warning-lighten text-warning"></i>
                    </div>
                    <h5 class="text-muted font-weight-normal mt-0 mb-1">طلبات الرعاية المعلقة</h5>
                    <h3 class="mt-3 mb-3">{{ $pendingCare }}</h3>
                    <p class="mb-0 text-muted">
                        <span class="badge badge-soft-warning mr-1">معلق</span>
                        <span class="font-11">يحتاج مراجعة</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-right">
                        <i class="mdi mdi-shower widget-icon bg-info-lighten text-info"></i>
                    </div>
                    <h5 class="text-muted font-weight-normal mt-0 mb-1">طلبات الاستحمام المعلقة</h5>
                    <h3 class="mt-3 mb-3">{{ $pendingBathing }}</h3>
                    <p class="mb-0 text-muted">
                        <span class="badge badge-soft-info mr-1">معلق</span>
                        <span class="font-11">يحتاج مراجعة</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-right">
                        <i class="mdi mdi-ambulance widget-icon bg-danger-lighten text-danger"></i>
                    </div>
                    <h5 class="text-muted font-weight-normal mt-0 mb-1">طلبات النقل المعلقة</h5>
                    <h3 class="mt-3 mb-3">{{ $pendingTransfer }}</h3>
                    <p class="mb-0 text-muted">
                        <span class="badge badge-soft-danger mr-1">معلق</span>
                        <span class="font-11">يحتاج مراجعة</span>
                    </p>
                </div>
            </div>
        </div>

    </div>{{-- /row 1 --}}

    {{-- ═══════════════════════════════════════════
         Row 2 — User & Room totals (5 counters)
    ═══════════════════════════════════════════ --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">إجماليات النظام</h5>
                </div>
                <div class="card-body py-3">
                    <div class="row text-center">

                        <div class="col-6 col-md border-right">
                            <h3 class="font-weight-bold text-primary mb-0">{{ $totalRooms }}</h3>
                            <p class="text-muted small mb-0"><i class="mdi mdi-home-heart mr-1"></i> غرف مفعّلة</p>
                        </div>

                        <div class="col-6 col-md border-right">
                            <h3 class="font-weight-bold text-success mb-0">{{ $totalPatients }}</h3>
                            <p class="text-muted small mb-0"><i class="mdi mdi-account mr-1"></i> مرضى</p>
                        </div>

                        <div class="col-6 col-md border-right">
                            <h3 class="font-weight-bold text-info mb-0">{{ $totalDoctors }}</h3>
                            <p class="text-muted small mb-0"><i class="mdi mdi-doctor mr-1"></i> أطباء</p>
                        </div>

                        <div class="col-6 col-md border-right">
                            <h3 class="font-weight-bold text-warning mb-0">{{ $totalNurses }}</h3>
                            <p class="text-muted small mb-0"><i class="mdi mdi-needle mr-1"></i> ممرضون</p>
                        </div>

                        <div class="col-6 col-md">
                            <h3 class="font-weight-bold text-secondary mb-0">{{ $totalFamilies }}</h3>
                            <p class="text-muted small mb-0"><i class="mdi mdi-account-group mr-1"></i> أهل المرضى</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>{{-- /row 2 --}}

    {{-- ═══════════════════════════════════════════
         Row 3 — Recent tables
    ═══════════════════════════════════════════ --}}
    <div class="row">

        {{-- Recent Orders --}}
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">آخر طلبات المتجر</h5>
                    <a href="{{ route('admin.orders.index') }}" class="text-muted font-13">عرض الكل &larr;</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="thead-light">
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
                                    <td class="text-muted small">{{ $order->id }}</td>
                                    <td class="small">{{ $order->user->name ?? '—' }}</td>
                                    <td class="small">{{ number_format($order->total, 2) }}</td>
                                    <td>
                                        <span class="badge badge-soft-{{ $order->status_color }}">
                                            {{ $order->status_label }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3 small">لا توجد طلبات</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Lab Requests --}}
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">آخر طلبات المختبر</h5>
                    <span class="text-muted font-13">أحدث 6</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>العميل</th>
                                    <th>تاريخ الحجز</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentLab as $lab)
                                <tr>
                                    <td class="text-muted small">{{ $lab->id }}</td>
                                    <td class="small">{{ $lab->user->name ?? '—' }}</td>
                                    <td class="small">{{ $lab->booking_date?->format('Y-m-d') ?? '—' }}</td>
                                    <td>
                                        <span class="badge badge-soft-{{ $lab->status_color }}">
                                            {{ $lab->status_label }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3 small">لا توجد طلبات</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Doctor Bookings --}}
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">آخر حجوزات الأطباء</h5>
                    <span class="text-muted font-13">أحدث 6</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="thead-light">
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
                                    <td class="small">{{ $booking->user->name ?? '—' }}</td>
                                    <td class="small">{{ $booking->doctor->name ?? '—' }}</td>
                                    <td class="small">{{ $booking->visit_type_label }}</td>
                                    <td>
                                        <span class="badge badge-soft-{{ $booking->status_color }}">
                                            {{ $booking->status_label }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3 small">لا توجد حجوزات</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /row 3 --}}

</div>
@endsection
