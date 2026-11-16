@extends('admin.layouts.app')
@section('title', $receipt->exists ? 'تعديل سند قبض' : 'سند قبض جديد')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.receipts.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right"></i>
        </a>
        <h4 class="mb-0 fw-bold">
            {{ $receipt->exists ? 'تعديل سند القبض رقم ' . $receipt->number : 'سند قبض جديد' }}
        </h4>
    </div>

    <div class="card border-0 shadow-sm mx-auto" style="max-width:700px;">
        <div class="card-body p-4">
            <form action="{{ $receipt->exists ? route('admin.receipts.update', $receipt) : route('admin.receipts.store') }}" method="POST">
                @csrf
                @if($receipt->exists) @method('PUT') @endif

                <div class="mb-3">
                    <label class="form-label fw-semibold">استلمنا من السيد/ة <span class="text-danger">*</span></label>
                    <input type="text" name="person_name" class="form-control @error('person_name') is-invalid @enderror"
                        value="{{ old('person_name', $receipt->person_name) }}" required autofocus>
                    @error('person_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">المبلغ (دينار أردني) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="amount" step="0.001" min="0.001"
                                class="form-control @error('amount') is-invalid @enderror"
                                value="{{ old('amount', $receipt->amount) }}" required>
                            <span class="input-group-text">د.أ</span>
                            @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">التاريخ <span class="text-danger">*</span></label>
                        <input type="date" name="receipt_date" class="form-control @error('receipt_date') is-invalid @enderror"
                            value="{{ old('receipt_date', $receipt->receipt_date?->format('Y-m-d')) }}" required>
                        @error('receipt_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">طريقة الدفع <span class="text-danger">*</span></label>
                    <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror">
                        @foreach(\App\Models\ReceiptVoucher::PAYMENT_METHODS as $value => $label)
                            <option value="{{ $value }}" {{ old('payment_method', $receipt->payment_method) === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('payment_method') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">وذلك عن <span class="text-muted small fw-normal">(اختياري)</span></label>
                    <input type="text" name="description" class="form-control @error('description') is-invalid @enderror"
                        value="{{ old('description', $receipt->description) }}" placeholder="مثال: رسوم زيارة تمريضية لشهر تشرين الثاني">
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-check-lg me-1"></i>
                    {{ $receipt->exists ? 'حفظ التعديلات' : 'حفظ السند' }}
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
