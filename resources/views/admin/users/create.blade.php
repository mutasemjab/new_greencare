@extends('admin.layouts.app')
@section('title', 'إضافة مستخدم')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-right"></i>
        </a>
        <h4 class="mb-0 fw-bold">إضافة مستخدم جديد</h4>
    </div>

    <div class="card shadow-sm border-0" style="max-width:700px">
        <div class="card-body p-4">
            @include('admin.includes.alerts.error')

            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">الاسم <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">الدور <span class="text-danger">*</span></label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="">اختر الدور</option>
                            <option value="doctor"             {{ old('role') === 'doctor'             ? 'selected' : '' }}>طبيب</option>
                            <option value="nurse"              {{ old('role') === 'nurse'              ? 'selected' : '' }}>ممرض</option>
                            <option value="head_nurse"         {{ old('role') === 'head_nurse'         ? 'selected' : '' }}>رئيس الممرضين</option>
                            <option value="university_manager" {{ old('role') === 'university_manager' ? 'selected' : '' }}>مسؤول الجامعة</option>
                            <option value="patient"            {{ old('role') === 'patient'            ? 'selected' : '' }}>مريض</option>
                            <option value="patient_family"     {{ old('role') === 'patient_family'     ? 'selected' : '' }}>أهل المريض</option>
                        </select>
                        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">رقم الهاتف</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                            value="{{ old('phone') }}">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">البريد الإلكتروني</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">تاريخ الميلاد</label>
                        <input type="date" name="date_of_birth" class="form-control"
                            value="{{ old('date_of_birth') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">الجنس</label>
                        <select name="gender" class="form-select">
                            <option value="">غير محدد</option>
                            <option value="male"   {{ old('gender') === 'male'   ? 'selected' : '' }}>ذكر</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>أنثى</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">FCM Token</label>
                        <input type="text" name="fcm_token" class="form-control" value="{{ old('fcm_token') }}">
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                id="is_active" {{ old('is_active', '1') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">مفعّل</label>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">حفظ</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
