@extends('admin.layouts.app')

@section('title', 'تفاصيل الطلب #' . $order->id)

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right"></i>
        </a>
        <h4 class="mb-0 fw-bold">طلب رقم #{{ $order->id }}</h4>
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
        <span class="badge {{ $st['class'] }} fs-6">{{ $st['label'] }}</span>
    </div>

    @include('admin.includes.alerts.success')

    <div class="row g-4">

        {{-- Order Info --}}
        <div class="col-lg-8">

            {{-- Customer Info --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-person-circle me-2"></i>معلومات العميل
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="text-muted small">الاسم</div>
                            <div class="fw-semibold">{{ $order->user?->name ?? '—' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small">الهاتف</div>
                            <div class="fw-semibold">{{ $order->user?->phone ?? '—' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small">البريد الإلكتروني</div>
                            <div class="fw-semibold">{{ $order->user?->email ?? '—' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small">منطقة التوصيل</div>
                            <div class="fw-semibold">{{ $order->deliveryZone?->name ?? '—' }}</div>
                        </div>
                        <div class="col-12">
                            <div class="text-muted small">العنوان</div>
                            <div class="fw-semibold">{{ $order->address ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Order Items --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-cart me-2"></i>المنتجات
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>المنتج</th>
                                    <th class="text-center">الكمية</th>
                                    <th class="text-end">السعر</th>
                                    <th class="text-end">المجموع</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $item->product?->name ?? $item->product_name }}</div>
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">{{ number_format($item->price, 2) }} JD</td>
                                    <td class="text-end fw-semibold">{{ number_format($item->price * $item->quantity, 2) }} JD</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-semibold">المجموع الفرعي:</td>
                                    <td class="text-end fw-semibold">{{ number_format($order->subtotal, 2) }} JD</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end fw-semibold">رسوم التوصيل:</td>
                                    <td class="text-end fw-semibold">{{ number_format($order->delivery_fee, 2) }} JD</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold fs-5">الإجمالي:</td>
                                    <td class="text-end fw-bold fs-5 text-primary">{{ number_format($order->total, 2) }} JD</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            @if($order->notes)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-chat-text me-2"></i>ملاحظات
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $order->notes }}</p>
                </div>
            </div>
            @endif

        </div>

        {{-- Status Update --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-arrow-repeat me-2"></i>تحديث الحالة
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label class="form-label fw-semibold">الحالة</label>
                            <select name="status" class="form-select">
                                <option value="pending"    @selected($order->status === 'pending')>بانتظار التأكيد</option>
                                <option value="confirmed"  @selected($order->status === 'confirmed')>مؤكد</option>
                                <option value="processing" @selected($order->status === 'processing')>قيد المعالجة</option>
                                <option value="shipped"    @selected($order->status === 'shipped')>تم الشحن</option>
                                <option value="delivered"  @selected($order->status === 'delivered')>تم التوصيل</option>
                                <option value="cancelled"  @selected($order->status === 'cancelled')>ملغي</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-lg me-1"></i> تحديث
                        </button>
                    </form>
                </div>
            </div>

            {{-- Order Meta --}}
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-info-circle me-2"></i>معلومات الطلب
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <span class="text-muted small">تاريخ الطلب:</span>
                        <div class="fw-semibold">{{ $order->created_at->format('Y/m/d H:i') }}</div>
                    </div>
                    <div>
                        <span class="text-muted small">آخر تحديث:</span>
                        <div class="fw-semibold">{{ $order->updated_at->format('Y/m/d H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
