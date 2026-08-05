@extends('admin.layouts.app')
@section('title', 'أدوية المرضى')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">
            <i class="bi bi-capsule me-2"></i>أدوية المرضى
        </h4>
        <span class="badge bg-info-subtle text-info border">خارج الغرف — يضيفها المريض</span>
    </div>

    {{-- Search --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="form-control form-control-sm"
                        placeholder="بحث باسم الدواء أو اسم المريض...">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary">بحث</button>
                    @if(request('search'))
                        <a href="{{ route('admin.sihati.medications.index') }}" class="btn btn-sm btn-outline-secondary">مسح</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>المريض</th>
                        <th>الدواء</th>
                        <th>الجرعة</th>
                        <th>التكرار</th>
                        <th>تاريخ البدء</th>
                        <th>تاريخ الانتهاء</th>
                        <th class="text-end"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($medications as $med)
                    <tr>
                        <td class="text-muted small">{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-semibold">{{ $med->patient?->name ?? '—' }}</div>
                            <div class="small text-muted">{{ $med->patient?->phone }}</div>
                        </td>
                        <td class="fw-semibold">{{ $med->medication_name }}</td>
                        <td>{{ $med->dosage ?? '—' }}</td>
                        <td>{{ $med->frequency ?? '—' }}</td>
                        <td class="small">{{ $med->start_date?->format('Y/m/d') ?? '—' }}</td>
                        <td class="small">
                            @if($med->end_date)
                                @if($med->end_date->isPast())
                                    <span class="text-danger">{{ $med->end_date->format('Y/m/d') }}</span>
                                @else
                                    {{ $med->end_date->format('Y/m/d') }}
                                @endif
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.sihati.medications.show', $med) }}"
                                class="btn btn-sm btn-outline-secondary" title="تفاصيل">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-capsule fs-3 d-block mb-2"></i>
                            لا توجد أدوية مُسجَّلة
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($medications->hasPages())
        <div class="card-footer bg-transparent d-flex justify-content-end">
            {{ $medications->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
