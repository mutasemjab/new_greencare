@extends('admin.layouts.app')
@section('title', 'تعديل مستخدم')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-right"></i>
        </a>
        <h4 class="mb-0 fw-bold">تعديل: {{ $user->name }}</h4>
    </div>

    <div class="card shadow-sm border-0" style="max-width:700px">
        <div class="card-body p-4">
            @include('admin.includes.alerts.error')

            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf @method('PATCH')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">الاسم <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">الدور <span class="text-danger">*</span></label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="doctor"             {{ old('role', $user->role) === 'doctor'             ? 'selected' : '' }}>طبيب</option>
                            <option value="nurse"              {{ old('role', $user->role) === 'nurse'              ? 'selected' : '' }}>ممرض</option>
                            <option value="super_nurse"        {{ old('role', $user->role) === 'super_nurse'        ? 'selected' : '' }}>ممرض مسؤول</option>
                            <option value="university_manager" {{ old('role', $user->role) === 'university_manager' ? 'selected' : '' }}>مسؤول الجامعة</option>
                            <option value="patient"            {{ old('role', $user->role) === 'patient'            ? 'selected' : '' }}>مريض</option>
                            <option value="patient_family"     {{ old('role', $user->role) === 'patient_family'     ? 'selected' : '' }}>أهل المريض</option>
                        </select>
                        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">رقم الهاتف</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                            value="{{ old('phone', $user->phone) }}">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">البريد الإلكتروني</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $user->email) }}">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">تاريخ الميلاد</label>
                        <input type="date" name="date_of_birth" class="form-control"
                            value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">الجنس</label>
                        <select name="gender" class="form-select">
                            <option value="">غير محدد</option>
                            <option value="male"   {{ old('gender', $user->gender) === 'male'   ? 'selected' : '' }}>ذكر</option>
                            <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>أنثى</option>
                        </select>
                    </div>

                    @if($user->patient_code)
                    <div class="col-12">
                        <label class="form-label fw-semibold">كود المريض</label>
                        <div class="input-group">
                            <input type="text" class="form-control" value="{{ $user->patient_code }}" readonly>
                            <span class="input-group-text bg-light text-muted small">يُنشأ تلقائياً</span>
                        </div>
                    </div>
                    @endif

                    <div class="col-12">
                        <label class="form-label fw-semibold">FCM Token</label>
                        <input type="text" name="fcm_token" class="form-control"
                            value="{{ old('fcm_token', $user->fcm_token) }}">
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                id="is_active" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">مفعّل</label>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
