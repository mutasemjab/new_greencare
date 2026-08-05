@extends('admin.layouts.app')
@section('title', 'قالب: ' . $template->name)

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.sihati.templates.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right"></i>
        </a>
        <div>
            <h4 class="mb-0 fw-bold">{{ $template->name }}</h4>
            <span class="badge bg-{{ $template->type_color }}-subtle text-{{ $template->type_color }} mt-1">
                {{ $template->type_label }}
            </span>
        </div>
        @if($template->is_active)
            <span class="badge bg-success-subtle text-success ms-auto">نشط</span>
        @else
            <span class="badge bg-secondary-subtle text-secondary ms-auto">معطل</span>
        @endif
        <a href="{{ route('admin.sihati.templates.edit', $template) }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil me-1"></i> تعديل القالب
        </a>
    </div>

    @include('admin.includes.alerts.success')

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-3">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($template->description)
    <div class="alert alert-light border mb-4">
        <i class="bi bi-info-circle me-2 text-muted"></i>{{ $template->description }}
    </div>
    @endif

    <div class="row g-4">

        {{-- Fields list --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-bold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-list-check me-2"></i>حقول القالب ({{ $template->fields->count() }})</span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:50px">#</th>
                                <th>السؤال</th>
                                <th>نوع الإجابة</th>
                                <th>إلزامي</th>
                                <th class="text-end">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($template->fields as $field)
                            <tr>
                                <td class="text-muted small">{{ $field->sort_order }}</td>
                                <td class="fw-semibold">{{ $field->question }}</td>
                                <td>
                                    @php
                                        $typeColors = ['text' => 'secondary', 'number' => 'primary', 'yes_no' => 'success', 'image' => 'warning'];
                                    @endphp
                                    <span class="badge bg-{{ $typeColors[$field->answer_type] ?? 'light' }}-subtle text-{{ $typeColors[$field->answer_type] ?? 'dark' }}">
                                        {{ $field->answer_type_label }}
                                    </span>
                                </td>
                                <td>
                                    @if($field->is_required)
                                        <i class="bi bi-check-circle-fill text-danger" title="إلزامي"></i>
                                    @else
                                        <i class="bi bi-dash text-muted"></i>
                                    @endif
                                </td>
                                <td class="text-end">
                                    {{-- Edit field inline via modal --}}
                                    <button class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editFieldModal{{ $field->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.sihati.fields.destroy', $field) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('حذف هذا الحقل؟ الإجابات القديمة ستبقى محفوظة.')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Edit Field Modal --}}
                            <div class="modal fade" id="editFieldModal{{ $field->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form action="{{ route('admin.sihati.fields.update', $field) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">تعديل الحقل</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">السؤال</label>
                                                    <input type="text" name="question" value="{{ $field->question }}"
                                                        class="form-control" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">نوع الإجابة</label>
                                                    <select name="answer_type" class="form-select">
                                                        <option value="text"   {{ $field->answer_type === 'text'   ? 'selected' : '' }}>نص</option>
                                                        <option value="number" {{ $field->answer_type === 'number' ? 'selected' : '' }}>رقم</option>
                                                        <option value="yes_no" {{ $field->answer_type === 'yes_no' ? 'selected' : '' }}>نعم / لا</option>
                                                        <option value="image"  {{ $field->answer_type === 'image'  ? 'selected' : '' }}>صورة</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">الترتيب</label>
                                                    <input type="number" name="sort_order" value="{{ $field->sort_order }}"
                                                        class="form-control" min="0">
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="is_required"
                                                        value="1" {{ $field->is_required ? 'checked' : '' }}
                                                        id="req{{ $field->id }}">
                                                    <label class="form-check-label" for="req{{ $field->id }}">حقل إلزامي</label>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                                                <button type="submit" class="btn btn-primary btn-sm">حفظ</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="bi bi-list-ul fs-3 d-block mb-2"></i>
                                    لا توجد حقول بعد — أضف أول حقل من النموذج الجانبي
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Add Field sidebar --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-plus-circle me-2"></i>إضافة حقل جديد
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.sihati.templates.fields.store', $template) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">السؤال <span class="text-danger">*</span></label>
                            <input type="text" name="question" value="{{ old('question') }}"
                                class="form-control form-control-sm @error('question') is-invalid @enderror"
                                placeholder="مثال: ضغط الدم؟">
                            @error('question') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">نوع الإجابة <span class="text-danger">*</span></label>
                            <select name="answer_type" class="form-select form-select-sm @error('answer_type') is-invalid @enderror">
                                <option value="text"   {{ old('answer_type') === 'text'   ? 'selected' : '' }}>نص</option>
                                <option value="number" {{ old('answer_type') === 'number' ? 'selected' : '' }}>رقم</option>
                                <option value="yes_no" {{ old('answer_type') === 'yes_no' ? 'selected' : '' }}>نعم / لا</option>
                                <option value="image"  {{ old('answer_type') === 'image'  ? 'selected' : '' }}>صورة</option>
                            </select>
                            @error('answer_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">الترتيب</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order') }}"
                                class="form-control form-control-sm" min="0"
                                placeholder="{{ $template->fields->count() + 1 }}">
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="is_required"
                                value="1" {{ old('is_required') ? 'checked' : '' }} id="isRequired">
                            <label class="form-check-label" for="isRequired">حقل إلزامي</label>
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-plus-lg me-1"></i> إضافة الحقل
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
