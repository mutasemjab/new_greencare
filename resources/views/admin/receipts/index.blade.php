@extends('admin.layouts.app')
@section('title', 'سندات القبض')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold"><i class="bi bi-receipt me-2"></i>سندات القبض</h4>
        @can('receipt-add')
        <a href="{{ route('admin.receipts.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> سند قبض جديد
        </a>
        @endcan
    </div>

    @include('admin.includes.alerts.success')
    @include('admin.includes.alerts.error')

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.receipts.index') }}" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-1">بحث</label>
                    <input type="text" name="search" class="form-control"
                        placeholder="اسم الشخص، البيان أو رقم السند..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">من تاريخ</label>
                    <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">إلى تاريخ</label>
                    <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('admin.receipts.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            <span class="fw-bold">{{ number_format($receipts->total()) }} سند</span>
            <span>المجموع: <strong class="text-success">{{ number_format($total, 3) }} د.أ</strong></span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>رقم السند</th>
                        <th>التاريخ</th>
                        <th>استلمنا من</th>
                        <th>المبلغ</th>
                        <th>طريقة الدفع</th>
                        <th>وذلك عن</th>
                        <th>أنشأه</th>
                        <th class="text-end"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receipts as $receipt)
                    <tr>
                        <td class="fw-semibold">{{ $receipt->number }}</td>
                        <td class="small">{{ $receipt->receipt_date->format('Y/m/d') }}</td>
                        <td>{{ $receipt->person_name }}</td>
                        <td class="fw-semibold text-success">{{ number_format($receipt->amount, 3) }} د.أ</td>
                        <td><span class="badge bg-light text-dark">{{ $receipt->payment_method_label }}</span></td>
                        <td class="small">{{ \Illuminate\Support\Str::limit($receipt->description, 40) ?: '—' }}</td>
                        <td class="small text-muted">{{ $receipt->createdBy?->name ?? '—' }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.receipts.show', $receipt) }}" class="btn btn-sm btn-outline-primary" title="عرض / طباعة">
                                <i class="bi bi-printer"></i>
                            </a>
                            @can('receipt-edit')
                            <a href="{{ route('admin.receipts.edit', $receipt) }}" class="btn btn-sm btn-outline-warning" title="تعديل">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endcan
                            @can('receipt-delete')
                            <form action="{{ route('admin.receipts.destroy', $receipt) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('حذف سند القبض رقم {{ $receipt->number }}؟')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="حذف">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-receipt fs-3 d-block mb-2"></i>
                            لا توجد سندات قبض
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($receipts->hasPages())
        <div class="card-footer bg-transparent d-flex justify-content-center">
            {{ $receipts->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

</div>
@endsection
