@extends('admin.layouts.app')

@section('title', 'توليد بطاقات استحمام')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.bathing.cards') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right"></i>
        </a>
        <h4 class="mb-0 fw-bold">توليد بطاقات استحمام</h4>
    </div>

    @include('admin.includes.alerts.error')

    <div class="card border-0 shadow-sm mx-auto" style="max-width:700px;">
        <div class="card-body p-4">
            <form action="{{ route('admin.bathing.cards.generate.store') }}" method="POST">
                @csrf

                {{-- Group Name --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">اسم المجموعة <span class="text-danger">*</span></label>
                    <input type="text" name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Unit Price --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">سعر البطاقة الواحدة <span class="text-danger">*</span></label>
                    <input type="number" name="unit_price" min="0" step="0.01"
                        class="form-control @error('unit_price') is-invalid @enderror"
                        value="{{ old('unit_price') }}" required>
                    @error('unit_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Quantity --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">عدد البطاقات <span class="text-danger">*</span></label>
                    <input type="number" name="quantity" min="1" max="500"
                        class="form-control @error('quantity') is-invalid @enderror"
                        value="{{ old('quantity', 10) }}" required>
                    <div class="form-text">الحد الأقصى 500 بطاقة في المرة الواحدة.</div>
                    @error('quantity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Max Uses --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">الحد الأقصى للاستخدام لكل بطاقة <span class="text-danger">*</span></label>
                    <input type="number" name="max_uses" min="1"
                        class="form-control @error('max_uses') is-invalid @enderror"
                        value="{{ old('max_uses', 5) }}" required>
                    @error('max_uses')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Point of Sale --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">نقطة البيع</label>
                    <select name="sold_at_point_id" class="form-select @error('sold_at_point_id') is-invalid @enderror">
                        <option value="">— غير محددة —</option>
                        @foreach($points as $point)
                            <option value="{{ $point->id }}" @selected(old('sold_at_point_id') == $point->id)>
                                {{ $point->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('sold_at_point_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-plus-square me-1"></i> توليد البطاقات
                    </button>
                    <a href="{{ route('admin.bathing.cards') }}" class="btn btn-outline-secondary px-4">إلغاء</a>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection
