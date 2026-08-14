@extends('admin.layouts.app')
@section('title', 'غرف صحتي')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold"><i class="bi bi-house-heart me-2"></i>غرف صحتي</h4>
        <a href="{{ route('admin.sihati.rooms.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> إنشاء غرفة
        </a>
    </div>

    @include('admin.includes.alerts.success')

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="form-control form-control-sm"
                        placeholder="بحث باسم الغرفة أو اسم المريض...">
                </div>
                <div class="col-auto">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">كل الغرف</option>
                        <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>نشطة</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>معطلة</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary">بحث</button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('admin.sihati.rooms.index') }}" class="btn btn-sm btn-outline-secondary">مسح</a>
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
                        <th>اسم الغرفة</th>
                        <th>كود المريض</th>
                        <th>المريض</th>
                        <th>أُنشئت بواسطة</th>
                        <th>الأعضاء</th>
                        <th>الخصم</th>
                        <th>الحالة</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rooms as $room)
                    <tr>
                        <td class="text-muted small">{{ $loop->iteration }}</td>
                        <td>
                            <a href="{{ route('admin.sihati.rooms.show', $room) }}" class="fw-semibold text-decoration-none">
                                {{ $room->name }}
                            </a>
                            @if($room->description)
                                <div class="small text-muted">{{ Str::limit($room->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="fw-semibold text-primary">{{ $room->patient_code ?? '—' }}</td>
                        <td>
                            <div class="fw-semibold">{{ $room->patient?->name ?? '—' }}</div>
                            <div class="small text-muted">{{ $room->patient?->phone }}</div>
                        </td>
                        <td class="small text-muted">{{ $room->createdBy?->name ?? '—' }}</td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                {{ $room->members_count }} عضو
                            </span>
                        </td>
                        <td>
                            @if($room->discount_value > 0)
                                <span class="badge bg-warning-subtle text-warning">
                                    {{ $room->discount_value }}%
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($room->is_active)
                                <span class="badge bg-success-subtle text-success">نشطة</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger">معطلة</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.sihati.rooms.show', $room) }}"
                                class="btn btn-sm btn-outline-primary" title="عرض التفاصيل">
                                <i class="bi bi-eye"></i>
                            </a>
                            <form action="{{ route('admin.sihati.rooms.toggle', $room) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-sm {{ $room->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                    title="{{ $room->is_active ? 'تعطيل' : 'تفعيل' }}">
                                    <i class="bi bi-{{ $room->is_active ? 'pause-circle' : 'play-circle' }}"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">
                            <i class="bi bi-house-heart fs-3 d-block mb-2"></i>
                            لا توجد غرف
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($rooms->hasPages())
        <div class="card-footer bg-transparent d-flex justify-content-end">
            {{ $rooms->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
