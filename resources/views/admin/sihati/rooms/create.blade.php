@extends('admin.layouts.app')
@section('title', 'إنشاء غرفة')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.sihati.rooms.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right"></i>
        </a>
        <h4 class="mb-0 fw-bold">إنشاء غرفة</h4>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="alert alert-info small">
                        <i class="bi bi-info-circle me-1"></i>
                        سيتم توليد كود مريض خاص بالغرفة تلقائياً عند الإنشاء، ولا يمكن لأحد استخدامه إلا أعضاء هذه الغرفة.
                    </div>

                    <form action="{{ route('admin.sihati.rooms.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">اسم الغرفة <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="مثال: غرفة 101">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">المريض <span class="text-danger">*</span></label>
                            <select name="patient_id" class="form-select @error('patient_id') is-invalid @enderror">
                                <option value="">اختر المريض</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->name }} @if($patient->phone) ({{ $patient->phone }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('patient_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">أنشأها (رئيس الممرضين) <span class="text-danger">*</span></label>
                            <select name="created_by" class="form-select @error('created_by') is-invalid @enderror">
                                <option value="">اختر رئيس الممرضين</option>
                                @foreach($headNurses as $headNurse)
                                    <option value="{{ $headNurse->id }}" {{ old('created_by') == $headNurse->id ? 'selected' : '' }}>
                                        {{ $headNurse->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('created_by') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">الوصف</label>
                            <textarea name="description" rows="3"
                                class="form-control @error('description') is-invalid @enderror"
                                placeholder="وصف اختياري للغرفة">{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">العنوان</label>
                            <input type="text" name="address" value="{{ old('address') }}"
                                class="form-control @error('address') is-invalid @enderror">
                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">نسبة الخصم %</label>
                            <input type="number" name="discount_value" min="0" max="100" step="0.01"
                                value="{{ old('discount_value', 0) }}"
                                class="form-control @error('discount_value') is-invalid @enderror">
                            @error('discount_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">قالب التسجيل</label>
                            <select name="registration_template_id" class="form-select @error('registration_template_id') is-invalid @enderror">
                                <option value="">— بدون قالب —</option>
                                @foreach($registrationTemplates as $template)
                                    <option value="{{ $template->id }}" {{ old('registration_template_id') == $template->id ? 'selected' : '' }}>
                                        {{ $template->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('registration_template_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-lg me-1"></i> إنشاء الغرفة
                            </button>
                            <a href="{{ route('admin.sihati.rooms.index') }}" class="btn btn-outline-secondary">
                                إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
