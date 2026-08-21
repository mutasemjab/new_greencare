@extends('admin.layouts.app')
@section('title', 'نموذج الزيارة الطبية — الحقول')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">حقول نموذج الزيارة الطبية</h4>
        <a href="{{ route('admin.sihati.visit-form-fields.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> إضافة حقل
        </a>
    </div>

    @include('admin.includes.alerts.success')

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>السؤال</th>
                            <th>النوع</th>
                            <th>الترتيب</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fields as $field)
                        <tr>
                            <td class="text-muted small">{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $field->question }}</td>
                            <td>{{ $field->field_type_label }}</td>
                            <td>{{ $field->sort_order }}</td>
                            <td>
                                @if($field->is_active)
                                    <span class="badge bg-success-subtle text-success">مفعّل</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">معطّل</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.sihati.visit-form-fields.edit', $field) }}"
                                        class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.sihati.visit-form-fields.destroy', $field) }}"
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
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                لا توجد حقول بعد
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($fields->hasPages())
        <div class="card-footer bg-transparent d-flex justify-content-center">
            {{ $fields->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
