@extends('admin.layouts.app')
@section('title', 'نماذج الزيارة الطبية')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">نماذج الزيارة الطبية</h4>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.sihati.visit-forms.index') }}" class="row g-3">
                <div class="col-md-8">
                    <input type="text" name="search" class="form-control"
                        placeholder="بحث باسم المريض..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search me-1"></i> بحث
                    </button>
                    <a href="{{ route('admin.sihati.visit-forms.index') }}" class="btn btn-outline-secondary w-100">
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
                            <th>المريض</th>
                            <th>كود الزيارة</th>
                            <th>الممرض المسؤول</th>
                            <th>التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($visitForms as $visitForm)
                        <tr>
                            <td class="text-muted small">{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $visitForm->patient?->name ?? '—' }}</td>
                            <td class="small text-primary">{{ $visitForm->code ?? '—' }}</td>
                            <td>{{ $visitForm->submittedBy?->name ?? '—' }}</td>
                            <td class="small text-muted">{{ $visitForm->created_at->format('Y/m/d H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.sihati.visit-forms.show', $visitForm) }}"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                لا توجد نماذج بعد
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($visitForms->hasPages())
        <div class="card-footer bg-transparent d-flex justify-content-center">
            {{ $visitForms->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
