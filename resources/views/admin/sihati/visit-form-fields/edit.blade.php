@extends('admin.layouts.app')
@section('title', 'تعديل حقل')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.sihati.visit-form-fields.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right"></i>
        </a>
        <h4 class="mb-0 fw-bold">تعديل حقل</h4>
    </div>

    <div class="card border-0 shadow-sm mx-auto" style="max-width:700px;">
        <div class="card-body p-4">
            <form action="{{ route('admin.sihati.visit-form-fields.update', $field) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="mb-3">
                    <label class="form-label fw-semibold">السؤال <span class="text-danger">*</span></label>
                    <input type="text" name="question" class="form-control @error('question') is-invalid @enderror"
                        value="{{ old('question', $field->question) }}" required>
                    @error('question') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">نوع الحقل <span class="text-danger">*</span></label>
                    <select name="field_type" id="fieldType" class="form-select @error('field_type') is-invalid @enderror" required>
                        <option value="text"      {{ old('field_type', $field->field_type) === 'text' ? 'selected' : '' }}>نص</option>
                        <option value="number"    {{ old('field_type', $field->field_type) === 'number' ? 'selected' : '' }}>رقم</option>
                        <option value="choice"    {{ old('field_type', $field->field_type) === 'choice' ? 'selected' : '' }}>اختيار واحد</option>
                        <option value="checklist" {{ old('field_type', $field->field_type) === 'checklist' ? 'selected' : '' }}>اختيار متعدد</option>
                    </select>
                    @error('field_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3" id="optionsGroup">
                    <label class="form-label fw-semibold">الخيارات (سطر لكل خيار)</label>
                    <textarea name="options" rows="4"
                        class="form-control @error('options') is-invalid @enderror"
                        placeholder="خيار 1&#10;خيار 2">{{ old('options', $field->options ? implode("\n", $field->options) : '') }}</textarea>
                    @error('options') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">الترتيب</label>
                    <input type="number" name="sort_order" min="0" class="form-control" value="{{ old('sort_order', $field->sort_order) }}">
                </div>

                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                            value="1" {{ old('is_active', $field->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_active">مفعّل</label>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i> حفظ التعديلات
                    </button>
                    <a href="{{ route('admin.sihati.visit-form-fields.index') }}" class="btn btn-outline-secondary px-4">إلغاء</a>
                </div>

            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
    (function () {
        var typeSelect = document.getElementById('fieldType');
        var optionsGroup = document.getElementById('optionsGroup');
        function toggle() {
            var needsOptions = ['choice', 'checklist'].includes(typeSelect.value);
            optionsGroup.style.display = needsOptions ? '' : 'none';
        }
        typeSelect.addEventListener('change', toggle);
        toggle();
    })();
</script>
@endpush
@endsection
