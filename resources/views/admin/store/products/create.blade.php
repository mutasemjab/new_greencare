@extends('admin.layouts.app')

@section('title', 'إضافة منتج')

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.store.products.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right"></i>
        </a>
        <h4 class="mb-0 fw-bold">إضافة منتج جديد</h4>
    </div>

    @include('admin.includes.alerts.error')

    <div class="card border-0 shadow-sm mx-auto" style="max-width:700px;">
        <div class="card-body p-4">
            <form action="{{ route('admin.store.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Category --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">التصنيف <span class="text-danger">*</span></label>
                    <select name="store_category_id" class="form-select @error('store_category_id') is-invalid @enderror" required>
                        <option value="">-- اختر التصنيف --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(old('store_category_id') == $cat->id)>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('store_category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Name --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">الاسم <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">الوصف</label>
                    <textarea name="description" rows="4"
                        class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Price --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">السعر (JD) <span class="text-danger">*</span></label>
                        <input type="number" name="price" step="0.01" min="0"
                            class="form-control @error('price') is-invalid @enderror"
                            value="{{ old('price') }}" required>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">سعر التخفيض (JD)</label>
                        <input type="number" name="sale_price" step="0.01" min="0"
                            class="form-control @error('sale_price') is-invalid @enderror"
                            value="{{ old('sale_price') }}">
                        @error('sale_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Stock --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">المخزون</label>
                    <input type="number" name="stock" min="0"
                        class="form-control @error('stock') is-invalid @enderror"
                        value="{{ old('stock', 0) }}">
                    @error('stock')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Images --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">الصور</label>
                    <input type="file" name="images[]" multiple
                        class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror"
                        accept="image/*">
                    <div class="form-text">يمكنك اختيار أكثر من صورة في آنٍ واحد.</div>
                    @error('images')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    @error('images.*')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Is Active --}}
                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                            value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_active">مفعّل</label>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i> حفظ
                    </button>
                    <a href="{{ route('admin.store.products.index') }}" class="btn btn-outline-secondary px-4">
                        إلغاء
                    </a>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection
