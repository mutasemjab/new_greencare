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
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="alert alert-info small">
                        <i class="bi bi-info-circle me-1"></i>
                        سيتم توليد كود مريض خاص بالغرفة تلقائياً عند الإنشاء، ولا يمكن لأحد استخدامه إلا أعضاء هذه الغرفة.
                    </div>

                    <form action="{{ route('admin.sihati.rooms.store') }}" method="POST" enctype="multipart/form-data">
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

                        <hr class="my-4">
                        <h6 class="fw-bold mb-3">بيانات المريض (نموذج الاستقبال)</h6>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">العمر</label>
                                <input type="number" name="age" min="0" max="150" value="{{ old('age') }}" class="form-control @error('age') is-invalid @enderror">
                                @error('age') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">الجنس</label>
                                <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                    <option value="">—</option>
                                    <option value="male"   {{ old('gender') === 'male' ? 'selected' : '' }}>ذكر</option>
                                    <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>أنثى</option>
                                </select>
                                @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">الوزن (كغ)</label>
                                <input type="number" step="0.01" min="0" name="weight" value="{{ old('weight') }}" class="form-control @error('weight') is-invalid @enderror">
                                @error('weight') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">الحالة الاجتماعية</label>
                                <select name="marital_status" class="form-select @error('marital_status') is-invalid @enderror">
                                    <option value="">—</option>
                                    <option value="single"    {{ old('marital_status') === 'single' ? 'selected' : '' }}>أعزب</option>
                                    <option value="married"   {{ old('marital_status') === 'married' ? 'selected' : '' }}>متزوج</option>
                                    <option value="divorced"  {{ old('marital_status') === 'divorced' ? 'selected' : '' }}>مطلق</option>
                                    <option value="widowed"   {{ old('marital_status') === 'widowed' ? 'selected' : '' }}>أرمل</option>
                                </select>
                                @error('marital_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">الحالة الوظيفية</label>
                                <select name="functional_status" class="form-select @error('functional_status') is-invalid @enderror">
                                    <option value="">—</option>
                                    <option value="independent"          {{ old('functional_status') === 'independent' ? 'selected' : '' }}>مستقل</option>
                                    <option value="partially_dependent"  {{ old('functional_status') === 'partially_dependent' ? 'selected' : '' }}>معتمد جزئياً</option>
                                    <option value="fully_dependent"      {{ old('functional_status') === 'fully_dependent' ? 'selected' : '' }}>معتمد كلياً</option>
                                </select>
                                @error('functional_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">العرق</label>
                                <select name="race" class="form-select @error('race') is-invalid @enderror">
                                    <option value="">—</option>
                                    <option value="white" {{ old('race') === 'white' ? 'selected' : '' }}>أبيض</option>
                                    <option value="black" {{ old('race') === 'black' ? 'selected' : '' }}>أسود</option>
                                </select>
                                @error('race') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">المستوى التعليمي</label>
                                <input type="text" name="education_level" value="{{ old('education_level') }}" class="form-control @error('education_level') is-invalid @enderror">
                                @error('education_level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">فصيلة الدم</label>
                                <select name="blood_group" class="form-select @error('blood_group') is-invalid @enderror">
                                    <option value="">—</option>
                                    @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                                        <option value="{{ $bg }}" {{ old('blood_group') === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                    @endforeach
                                </select>
                                @error('blood_group') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="has_allergies" id="has_allergies" value="1" {{ old('has_allergies') ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="has_allergies">يوجد حساسية</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">تفاصيل الحساسية</label>
                            <textarea name="allergy_details" rows="2" class="form-control @error('allergy_details') is-invalid @enderror">{{ old('allergy_details') }}</textarea>
                            @error('allergy_details') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        @if($diagnoses->isNotEmpty())
                        <div class="mb-3">
                            <label class="form-label fw-semibold">التشخيصات</label>
                            <div class="row">
                                @foreach($diagnoses as $diagnosis)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="diagnosis_ids[]"
                                            value="{{ $diagnosis->id }}" id="diag{{ $diagnosis->id }}"
                                            {{ in_array($diagnosis->id, old('diagnosis_ids', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="diag{{ $diagnosis->id }}">{{ $diagnosis->name }}</label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($chronicDiseases->isNotEmpty())
                        <div class="mb-3">
                            <label class="form-label fw-semibold">الأمراض المزمنة</label>
                            <div class="row">
                                @foreach($chronicDiseases as $chronicDisease)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="chronic_disease_ids[]"
                                            value="{{ $chronicDisease->id }}" id="cd{{ $chronicDisease->id }}"
                                            {{ in_array($chronicDisease->id, old('chronic_disease_ids', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="cd{{ $chronicDisease->id }}">{{ $chronicDisease->name }}</label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div class="mb-4">
                            <label class="form-label fw-semibold">مرفقات</label>
                            <input type="file" name="attachments[]" multiple accept=".png,.jpg,.jpeg,.pdf" class="form-control @error('attachments') is-invalid @enderror">
                            @error('attachments') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
