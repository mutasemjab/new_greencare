@extends('admin.layouts.app')
@section('title', 'تفاصيل المستخدم')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-right"></i>
        </a>
        <h4 class="mb-0 fw-bold">{{ $user->name }}</h4>
        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary ms-auto">
            <i class="bi bi-pencil me-1"></i> تعديل
        </a>
    </div>

    <div class="row g-4">
        {{-- Info Card --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-header fw-bold bg-transparent">معلومات المستخدم</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">الدور</span>
                        <span class="badge bg-secondary">{{ $user->role_label }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">الهاتف</span>
                        <span>{{ $user->phone ?? '—' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">البريد</span>
                        <span>{{ $user->email ?? '—' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">الجنس</span>
                        <span>{{ $user->gender_label }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">تاريخ الميلاد</span>
                        <span>{{ $user->date_of_birth?->format('Y-m-d') ?? '—' }}</span>
                    </li>
                    @if($user->patient_code)
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">كود المريض</span>
                        <code>{{ $user->patient_code }}</code>
                    </li>
                    @endif
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">الحالة</span>
                        @if($user->is_active)
                            <span class="badge bg-success">نشط</span>
                        @else
                            <span class="badge bg-danger">معطل</span>
                        @endif
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">تاريخ التسجيل</span>
                        <span>{{ $user->created_at->format('Y-m-d') }}</span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Activity Summary --}}
        <div class="col-md-8">
            <div class="row g-3">
                @php
                    $stats = [
                        ['label' => 'طلبات التمريض',  'count' => $user->nursingRequests->count(),  'icon' => 'person-badge',       'color' => 'primary'],
                        ['label' => 'طلبات الاستحمام', 'count' => $user->bathingRequests->count(),  'icon' => 'droplet',            'color' => 'info'],
                        ['label' => 'طلبات الرعاية',  'count' => $user->careRequests->count(),     'icon' => 'heart-pulse',        'color' => 'danger'],
                        ['label' => 'طلبات المختبر',  'count' => $user->labRequests->count(),      'icon' => 'eyedropper',         'color' => 'warning'],
                        ['label' => 'طلبات الأشعة',   'count' => $user->xrayRequests->count(),    'icon' => 'radioactive',        'color' => 'secondary'],
                        ['label' => 'طلبات المتجر',   'count' => $user->orders->count(),          'icon' => 'bag-check',          'color' => 'success'],
                    ];
                @endphp

                @foreach($stats as $stat)
                <div class="col-6 col-md-4">
                    <div class="card border-0 shadow-sm text-center p-3">
                        <i class="bi bi-{{ $stat['icon'] }} text-{{ $stat['color'] }} fs-3 mb-1"></i>
                        <div class="fs-4 fw-bold">{{ $stat['count'] }}</div>
                        <div class="text-muted small">{{ $stat['label'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Addresses --}}
            @if($user->addresses->isNotEmpty())
            <div class="card shadow-sm border-0 mt-3">
                <div class="card-header fw-bold bg-transparent">العناوين</div>
                <ul class="list-group list-group-flush">
                    @foreach($user->addresses as $address)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold">{{ $address->label ?? 'عنوان' }}</span>
                            @if($address->is_default)
                                <span class="badge bg-success">افتراضي</span>
                            @endif
                        </div>
                        <div class="text-muted small">{{ $address->address }}</div>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
