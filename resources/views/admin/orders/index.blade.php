@extends('admin.layouts.app')

@section('title', 'طلبات المتجر')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">طلبات المتجر</h4>
    </div>

    @include('admin.includes.alerts.success')

    {{-- Filter --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control"
                        placeholder="بحث باسم العميل أو رقم الطلب..."
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">-- جميع الحالات --</option>
                        <option value="pending"      @selected(request('status') === 'pending')>بانتظار التأكيد</option>
                        <option value="confirmed"    @selected(request('status') === 'confirmed')>مؤكد</option>
                        <option value="processing"   @selected(request('status') === 'processing')>قيد المعالجة</option>
                        <option value="shipped"      @selected(request('status') === 'shipped')>تم الشحن</option>
                        <option value="delivered"    @selected(request('status') === 'delivered')>تم التوصيل</option>
                        <option value="cancelled"    @selected(request('status') === 'cancelled')>ملغي</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search me-1"></i> بحث
                    </button>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>العميل</th>
                            <th>العنوان</th>
                            <th>المجموع</th>
                            <th>رسوم التوصيل</th>
                            <th>الإجمالي</th>
                            <th>الحالة</th>
                            <th>تاريخ الطلب</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        @php
                            $statusMap = [
                                'pending'    => ['label' => 'بانتظار التأكيد', 'class' => 'bg-warning-subtle text-warning'],
                                'confirmed'  => ['label' => 'مؤكد',            'class' => 'bg-info-subtle text-info'],
                                'processing' => ['label' => 'قيد المعالجة',    'class' => 'bg-primary-subtle text-primary'],
                                'shipped'    => ['label' => 'تم الشحن',        'class' => 'bg-primary-subtle text-primary'],
                                'delivered'  => ['label' => 'تم التوصيل',      'class' => 'bg-success-subtle text-success'],
                                'cancelled'  => ['label' => 'ملغي',            'class' => 'bg-danger-subtle text-danger'],
                            ];
                            $st = $statusMap[$order->status] ?? ['label' => $order->status, 'class' => 'bg-secondary-subtle text-secondary'];
                        @endphp
                        <tr>
                            <td class="text-muted small">{{ $order->id }}</td>
                            <td>
                                <div class="fw-semibold">{{ $order->user?->name ?? '—' }}</div>
                                <div class="small text-muted">{{ $order->user?->phone }}</div>
                            </td>
                            <td class="small">{{ Str::limit($order->address, 40) }}</td>
                            <td>{{ number_format($order->subtotal, 2) }} JD</td>
                            <td>{{ number_format($order->delivery_fee, 2) }} JD</td>
                            <td class="fw-bold">{{ number_format($order->total, 2) }} JD</td>
                            <td>
                                <span class="badge {{ $st['class'] }}">{{ $st['label'] }}</span>
                            </td>
                            <td class="small text-muted">{{ $order->created_at->format('Y/m/d H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.orders.show', $order) }}"
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
        @if($orders->hasPages())
        <div class="card-footer bg-transparent d-flex justify-content-center">
            {{ $orders->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
