@extends('admin.layouts.app')
@section('title', 'مفتاح API جديد')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.api-clients.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right"></i>
        </a>
        <h4 class="mb-0 fw-bold">مفتاح API جديد</h4>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('admin.api-clients.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">اسم المنصة/الجهة</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="مثال: منصة المخزون الخارجية">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="alert alert-info small mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            بعد الإنشاء رح يظهرلك المفتاح مرة وحدة بس — احفظه/انسخه فورًا وسلّمه للمنصة الخارجية.
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-key me-1"></i> إنشاء المفتاح
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
