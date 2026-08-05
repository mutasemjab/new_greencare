@extends('admin.layouts.app')
@section('title', 'قوالب التقارير')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">قوالب التقارير</h4>
        <a href="{{ route('admin.sihati.templates.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> إنشاء قالب
        </a>
    </div>

    @include('admin.includes.alerts.success')

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-auto">
                    <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">كل الأنواع</option>
                        <option value="registration" {{ request('type') === 'registration' ? 'selected' : '' }}>قالب التسجيل</option>
                        <option value="nurse"        {{ request('type') === 'nurse'        ? 'selected' : '' }}>تقرير الممرض</option>
                        <option value="doctor"       {{ request('type') === 'doctor'       ? 'selected' : '' }}>تقرير الدكتور</option>
                    </select>
                </div>
                @if(request('type'))
                    <div class="col-auto">
                        <a href="{{ route('admin.sihati.templates.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-x"></i> مسح
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>اسم القالب</th>
                        <th>النوع</th>
                        <th>عدد الحقول</th>
                        <th>الحالة</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($templates as $template)
                    <tr>
                        <td class="text-muted small">{{ $loop->iteration }}</td>
                        <td>
                            <a href="{{ route('admin.sihati.templates.show', $template) }}" class="fw-semibold text-decoration-none">
                                {{ $template->name }}
                            </a>
                            @if($template->description)
                                <div class="small text-muted">{{ Str::limit($template->description, 60) }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $template->type_color }}-subtle text-{{ $template->type_color }}">
                                {{ $template->type_label }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $template->fields_count }} حقل</span>
                        </td>
                        <td>
                            @if($template->is_active)
                                <span class="badge bg-success-subtle text-success">نشط</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary">معطل</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.sihati.templates.show', $template) }}"
                                class="btn btn-sm btn-outline-info" title="إدارة الحقول">
                                <i class="bi bi-list-check"></i>
                            </a>
                            <a href="{{ route('admin.sihati.templates.edit', $template) }}"
                                class="btn btn-sm btn-outline-primary" title="تعديل">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.sihati.templates.destroy', $template) }}" method="POST"
                                class="d-inline"
                                onsubmit="return confirm('هل أنت متأكد من حذف هذا القالب؟ سيتأثر كل ما يرتبط به.')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="حذف">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-journal-medical fs-3 d-block mb-2"></i>
                            لا توجد قوالب
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($templates->hasPages())
        <div class="card-footer bg-transparent d-flex justify-content-end">
            {{ $templates->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
