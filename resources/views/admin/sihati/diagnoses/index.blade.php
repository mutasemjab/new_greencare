@extends('admin.layouts.app')
@section('title', 'التشخيصات')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">التشخيصات</h4>
        <a href="{{ route('admin.sihati.diagnoses.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> إضافة تشخيص
        </a>
    </div>

    @include('admin.includes.alerts.success')

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.sihati.diagnoses.index') }}" class="row g-3">
                <div class="col-md-7">
                    <input type="text" name="search" class="form-control"
                        placeholder="بحث بالاسم..." value="{{ request('search') }}">
                </div>
                <div class="col-md-5 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search me-1"></i> بحث
                    </button>
                    <a href="{{ route('admin.sihati.diagnoses.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($diagnoses as $diagnosis)
                        <tr>
                            <td class="text-muted small">{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $diagnosis->name }}</td>
                            <td>
                                @if($diagnosis->is_active)
                                    <span class="badge bg-success-subtle text-success">مفعّل</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">معطّل</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.sihati.diagnoses.edit', $diagnosis) }}"
                                        class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.sihati.diagnoses.destroy', $diagnosis) }}"
                                        method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                لا توجد تشخيصات بعد
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($diagnoses->hasPages())
        <div class="card-footer bg-transparent d-flex justify-content-center">
            {{ $diagnoses->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
