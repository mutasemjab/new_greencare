@extends('admin.layouts.app')
@section('title', 'الأطباء')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">الأطباء</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.doctor-bookings.index') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-calendar-check me-1"></i> الحجوزات
            </a>
            <a href="{{ route('admin.doctors.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> إضافة طبيب
            </a>
        </div>
    </div>

    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control form-control-sm"
                placeholder="بحث بالاسم أو التخصص..." value="{{ request('search') }}">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-sm btn-secondary"><i class="bi bi-search"></i> بحث</button>
            <a href="{{ route('admin.doctors.index') }}" class="btn btn-sm btn-outline-secondary">مسح</a>
        </div>
    </form>

    @include('admin.includes.alerts.success')

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>الطبيب</th>
                        <th>التخصص</th>
                        <th>الزيارة المنزلية</th>
                        <th>موعد العيادة</th>
                        <th>التقييم</th>
                        <th>الخبرة</th>
                        <th>الحالة</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($doctors as $doctor)
                    <tr>
                        <td class="text-muted small">{{ $doctor->id }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($doctor->photo)
                                    <img src="{{ Storage::disk('public')->url($doctor->photo) }}" class="rounded-circle"
                                        style="width:38px;height:38px;object-fit:cover;">
                                @else
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                                        style="width:38px;height:38px;">
                                        <i class="bi bi-person text-muted"></i>
                                    </div>
                                @endif
                                <span class="fw-semibold">{{ $doctor->name }}</span>
                            </div>
                        </td>
                        <td>{{ $doctor->specialty }}</td>
                        <td>{{ number_format($doctor->home_visit_price, 2) }} د.أ</td>
                        <td>{{ number_format($doctor->appointment_price, 2) }} د.أ</td>
                        <td>
                            <span class="text-warning">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star{{ $i <= $doctor->rating ? '-fill' : '' }}"></i>
                                @endfor
                            </span>
                            <small class="text-muted ms-1">{{ $doctor->rating }}</small>
                        </td>
                        <td>{{ $doctor->years_experience }} سنة</td>
                        <td>
                            @if($doctor->is_active)
                                <span class="badge bg-success">نشط</span>
                            @else
                                <span class="badge bg-danger">معطل</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.doctors.show', $doctor) }}" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.doctors.edit', $doctor) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.doctors.destroy', $doctor) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('حذف هذا الطبيب؟')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">لا يوجد أطباء</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($doctors->hasPages())
        <div class="card-footer bg-transparent d-flex justify-content-end">
            {{ $doctors->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
