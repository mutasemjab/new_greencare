@extends('admin.layouts.app')
@section('title', 'المستخدمون')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">المستخدمون</h4>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> إضافة مستخدم
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control form-control-sm"
                placeholder="بحث بالاسم أو الهاتف أو البريد أو الكود..."
                value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="role" class="form-select form-select-sm">
                <option value="">كل الأدوار</option>
                <option value="doctor"             {{ request('role') === 'doctor'             ? 'selected' : '' }}>طبيب</option>
                <option value="nurse"              {{ request('role') === 'nurse'              ? 'selected' : '' }}>ممرض</option>
                <option value="super_nurse"        {{ request('role') === 'super_nurse'        ? 'selected' : '' }}>ممرض مسؤول</option>
                <option value="university_manager" {{ request('role') === 'university_manager' ? 'selected' : '' }}>مسؤول الجامعة</option>
                <option value="patient"            {{ request('role') === 'patient'            ? 'selected' : '' }}>مريض</option>
                <option value="patient_family"     {{ request('role') === 'patient_family'     ? 'selected' : '' }}>أهل المريض</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-sm btn-secondary">
                <i class="bi bi-search"></i> بحث
            </button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">مسح</a>
        </div>
    </form>

    @include('admin.includes.alerts.success')

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>الهاتف</th>
                        <th>البريد</th>
                        <th>الدور</th>
                        <th>كود المريض</th>
                        <th>الجنس</th>
                        <th>الحالة</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="text-muted small">{{ $user->id }}</td>
                        <td class="fw-semibold">{{ $user->name }}</td>
                        <td>{{ $user->phone ?? '—' }}</td>
                        <td>{{ $user->email ?? '—' }}</td>
                        <td><span class="badge bg-secondary">{{ $user->role_label }}</span></td>
                        <td>
                            @if($user->patient_code)
                                <code>{{ $user->patient_code }}</code>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $user->gender_label }}</td>
                        <td>
                            @if($user->is_active)
                                <span class="badge bg-success">نشط</span>
                            @else
                                <span class="badge bg-danger">معطل</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">لا يوجد مستخدمون</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="card-footer bg-transparent d-flex justify-content-end">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
